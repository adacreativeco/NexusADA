<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RoleAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'admin@dhe.com')->first();
        if ($user) {
            // Role assignment using Spatie method
            $user->assignRole('super_admin');
        }
    }
}
