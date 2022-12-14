<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(RoleUserSeeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(CompanyModuleSeeder::class);
        $this->call(ExpenseSeeder::class);
    }
}
