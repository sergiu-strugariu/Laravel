<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 11/27/2017
 * Time: 11:39 AM
 */

use Illuminate\Database\Seeder;
use App\Models\PermissionRole;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        /*
         * 1: Master
         * 2: admin
         * 3: Recruiter
         * 4: Css
         * 5: Client
         * 6: TDS
         * 7: Assessor
         */

        /*Client permissions*/
        PermissionRole::create(['role_id' => 5, 'permission_id' => 1]);
        PermissionRole::create(['role_id' => 5, 'permission_id' => 2]);
        PermissionRole::create(['role_id' => 5, 'permission_id' => 3]);
        PermissionRole::create(['role_id' => 5, 'permission_id' => 4]);
        PermissionRole::create(['role_id' => 5, 'permission_id' => 5]);
        PermissionRole::create(['role_id' => 5, 'permission_id' => 6]);
        PermissionRole::create(['role_id' => 5, 'permission_id' => 7]);
        PermissionRole::create(['role_id' => 5, 'permission_id' => 8]);
        PermissionRole::create(['role_id' => 5, 'permission_id' => 9]);
        PermissionRole::create(['role_id' => 5, 'permission_id' => 10]);
        PermissionRole::create(['role_id' => 5, 'permission_id' => 11]);
        PermissionRole::create(['role_id' => 5, 'permission_id' => 12]);
        PermissionRole::create(['role_id' => 5, 'permission_id' => 15]);

        /*Css permissions*/
        PermissionRole::create(['role_id' => 4, 'permission_id' => 1]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 2]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 3]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 4]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 5]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 6]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 7]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 8]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 9]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 10]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 11]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 12]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 13]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 14]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 15]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 16]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 17]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 18]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 19]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 20]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 21]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 22]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 23]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 24]);
        PermissionRole::create(['role_id' => 4, 'permission_id' => 25]);

        /*Recruiters permissions*/
        PermissionRole::create(['role_id' => 3, 'permission_id' => 1]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 2]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 3]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 4]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 5]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 6]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 7]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 8]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 9]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 10]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 11]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 12]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 13]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 14]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 15]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 16]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 17]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 18]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 19]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 20]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 21]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 22]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 23]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 24]);
        PermissionRole::create(['role_id' => 3, 'permission_id' => 25]);

        /*Admins*/
        PermissionRole::create(['role_id' => 1, 'permission_id' => 1]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 2]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 3]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 4]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 5]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 6]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 7]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 8]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 9]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 10]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 11]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 12]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 13]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 14]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 15]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 16]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 17]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 18]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 19]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 20]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 21]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 22]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 23]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 24]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 25]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 26]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 27]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 28]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 29]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 30]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 31]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 32]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 33]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 34]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 35]);
        PermissionRole::create(['role_id' => 1, 'permission_id' => 36]);


        PermissionRole::create(['role_id' => 2, 'permission_id' => 1]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 2]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 3]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 4]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 5]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 6]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 7]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 8]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 9]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 10]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 11]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 12]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 13]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 14]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 15]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 16]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 17]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 18]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 19]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 20]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 21]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 22]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 23]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 24]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 25]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 26]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 27]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 28]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 29]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 30]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 31]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 32]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 33]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 34]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 35]);
        PermissionRole::create(['role_id' => 2, 'permission_id' => 36]);

        /*Assessor*/
        PermissionRole::create(['role_id' => 7, 'permission_id' => 37]);
        PermissionRole::create(['role_id' => 7, 'permission_id' => 38]);
        PermissionRole::create(['role_id' => 7, 'permission_id' => 39]);
        PermissionRole::create(['role_id' => 7, 'permission_id' => 40]);
        PermissionRole::create(['role_id' => 7, 'permission_id' => 1]);
        PermissionRole::create(['role_id' => 7, 'permission_id' => 3]);
        PermissionRole::create(['role_id' => 7, 'permission_id' => 4]);
        PermissionRole::create(['role_id' => 7, 'permission_id' => 6]);
        PermissionRole::create(['role_id' => 7, 'permission_id' => 9]);
        PermissionRole::create(['role_id' => 7, 'permission_id' => 11]);
        PermissionRole::create(['role_id' => 7, 'permission_id' => 12]);
        PermissionRole::create(['role_id' => 7, 'permission_id' => 29]);

        /*TDS*/
        PermissionRole::create(['role_id' => 6, 'permission_id' => 1]);
        PermissionRole::create(['role_id' => 6, 'permission_id' => 2]);
        PermissionRole::create(['role_id' => 6, 'permission_id' => 3]);
        PermissionRole::create(['role_id' => 6, 'permission_id' => 4]);
        PermissionRole::create(['role_id' => 6, 'permission_id' => 6]);
        PermissionRole::create(['role_id' => 6, 'permission_id' => 15]);

    }
}