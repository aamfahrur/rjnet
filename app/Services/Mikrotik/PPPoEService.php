<?php

declare(strict_types=1);

namespace App\Services\Mikrotik;

use App\Models\PPPoEAccount;
use App\Models\Router;

/**
 * PPPoE Service - Manages PPPoE secrets on Mikrotik RouterOS.
 */
class PPPoEService extends BaseMikrotikService
{
    /**
     * Get all PPP secrets from router.
     */
    public function getAllSecrets(): array
    {
        return $this->read('/ppp/secret/print');
    }

    /**
     * Get a single PPP secret by name.
     */
    public function getSecret(string $username): ?array
    {
        $results = $this->read('/ppp/secret/print', ['name' => $username]);
        return $results[0] ?? null;
    }

    /**
     * Check if a PPP secret exists.
     */
    public function secretExists(string $username): bool
    {
        return $this->getSecret($username) !== null;
    }

    /**
     * Add a new PPP secret to the router.
     */
    public function addSecret(PPPoEAccount $account): bool
    {
        $data = $account->toMikrotikArray();

        $this->executeRetry(function () use ($data) {
            $this->add('/ppp/secret/add', $data);
        });

        $account->markAsSynced();
        $this->logInfo('PPP Secret created', ['username' => $account->username]);

        return true;
    }

    /**
     * Update an existing PPP secret.
     */
    public function updateSecret(PPPoEAccount $account): bool
    {
        $data = $account->toMikrotikArray();
        unset($data['name']); // name is the identifier

        $this->executeRetry(function () use ($account, $data) {
            $this->set('/ppp/secret/set', ['name' => $account->username], $data);
        });

        $account->markAsSynced();
        $this->logInfo('PPP Secret updated', ['username' => $account->username]);

        return true;
    }

    /**
     * Delete a PPP secret from the router.
     */
    public function deleteSecret(string $username): bool
    {
        $this->executeRetry(function () use ($username) {
            $this->remove('/ppp/secret/remove', ['name' => $username]);
        });

        $this->logInfo('PPP Secret deleted', ['username' => $username]);
        return true;
    }

    /**
     * Enable a PPP secret.
     */
    public function enableSecret(string $username): bool
    {
        $this->set('/ppp/secret/set', ['name' => $username], ['disabled' => 'no']);
        $this->logInfo('PPP Secret enabled', ['username' => $username]);
        return true;
    }

    /**
     * Disable/suspend a PPP secret.
     */
    public function disableSecret(string $username): bool
    {
        $this->set('/ppp/secret/set', ['name' => $username], ['disabled' => 'yes']);
        $this->logInfo('PPP Secret disabled', ['username' => $username]);
        return true;
    }

    /**
     * Change PPP secret password.
     */
    public function changePassword(string $username, string $newPassword): bool
    {
        $this->set('/ppp/secret/set', ['name' => $username], [
            'password' => $newPassword,
        ]);
        $this->logInfo('PPP Secret password changed', ['username' => $username]);
        return true;
    }

    /**
     * Get all active PPPoE connections.
     */
    public function getActiveConnections(): array
    {
        return $this->read('/ppp/active/print');
    }

    /**
     * Get active connection for a specific user.
     */
    public function getActiveConnection(string $username): ?array
    {
        $results = $this->read('/ppp/active/print', ['name' => $username]);
        return $results[0] ?? null;
    }

    /**
     * Check if a user is currently connected.
     */
    public function isUserOnline(string $username): bool
    {
        return $this->getActiveConnection($username) !== null;
    }

    /**
     * Disconnect an active PPPoE session.
     */
    public function disconnectUser(string $username): bool
    {
        try {
            $this->remove('/ppp/active/remove', ['name' => $username]);
            $this->logInfo('User disconnected', ['username' => $username]);
            return true;
        } catch (\Exception $e) {
            $this->logWarning('Failed to disconnect user (may not be online)', [
                'username' => $username,
                'error'    => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get PPPoE profile list.
     */
    public function getProfiles(): array
    {
        return $this->read('/ppp/profile/print');
    }

    /**
     * Get specific PPPoE profile.
     */
    public function getProfile(string $name): ?array
    {
        $results = $this->read('/ppp/profile/print', ['name' => $name]);
        return $results[0] ?? null;
    }

    /**
     * Add a PPPoE profile if it doesn't exist.
     */
    public function ensureProfile(string $name, array $config): void
    {
        if (!$this->getProfile($name)) {
            $this->add('/ppp/profile/add', array_merge(['name' => $name], $config));
            $this->logInfo('PPP Profile created', ['name' => $name]);
        }
    }

    /**
     * Sync all PPP secrets from router to local database.
     */
    public function syncAllSecrets(): array
    {
        $remoteSecrets = $this->getAllSecrets();
        $stats = ['created' => 0, 'updated' => 0, 'total' => count($remoteSecrets)];

        foreach ($remoteSecrets as $secret) {
            $localAccount = PPPoEAccount::where('router_id', $this->router->id)
                ->where('username', $secret['name'] ?? '')
                ->first();

            if ($localAccount) {
                $localAccount->update([
                    'profile'        => $secret['profile'] ?? null,
                    'disabled'       => ($secret['disabled'] ?? 'false') === 'true',
                    'comment'        => $secret['comment'] ?? null,
                    'last_synced_at' => now(),
                ]);
                $stats['updated']++;
            } else {
                $stats['created']++;
                // Could auto-create accounts if needed
            }
        }

        return $stats;
    }
}
