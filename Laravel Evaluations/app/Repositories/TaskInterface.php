<?php
/**
 * Interface for TaskRepository
 */

namespace App\Repositories;

interface TaskInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);

    function search($project, $filters);

    function createOrUpdate(array $attributes);
}