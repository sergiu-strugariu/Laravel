<?php
/**
 * Interface for TaskStatusRepository
 */

namespace App\Repositories;

interface TaskStatusInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);
}