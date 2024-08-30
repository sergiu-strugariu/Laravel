<?php

namespace App\Repositories;

interface MailsInterface
{
    function getAll();

    function getById($id);
    
    function getBySlug($slug);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);
    
    function search($id);

    function untrash($id);

}