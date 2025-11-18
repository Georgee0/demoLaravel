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
        $guard = config('auth.defaults.guard'); // ensure correct guard (usually 'web' or 'api')

        // Define permissions
        Permission::create(['name' => 'create bookings']);
        Permission::create(['name' => 'view bookings']);
        Permission::create(['name' => 'delete bookings']);

        Permission::create(['name' => 'create drivers']);
        Permission::create(['name' => 'view drivers']);
        Permission::create(['name' => 'update drivers']);
        Permission::create(['name' => 'delete drivers']);

        Permission::create(['name' => 'create trucks']);
        Permission::create(['name' => 'view trucks']);
        Permission::create(['name' => 'update trucks']);
        Permission::create(['name' => 'delete trucks']);

        Permission::create(['name' => 'view transporter']);
        Permission::create(['name' => 'create transporter']);
        

        //  Roles
        $admin = Role::create(['name' => 'admin']);
        $transporter = Role::create(['name' => 'transporter']);
        $operational_verifier = Role::create(['name' => 'operationalVerifier']);
        $company = Role::create(['name' => 'company']);
        $customer_care = Role::create(['name' => 'customerCare']);

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
