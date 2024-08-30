<?php
/**
 * Interface for ReferenceRepository
 */

namespace App\Repositories;

interface ReferenceInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);

    function search($filters);
}