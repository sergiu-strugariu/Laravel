<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('ClientsSeeder');
        $this->call('UserTableSeeder');
        $this->call('RolesTableSeeder');
        $this->call('ModulesTableSeeder');
        $this->call('PermissionsSeeder');
        $this->call('RolePermissionSeeder');
        $this->call('ProjectTypeSeeder');
        $this->call('LanguagesTableSeeder');
        $this->call('PaperTypesTableSeeder');
        $this->call('TaskStatusesTableSeeder');
        $this->call('UserStatusSeeder');
        $this->call('GroupsTableSeeder');
        $this->call('UserRoleSeeder');
        $this->call('ReferencesTableSeeder');
        $this->call('QuestionLevelsSeeder');
        $this->call('QuestionsSeeder');
        $this->call('MailSeeder');
        $this->call('SettingsSeeder');
        $this->call(RoleTaskUpdateTableSeeder::class);
        $this->call(TaskUpdatesTableSeeder::class);
        $this->call(PricingTypeSeeder::class);
    }
}
