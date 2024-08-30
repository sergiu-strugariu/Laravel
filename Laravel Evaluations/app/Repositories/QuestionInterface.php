<?php

namespace App\Repositories;

interface QuestionInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);
    
    function search($id);

    function untrash($id);

}