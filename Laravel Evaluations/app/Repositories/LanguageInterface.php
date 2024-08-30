<?php
/**
 * Interface for LanguageRepository
 */

namespace App\Repositories;

interface LanguageInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);

    function getByName($name);
}