<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'user',
            'permission',
            'member',
            'branch',
            'agent',
            'saving_account',
            'saving_account_withdrawal',
            'saving_account_transaction',
            'role'
        ];

        foreach($modules as $module) {
            foreach(['create', 'read', 'update', 'delete'] as $mode) {
                Permission::updateOrCreate(['name' => "$module $mode"], [
                    'name' => "$module $mode",
                    'guard_name' => 'web'
                ]);
            }
        }
    }
}
