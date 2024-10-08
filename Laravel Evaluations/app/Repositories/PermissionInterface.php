<?php

namespace App\Repositories;


interface PermissionInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);

}