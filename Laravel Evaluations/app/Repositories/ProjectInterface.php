<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 12/4/2017
 * Time: 9:13 AM
 */

namespace App\Repositories;


interface ProjectInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);

    function getByClient($client_id);

    function search($filters);
}