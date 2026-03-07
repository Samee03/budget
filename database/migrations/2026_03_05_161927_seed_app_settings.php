<?php

use App\Models\Admin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Admin::where('email', 'admin@domain.com')->exists()) {
            $admin = Admin::create([
                'name' => 'Admin User',
                'email' => 'admin@domain.com',
                'password' => Hash::make('admin00pw00@'),
                'status' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);

            $role = Role::firstOrCreate(
                ['name' => 'super_admin', 'guard_name' => 'admin'],
                ['name' => 'super_admin', 'guard_name' => 'admin'],
            );

            $admin->assignRole($role);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $admin = Admin::where('email', 'admin@domain.com')->first();

        if ($admin) {
            $admin->delete();
        }
    }
};
