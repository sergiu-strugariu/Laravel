<?php
/**
 * Interface for TaskFollowersRepository
 */

namespace App\Repositories;

interface TaskFollowersInterface
{
    function getAll();

    function getById($id);

    function getByUserAndTask(array $attributes);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);

    function createOrSkip(array $attributes);

    function deleteFollowerFromTask(array $attributes);
}