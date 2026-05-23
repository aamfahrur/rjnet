<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ConnectionType;
use App\Enums\CustomerStatus;
use App\Enums\InvoiceStatus;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\InternetPackage;
use App\Models\Invoice;
use App\Models\Router;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Dummy customer data — 25 realistic Indonesian customers.
     */
    private array $customers = [
        ['name' => 'Budi Santoso',       'phone' => '081234567801', 'status' => 'active',    'district' => 'Cilandak',         'city' => 'Jakarta Selatan',  'package' => 'PREMIUM-25M'],
        ['name' => 'Siti Nurhaliza',      'phone' => '081234567802', 'status' => 'active',    'district' => 'Pasar Minggu',     'city' => 'Jakarta Selatan',  'package' => 'STANDARD-10M'],
        ['name' => 'Ahmad Fauzi',         'phone' => '081234567803', 'status' => 'active',    'district' => 'Pondok Aren',      'city' => 'Tangerang Selatan', 'package' => 'ULTIMATE-50M'],
        ['name' => 'Dewi Lestari',        'phone' => '081234567804', 'status' => 'active',    'district' => 'Bintaro',          'city' => 'Tangerang Selatan', 'package' => 'BASIC-5M'],
        ['name' => 'Rizki Pratama',       'phone' => '081234567805', 'status' => 'suspended', 'district' => 'Cilandak',         'city' => 'Jakarta Selatan',  'package' => 'STANDARD-10M'],
        ['name' => 'Maya Anggraini',      'phone' => '081234567806', 'status' => 'active',    'district' => 'Pondok Aren',      'city' => 'Tangerang Selatan', 'package' => 'PREMIUM-25M'],
        ['name' => 'Hendra Gunawan',      'phone' => '081234567807', 'status' => 'active',    'district' => 'Kebayoran Lama',   'city' => 'Jakarta Selatan',  'package' => 'ULTIMATE-50M'],
        ['name' => 'Ratna Sari',          'phone' => '081234567808', 'status' => 'pending',   'district' => 'Pasar Minggu',     'city' => 'Jakarta Selatan',  'package' => 'BASIC-5M'],
        ['name' => 'Doni Kusuma',         'phone' => '081234567809', 'status' => 'isolated',  'district' => 'Cilandak',         'city' => 'Jakarta Selatan',  'package' => 'STANDARD-10M'],
        ['name' => 'Fitri Handayani',     'phone' => '081234567810', 'status' => 'active',    'district' => 'Bintaro',          'city' => 'Tangerang Selatan', 'package' => 'PREMIUM-25M'],
        ['name' => 'Agus Wijaya',         'phone' => '081234567811', 'status' => 'active',    'district' => 'Pondok Aren',      'city' => 'Tangerang Selatan', 'package' => 'BASIC-5M'],
        ['name' => 'Indah Permata',       'phone' => '081234567812', 'status' => 'active',    'district' => 'Ciputat',          'city' => 'Tangerang Selatan', 'package' => 'STANDARD-10M'],
        ['name' => 'Eko Prasetyo',        'phone' => '081234567813', 'status' => 'suspended', 'district' => 'Kebayoran Baru',   'city' => 'Jakarta Selatan',  'package' => 'PREMIUM-25M'],
        ['name' => 'Nina Amelia',         'phone' => '081234567814', 'status' => 'active',    'district' => 'Cilandak',         'city' => 'Jakarta Selatan',  'package' => 'ULTIMATE-50M'],
        ['name' => 'Rudi Hartono',        'phone' => '081234567815', 'status' => 'active',    'district' => 'Pasar Minggu',     'city' => 'Jakarta Selatan',  'package' => 'BASIC-5M'],
        ['name' => 'Linda Kusuma',        'phone' => '081234567816', 'status' => 'active',    'district' => 'Bintaro',          'city' => 'Tangerang Selatan', 'package' => 'PREMIUM-25M'],
        ['name' => 'Andi Prasetyo',       'phone' => '081234567817', 'status' => 'active',    'district' => 'Ciputat',          'city' => 'Tangerang Selatan', 'package' => 'ULTIMATE-50M'],
        ['name' => 'Sari Wulandari',      'phone' => '081234567818', 'status' => 'active',    'district' => 'Pondok Aren',      'city' => 'Tangerang Selatan', 'package' => 'STANDARD-10M'],
        ['name' => 'Bayu Setiawan',       'phone' => '081234567819', 'status' => 'active',    'district' => 'Cilandak',         'city' => 'Jakarta Selatan',  'package' => 'PREMIUM-25M'],
        ['name' => 'Putri Ayu',           'phone' => '081234567820', 'status' => 'terminated','district' => 'Kebayoran Lama',   'city' => 'Jakarta Selatan',  'package' => 'BASIC-5M'],
        ['name' => 'Toni Hermawan',       'phone' => '081234567821', 'status' => 'active',    'district' => 'Bintaro',          'city' => 'Tangerang Selatan', 'package' => 'STANDARD-10M'],
        ['name' => 'Rina Marlina',        'phone' => '081234567822', 'status' => 'active',    'district' => 'Pasar Minggu',     'city' => 'Jakarta Selatan',  'package' => 'ULTIMATE-50M'],
        ['name' => 'Dedi Irawan',         'phone' => '081234567823', 'status' => 'pending',   'district' => 'Ciputat',          'city' => 'Tangerang Selatan', 'package' => 'PREMIUM-25M'],
        ['name' => 'Yuni Astuti',         'phone' => '081234567824', 'status' => 'active',    'district' => 'Pondok Aren',      'city' => 'Tangerang Selatan', 'package' => 'BASIC-5M'],
        ['name' => 'Irfan Maulana',       'phone' => '081234567825', 'status' => 'active',    'district' => 'Cilandak',         'city' => 'Jakarta Selatan',  'package' => 'STANDARD-10M'],
    ];

    private array $addresses = [
        'Cilandak'       => ['Jl. Cilandak Raya No.', 'Cilandak Barat', '12430', -6.287420, 106.796600],
        'Pasar Minggu'   => ['Jl. Raya Pasar Minggu No.', 'Pejaten Barat', '12510', -6.283000, 106.844400],
        'Pondok Aren'    => ['Jl. Pondok Aren Raya No.', 'Pondok Aren', '15224', -6.265000, 106.700800],
        'Bintaro'        => ['Jl. Bintaro Utama No.', 'Bintaro', '15224', -6.275600, 106.732500],
        'Kebayoran Lama' => ['Jl. Kebayoran Lama No.', 'Kebayoran Lama Selatan', '12240', -6.244000, 106.779500],
        'Kebayoran Baru' => ['Jl. Kebayoran Baru No.', 'Kebayoran Baru', '12190', -6.238600, 106.801300],
        'Ciputat'        => ['Jl. Ciputat Raya No.', 'Ciputat', '15411', -6.301800, 106.742800],
    ];

    public function run(): void
    {
        $admin = User::where('email', 'admin@rjnet.id')->first();
        $adminId = $admin?->id ?? 1;

        // Fetch packages
        $packages = InternetPackage::all()->keyBy('code');
        if ($packages->isEmpty()) {
            $this->command?->warn('No internet packages found. Please run DatabaseSeeder first.');
            return;
        }

        // Fetch or ensure there's at least one router
        $router = Router::first();
        if (!$router) {
            $this->command?->warn('No routers found. Creating a dummy router...');
            $router = Router::create([
                'name'      => 'MikroTik POP Utama',
                'host'      => '192.168.88.1',
                'api_port'  => 8728,
                'username'  => 'admin',
                'password'  => 'dummypass',
                'is_active' => true,
                'status'    => \App\Enums\RouterStatus::ONLINE->value,
            ]);
        }

        $customerRole = \Spatie\Permission\Models\Role::where('name', 'customer')->where('guard_name', 'web')->first();

        $this->command?->info('Seeding ' . count($this->customers) . ' customers...');

        $index = 0;
        foreach ($this->customers as $data) {
            $index++;

            // Create user account for customer login (optional, ~80% have login)
            $userId = null;
            $username = str_replace(' ', '', strtolower($data['name']));
            if ($index <= 20) {
                $user = User::firstOrCreate(
                    ['email' => $username . '@gmail.com'],
                    [
                        'name'              => $data['name'],
                        'username'          => $username,
                        'password'          => Hash::make('password'),
                        'phone'             => $data['phone'],
                        'is_active'         => in_array($data['status'], ['active', 'suspended', 'pending']),
                        'email_verified_at' => now(),
                    ]
                );
                if ($customerRole) {
                    $user->assignRole($customerRole);
                }
                $userId = $user->id;
            }

            // Create customer
            $lastId = Customer::withTrashed()->max('id') ?? 0;
            $customer = Customer::create([
                'customer_code'     => 'CUS-' . str_pad((string) ($lastId + $index + 1), 6, '0', STR_PAD_LEFT),
                'user_id'           => $userId,
                'full_name'         => $data['name'],
                'email'             => $userId ? ($username . '@gmail.com') : null,
                'phone'             => $data['phone'],
                'id_number'         => '317' . str_pad((string) (10000000000 + $index), 11, '0', STR_PAD_LEFT),
                'status'            => CustomerStatus::from($data['status']),
                'registration_date' => now()->subDays(rand(7, 365)),
                'notes'             => $data['status'] === 'suspended' ? 'Suspend karena pembayaran overdue 2 bulan' : null,
                'created_by'        => $adminId,
            ]);

            // Create address
            $addrData = $this->addresses[$data['district']] ?? $this->addresses['Cilandak'];
            CustomerAddress::create([
                'customer_id' => $customer->id,
                'label'       => 'Rumah',
                'address'     => $addrData[0] . rand(1, 200) . ', RT ' . str_pad((string) rand(1, 10), 2, '0', STR_PAD_LEFT) . '/RW ' . str_pad((string) rand(1, 8), 2, '0', STR_PAD_LEFT),
                'village'     => $addrData[1],
                'district'    => $data['district'],
                'city'        => $data['city'],
                'province'    => in_array($data['city'], ['Jakarta Selatan', 'Jakarta Pusat']) ? 'DKI Jakarta' : 'Banten',
                'postal_code' => $addrData[2],
                'latitude'    => $addrData[3] + (rand(-50, 50) / 10000),
                'longitude'   => $addrData[4] + (rand(-50, 50) / 10000),
                'is_primary'  => true,
            ]);

            // Create subscription
            $pkg = $packages->get($data['package']);
            if ($pkg) {
                Subscription::create([
                    'customer_id'     => $customer->id,
                    'package_id'      => $pkg->id,
                    'router_id'       => $router->id,
                    'connection_type' => ConnectionType::PPPOE->value,
                    'status'          => $data['status'],
                    'start_date'      => $customer->registration_date,
                    'billing_date'    => now()->startOfMonth()->addDays(rand(1, 25)),
                    'auto_renewal'    => true,
                ]);

                // Create invoice for current month
                $billingDate = now()->startOfMonth()->addDays(5);
                $dueDate = $billingDate->copy()->addDays(10);
                $status = InvoiceStatus::PENDING;
                if ($data['status'] === 'active' && rand(1, 3) === 1) {
                    $status = InvoiceStatus::PAID;
                } elseif ($data['status'] === 'suspended') {
                    $status = InvoiceStatus::OVERDUE;
                }

                Invoice::create([
                    'invoice_number'       => 'INV-' . str_pad((string) ($customer->id * 100 + now()->month), 8, '0', STR_PAD_LEFT),
                    'customer_id'          => $customer->id,
                    'subscription_id'      => Subscription::where('customer_id', $customer->id)->latest()->first()?->id,
                    'subtotal'             => $pkg->price,
                    'tax_amount'           => 0,
                    'discount_amount'      => 0,
                    'total_amount'         => $pkg->price,
                    'paid_amount'          => $status === InvoiceStatus::PAID ? $pkg->price : 0,
                    'remaining_amount'     => $status === InvoiceStatus::PAID ? 0 : $pkg->price,
                    'status'               => $status,
                    'issue_date'           => $billingDate,
                    'due_date'             => $dueDate,
                    'billing_period_start' => now()->startOfMonth(),
                    'billing_period_end'   => now()->endOfMonth(),
                    'notes'                => null,
                ]);
            }

            $this->command?->info("  [$index/" . count($this->customers) . "] {$data['name']} — {$data['package']} ({$data['status']})");
        }

        $this->command?->info('Successfully seeded ' . count($this->customers) . ' customers!');
    }
}
