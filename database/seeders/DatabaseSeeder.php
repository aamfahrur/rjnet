<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\InternetPackage;
use App\Models\PaymentGatewayConfig;
use App\Models\RouterGroup;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =========================================================================
        // Roles & Permissions
        // =========================================================================

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $teknisiRole = Role::firstOrCreate(['name' => 'teknisi', 'guard_name' => 'web']);
        $csRole = Role::firstOrCreate(['name' => 'cs', 'guard_name' => 'web']);
        $customerRole = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        // Permissions by module
        $permissions = [
            'customer'   => ['view customer', 'create customer', 'edit customer', 'delete customer', 'suspend customer'],
            'package'    => ['view package', 'create package', 'edit package', 'delete package'],
            'router'     => ['view router', 'create router', 'edit router', 'delete router', 'test router'],
            'invoice'    => ['view invoice', 'create invoice', 'edit invoice', 'delete invoice', 'generate invoice'],
            'payment'    => ['view payment', 'confirm payment'],
            'ticket'     => ['view ticket', 'create ticket', 'edit ticket', 'assign ticket', 'resolve ticket', 'close ticket'],
            'topology'   => ['view topology', 'manage topology'],
            'monitoring' => ['view monitoring'],
            'report'     => ['view report'],
            'user'       => ['view user', 'create user', 'edit user', 'delete user'],
            'setting'    => ['manage settings'],
        ];

        $allPermissions = [];
        foreach ($permissions as $group => $perms) {
            foreach ($perms as $perm) {
                $allPermissions[] = Permission::firstOrCreate([
                    'name'       => $perm,
                    'guard_name' => 'web',
                ], [
                    'group' => $group,
                ]);
            }
        }

        $adminRole->syncPermissions($allPermissions);
        $teknisiRole->syncPermissions(Permission::whereIn('group', ['customer', 'ticket', 'monitoring', 'topology'])->get());
        $csRole->syncPermissions(Permission::whereIn('group', ['customer', 'invoice', 'payment', 'ticket'])->get());
        $customerRole->syncPermissions(Permission::whereIn('name', ['view ticket', 'create ticket'])->get());

        // =========================================================================
        // System Users
        // =========================================================================

        $admin = User::firstOrCreate(
            ['email' => 'admin@rjnet.id'],
            [
                'name'              => 'Super Admin',
                'username'          => 'admin',
                'password'          => Hash::make('password'),
                'phone'             => '081234567890',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        User::firstOrCreate(
            ['email' => 'teknisi@rjnet.id'],
            [
                'name'              => 'Teknisi Lapangan',
                'username'          => 'teknisi',
                'password'          => Hash::make('password'),
                'phone'             => '081234567891',
                'is_active'         => true,
                'email_verified_at' => now(),
            ]
        )->assignRole('teknisi');

        // =========================================================================
        // Router Groups
        // =========================================================================

        RouterGroup::firstOrCreate(
            ['name' => 'Main POP'],
            ['location' => 'Kantor Pusat', 'description' => 'POP Utama']
        );

        RouterGroup::firstOrCreate(
            ['name' => 'Branch POP'],
            ['location' => 'Cabang A', 'description' => 'POP Cabang']
        );

        // =========================================================================
        // Payment Gateways
        // =========================================================================

        PaymentGatewayConfig::firstOrCreate(
            ['code' => 'ipaymu'],
            [
                'name'         => 'iPaymu',
                'driver_class' => \App\Services\Payment\Drivers\IPaymuDriver::class,
                'config'       => [
                    'va'      => env('IPAYMU_VA', ''),
                    'api_key' => env('IPAYMU_API_KEY', ''),
                    'sandbox' => true,
                ],
                'admin_fee_percentage' => 0,
                'admin_fee_fixed'      => 1500,
                'is_active'            => true,
                'is_sandbox'           => true,
                'sort_order'           => 1,
            ]
        );

        // =========================================================================
        // Internet Packages
        // =========================================================================

        $packages = [
            ['code' => 'BASIC-5M', 'name' => 'Paket Basic 5 Mbps', 'price' => 150_000, 'download_speed_bps' => 5_000_000, 'upload_speed_bps' => 1_000_000, 'mikrotik_profile' => '5M-profile', 'priority' => 8],
            ['code' => 'STANDARD-10M', 'name' => 'Paket Standard 10 Mbps', 'price' => 200_000, 'download_speed_bps' => 10_000_000, 'upload_speed_bps' => 2_000_000, 'mikrotik_profile' => '10M-profile', 'priority' => 7],
            ['code' => 'PREMIUM-25M', 'name' => 'Paket Premium 25 Mbps', 'price' => 350_000, 'download_speed_bps' => 25_000_000, 'upload_speed_bps' => 5_000_000, 'mikrotik_profile' => '25M-profile', 'priority' => 5],
            ['code' => 'ULTIMATE-50M', 'name' => 'Paket Ultimate 50 Mbps', 'price' => 500_000, 'download_speed_bps' => 50_000_000, 'upload_speed_bps' => 10_000_000, 'mikrotik_profile' => '50M-profile', 'priority' => 3],
        ];

        foreach ($packages as $pkg) {
            InternetPackage::firstOrCreate(
                ['code' => $pkg['code']],
                $pkg + ['created_by' => $admin->id, 'is_active' => true, 'is_visible' => true]
            );
        }

        $this->command?->info('Database seeded successfully!');
        $this->command?->info('Default admin: admin@rjnet.id / password');

        // Seed dummy customers
        $this->call(CustomerSeeder::class);
        $this->command?->info('Customer dummy data seeded!');
    }
}
