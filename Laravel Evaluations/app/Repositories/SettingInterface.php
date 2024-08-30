<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 1/12/2018
 * Time: 9:55 AM
 */

namespace App\Repositories;


interface SettingInterface
{
    function getAll();

    function getById($id);
    
    function getByKey($key);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);
}