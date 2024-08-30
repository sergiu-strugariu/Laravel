<?php
/**
 * Interface for GroupUserRepository
 */

namespace App\Repositories;

interface GroupUserInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);

    function search($project, $filters);

    function createOrUpdate(array $attributes);

    function deleteUserFromGroup(array $attributes);
}