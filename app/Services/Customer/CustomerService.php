<?php

declare(strict_types=1);

namespace App\Services\Customer;

use App\Enums\ConnectionType;
use App\Enums\CustomerStatus;
use App\Models\Customer;
use App\Models\InternetPackage;
use App\Models\PPPoEAccount;
use App\Models\Router;
use App\Models\Subscription;
use App\Services\Mikrotik\MikrotikServiceFactory;
use App\Traits\HasTransaction;
use App\Traits\Loggable;

class CustomerService
{
    use HasTransaction;
    use Loggable;

    public function __construct(
        private readonly MikrotikServiceFactory $mikrotikFactory,
    ) {
    }

    protected function logPrefix(): string
    {
        return 'CustomerService';
    }

    /**
     * Register a new customer with full setup.
     */
    public function registerCustomer(array $data, ?array $addressData = null): Customer
    {
        return $this->transactional(function () use ($data, $addressData) {
            // Create customer
            $customer = Customer::create([
                'full_name'         => $data['full_name'],
                'email'             => $data['email'] ?? null,
                'phone'             => $data['phone'],
                'phone_alt'         => $data['phone_alt'] ?? null,
                'id_number'         => $data['id_number'] ?? null,
                'registration_date' => $data['registration_date'] ?? now(),
                'status'            => CustomerStatus::ACTIVE,
                'notes'             => $data['notes'] ?? null,
                'created_by'        => auth()->id(),
            ]);

            // Create primary address
            if ($addressData) {
                $customer->addresses()->create(array_merge($addressData, [
                    'is_primary' => true,
                ]));
            }

            $this->logInfo('Customer registered', [
                'code' => $customer->customer_code,
                'name' => $customer->full_name,
            ]);

            return $customer;
        });
    }

    /**
     * Activate customer subscription with package and router.
     */
    public function activateSubscription(
        Customer $customer,
        InternetPackage $package,
        Router $router,
        array $data,
    ): Subscription {
        return $this->transactional(function () use ($customer, $package, $router, $data) {
            $connectionType = ConnectionType::from($data['connection_type'] ?? 'pppoe');

            // Create subscription
            $subscription = Subscription::create([
                'customer_id'     => $customer->id,
                'package_id'      => $package->id,
                'router_id'       => $router->id,
                'connection_type' => $connectionType,
                'status'          => CustomerStatus::ACTIVE,
                'start_date'      => $data['start_date'] ?? now(),
                'billing_date'    => $data['billing_date'] ?? (int) now()->day,
                'price_override'  => $data['price_override'] ?? null,
                'auto_renewal'    => $data['auto_renewal'] ?? true,
                'notes'           => $data['notes'] ?? null,
            ]);

            // Create PPPoE or Hotspot account based on connection type
            if ($connectionType === ConnectionType::PPPOE) {
                $this->createPPPoEAccount($customer, $subscription, $router, $package, $data);
            } elseif ($connectionType === ConnectionType::HOTSPOT) {
                $this->createHotspotAccount($customer, $subscription, $router, $package, $data);
            }

            $this->logInfo('Subscription activated', [
                'customer' => $customer->customer_code,
                'package'  => $package->name,
                'router'   => $router->name,
            ]);

            return $subscription;
        });
    }

    /**
     * Suspend customer and all services.
     */
    public function suspendCustomer(Customer $customer, string $reason): void
    {
        $this->transactional(function () use ($customer, $reason) {
            $subscription = $customer->activeSubscription;
            if (!$subscription) {
                throw new \RuntimeException('No active subscription found');
            }

            $subscription->suspend();
            $customer->update(['status' => CustomerStatus::SUSPENDED]);

            // Suspend PPPoE on Mikrotik
            if ($account = $subscription->pppoeAccount) {
                $pppoeService = $this->mikrotikFactory->pppoe($account->router);
                $pppoeService->disableSecret($account->username);
                $pppoeService->disconnectUser($account->username);
                $account->update(['disabled' => true]);
            }

            // Suspend Hotspot on Mikrotik
            if ($account = $subscription->hotspotAccount) {
                $hotspotService = $this->mikrotikFactory->hotspot($account->router);
                $hotspotService->disableUser($account->username);
                $hotspotService->disconnectUser($account->username);
                $account->update(['disabled' => true]);
            }

            $this->logInfo('Customer suspended', [
                'customer' => $customer->customer_code,
                'reason'   => $reason,
            ]);
        });
    }

    /**
     * Unsuspend customer and restore services.
     */
    public function unsuspendCustomer(Customer $customer): void
    {
        $this->transactional(function () use ($customer) {
            $subscription = $customer->activeSubscription;
            if (!$subscription) {
                throw new \RuntimeException('No active subscription found');
            }

            $subscription->unsuspend();
            $customer->update(['status' => CustomerStatus::ACTIVE]);

            // Unsuspend PPPoE on Mikrotik
            if ($account = $subscription->pppoeAccount) {
                $pppoeService = $this->mikrotikFactory->pppoe($account->router);
                $pppoeService->enableSecret($account->username);
                $account->update(['disabled' => false]);
            }

            // Unsuspend Hotspot on Mikrotik
            if ($account = $subscription->hotspotAccount) {
                $hotspotService = $this->mikrotikFactory->hotspot($account->router);
                $hotspotService->enableUser($account->username);
                $account->update(['disabled' => false]);
            }

            $this->logInfo('Customer unsuspended', [
                'customer' => $customer->customer_code,
            ]);
        });
    }

    /**
     * Change customer's PPPoE password.
     */
    public function changePPPoEPassword(Subscription $subscription, string $newPassword): void
    {
        $account = $subscription->pppoeAccount;
        if (!$account) {
            throw new \RuntimeException('No PPPoE account found');
        }

        $pppoeService = $this->mikrotikFactory->pppoe($account->router);
        $pppoeService->changePassword($account->username, $newPassword);
        $account->update(['password' => $newPassword]);

        $this->logInfo('PPPoE password changed', [
            'customer' => $subscription->customer->customer_code,
            'username' => $account->username,
        ]);
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    private function createPPPoEAccount(
        Customer $customer,
        Subscription $subscription,
        Router $router,
        InternetPackage $package,
        array $data,
    ): PPPoEAccount {
        $username = $data['pppoe_username'] ?? 'CUS' . $customer->id . '-' . substr(md5(uniqid()), 0, 4);
        $password = $data['pppoe_password'] ?? substr(md5(uniqid() . time()), 0, 10);

        $pppoeAccount = PPPoEAccount::create([
            'customer_id'     => $customer->id,
            'subscription_id' => $subscription->id,
            'router_id'       => $router->id,
            'username'        => $username,
            'password'        => $password,
            'profile'         => $package->mikrotik_profile ?? 'default',
            'service'         => 'pppoe',
            'comment'         => "Customer: {$customer->full_name} | Package: {$package->name}",
        ]);

        // Push to Mikrotik
        try {
            $pppoeService = $this->mikrotikFactory->pppoe($router);
            $pppoeService->addSecret($pppoeAccount);
        } catch (\Exception $e) {
            $this->logError('Failed to create PPPoE on router', [
                'error'   => $e->getMessage(),
                'account' => $username,
            ]);
            // Don't throw - account is created locally, can be synced later
        }

        return $pppoeAccount;
    }

    private function createHotspotAccount(
        Customer $customer,
        Subscription $subscription,
        Router $router,
        InternetPackage $package,
        array $data,
    ): void {
        $username = $data['hotspot_username'] ?? 'CUS' . $customer->id;
        $password = $data['hotspot_password'] ?? substr(md5(uniqid() . time()), 0, 8);

        $hotspotAccount = \App\Models\HotspotAccount::create([
            'customer_id'     => $customer->id,
            'subscription_id' => $subscription->id,
            'router_id'       => $router->id,
            'username'        => $username,
            'password'        => $password,
            'profile'         => $package->mikrotik_profile ?? 'default',
            'comment'         => "Customer: {$customer->full_name}",
        ]);

        try {
            $hotspotService = $this->mikrotikFactory->hotspot($router);
            $hotspotService->addUser([
                'name'     => $username,
                'password' => $password,
                'profile'  => $package->mikrotik_profile ?? 'default',
                'comment'  => "Customer: {$customer->full_name}",
            ]);
        } catch (\Exception $e) {
            $this->logError('Failed to create Hotspot user on router', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
