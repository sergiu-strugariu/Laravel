<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 11/24/2017
 * Time: 8:30 AM
 */

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsSeeder extends Seeder
{

    public function run()
    {
        /*Client*/
        Permission::create(['name' => 'menu.view_projects', 'slug' => 'menu.view_projects', 'module_id' => 5]);
        Permission::create(['name' => 'menu.order_projects', 'slug' => 'menu.order_projects', 'module_id' => 5]);
        Permission::create(['name' => 'export_excel', 'slug' => 'export_excel', 'module_id' => 5]);
        Permission::create(['name' => 'project.view_tasks', 'slug' => 'project.view_tasks', 'module_id' => 5]);
        Permission::create(['name' => 'project.create_tasks', 'slug' => 'project.create_tasks', 'module_id' => 5]);
        Permission::create(['name' => 'project.view_task', 'slug' => 'project.view_task', 'module_id' => 5]);
        Permission::create(['name' => 'task.download_report', 'slug' => 'task.download_report', 'module_id' => 5]);
        Permission::create(['name' => 'task.cancel', 'slug' => 'task.cancel', 'module_id' => 5]);
        Permission::create(['name' => 'task.edit_test_taker', 'slug' => 'task.edit_test_taker', 'module_id' => 5]);
        Permission::create(['name' => 'task.update', 'slug' => 'task.update', 'module_id' => 5]);
        Permission::create(['name' => 'task.change_language', 'slug' => 'task.change_language', 'module_id' => 5]);
        Permission::create(['name' => 'test.invite', 'slug' => 'test.invite', 'module_id' => 5]);

        /*Css have all of the above plus these*/
        Permission::create(['name' => 'project.create', 'slug' => 'project.create', 'module_id' => 5]);
        Permission::create(['name' => 'project.delete_own', 'slug' => 'project.delete_own', 'module_id' => 5]);
        Permission::create(['name' => 'test.view_answers', 'slug' => 'test.view_answers', 'module_id' => 5]);
        Permission::create(['name' => 'test.reset_online_test', 'slug' => 'test.reset_online_test', 'module_id' => 5]);
        Permission::create(['name' => 'task.add_assessor_manually', 'slug' => 'task.add_assessor_manually', 'module_id' => 5]);
        Permission::create(['name' => 'task.change_assessor', 'slug' => 'task.change_assessor', 'module_id' => 5]);
        Permission::create(['name' => 'task.view_assessors_history', 'slug' => 'task.view_assessors_history', 'module_id' => 5]);
        Permission::create(['name' => 'task.delete_own', 'slug' => 'task.delete_own', 'module_id' => 5]);
        Permission::create(['name' => 'task.view_tags', 'slug' => 'task.view_tags', 'module_id' => 5]);
        Permission::create(['name' => 'task.change_tags', 'slug' => 'task.change_tags', 'module_id' => 5]);
        Permission::create(['name' => 'task.order_by_online_test_date', 'slug' => 'task.order_by_online_test_date', 'module_id' => 5]);
        Permission::create(['name' => 'assessor.view_name', 'slug' => 'assessor.view_name', 'module_id' => 5]);
        Permission::create(['name' => 'status.change', 'slug' => 'status.change', 'module_id' => 5]);

        /*Admins have all of the above plus these*/
        Permission::create(['name' => 'project.delete', 'slug' => 'project.delete', 'module_id' => 5]);
        Permission::create(['name' => 'group.create', 'slug' => 'group.create', 'module_id' => 5]);
        Permission::create(['name' => 'role.set_assessor', 'slug' => 'role.set_assessor', 'module_id' => 5]);
        Permission::create(['name' => 'report.fill_report', 'slug' => 'report.fill_report', 'module_id' => 5]);
        Permission::create(['name' => 'task.order_tasks_by_project', 'slug' => 'task.order_tasks_by_project', 'module_id' => 5]);
        Permission::create(['name' => 'document.upload', 'slug' => 'document.upload', 'module_id' => 5]);
        Permission::create(['name' => 'document.update', 'slug' => 'document.update', 'module_id' => 5]);
        Permission::create(['name' => 'user.create', 'slug' => 'user.create', 'module_id' => 5]);
        Permission::create(['name' => 'user.update', 'slug' => 'user.update', 'module_id' => 5]);
        Permission::create(['name' => 'package.create', 'slug' => 'package.create', 'module_id' => 5]);
        Permission::create(['name' => 'package.update', 'slug' => 'package.update', 'module_id' => 5]);

        /*Assesors*/
        Permission::create(['name' => 'status.change_to_allocated', 'slug' => 'status.change_to_allocated', 'module_id' => 5]);
        Permission::create(['name' => 'status.change_to_issue', 'slug' => 'status.change_to_issue', 'module_id' => 5]);
        Permission::create(['name' => 'task.refuse', 'slug' => 'task.refuse', 'module_id' => 5]);
        Permission::create(['name' => 'document.access', 'slug' => 'document.access', 'module_id' => 5]);

    }
}