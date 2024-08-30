<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 12/19/2017
 * Time: 2:35 PM
 */

namespace App\Repositories;


interface LogInterface
{

    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);

}