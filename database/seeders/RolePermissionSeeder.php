<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permission = [
            'manage categories',
            'manage tools',
            'manage projects',
            'manage applicants',
            'manage wallets',
            'manage project tools',
            'apply job',
            'topup wallet',
            'withdraw wallet'
        ];

        foreach ($permission as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        $client_role = Role::firstOrCreate(['name' => 'project_client']);
        $client_permission = [
            'manage projects',
            'manage applicants',
            'manage project tools',
            'topup wallet',
            'withdraw wallet'
        ];
        $client_role->syncPermissions($client_permission);

        $freelancer_role = Role::firstOrCreate(['name' => 'project_freelancer']);
        $freelancer_permission = [
            'apply job',
            'withdraw wallet'
        ];
        $freelancer_role->syncPermissions($freelancer_permission);

        $super_admin_role = Role::firstOrCreate(['name' => 'super_admin']);
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'occupation' => 'Owner',
            'connect' => '998877',
            'avatar' => 'images/default-avatar.png',
            'password' => bcrypt('password')
        ]);
        $user->assignRole($super_admin_role);

        $wallet = new Wallet([
            'balance' => 0
        ]);
        $user->wallet()->save($wallet);
    }
}
