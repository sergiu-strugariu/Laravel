<?php
/**
 * Interface for UserGroupRepository
 */

namespace App\Repositories;

interface UserGroupInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);
}