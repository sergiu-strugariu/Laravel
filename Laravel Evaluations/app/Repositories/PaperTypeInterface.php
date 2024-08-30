<?php
/**
 * Interface for PaperTypeRepository
 */

namespace App\Repositories;

interface PaperTypeInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);

    function getByName($name);
}