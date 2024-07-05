<?php

namespace Database\Seeders;

use App\Enums\RolesEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles =  RolesEnum::cases();

        foreach($roles as $role){
            \Spatie\Permission\Models\Role::updateOrCreate(['name' => $role->value], [
                'name' => $role->value,
                'guard_name' => 'web'
            ]);

            if($role->value == RolesEnum::SUPER_ADMIN->value){
                $role = \Spatie\Permission\Models\Role::whereName(RolesEnum::SUPER_ADMIN->value)->first();
                $role->syncPermissions(\Spatie\Permission\Models\Permission::all());
            }
        }
    }
}
