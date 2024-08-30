<?php

namespace App\Repositories;

interface LanguagePaperTypeInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);
    
    function untrash($id);

}