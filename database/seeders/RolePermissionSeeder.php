<?php

namespace Database\Seeders;

use App\Http\Controllers\API\VerificationController;
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
        // Clear cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        // $guard = config('auth.defaults.guard'); // ensure correct guard (usually 'web' or 'api')
        $guard = 'sanctum';

        // Define permissions (include guard_name)
        Permission::firstOrCreate(['name' => 'create bookings', 'guard_name' => $guard]);
        Permission::firstOrCreate(['name' => 'view bookings',   'guard_name' => $guard]);
        Permission::firstOrCreate(['name' => 'delete bookings', 'guard_name' => $guard]);

        Permission::firstOrCreate(['name' => 'create drivers', 'guard_name' => $guard]);
        Permission::firstOrCreate(['name' => 'view drivers',   'guard_name' => $guard]);
        Permission::firstOrCreate(['name' => 'update drivers', 'guard_name' => $guard]);
        Permission::firstOrCreate(['name' => 'delete drivers', 'guard_name' => $guard]);

        Permission::firstOrCreate(['name' => 'create trucks', 'guard_name' => $guard]);
        Permission::firstOrCreate(['name' => 'view trucks',   'guard_name' => $guard]);
        Permission::firstOrCreate(['name' => 'update trucks', 'guard_name' => $guard]);
        Permission::firstOrCreate(['name' => 'delete trucks', 'guard_name' => $guard]);

        Permission::firstOrCreate(['name' => 'view transporter', 'guard_name' => $guard]);
        Permission::firstOrCreate(['name' => 'create transporter', 'guard_name' => $guard]);

        // Roles (include guard_name)
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
        $transporter = Role::firstOrCreate(['name' => 'transporter', 'guard_name' => $guard]);
        $operational_verifier = Role::firstOrCreate(['name' => 'operational_verifier', 'guard_name' => $guard]);
        $company = Role::firstOrCreate(['name' => 'company', 'guard_name' => $guard]);
        $customer_care = Role::firstOrCreate(['name' => 'customer_care', 'guard_name' => $guard]);
        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all());
        $transporter->givePermissionTo([
            'create bookings', 'create drivers', 'view drivers', 'update drivers', 
            'delete drivers', 'create trucks', 'view trucks', 'update trucks', 
            'delete trucks', 
        ]);
        $operational_verifier->givePermissionTo([
            'create bookings', 'view bookings', 'view drivers', 'view trucks', 
            'view transporter'
        ]);
        $company->givePermissionTo([
            'create transporter', 'view drivers', 'view trucks'
        ]);
        $customer_care->givePermissionTo([
            'create bookings', 'view bookings', 'create drivers', 'view drivers', 
            'update drivers', 'create trucks', 'view trucks', 'update trucks', 'view transporter'
        ]);

    }
}
