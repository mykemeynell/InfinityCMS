<?php

use Illuminate\Database\Seeder;

class InfinityDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            GroupSeeder::class,
            PermissionSeeder::class,
            PermissionGroupRelationshipSeeder::class,
            SettingsSeeder::class,
        ]);
    }
}
