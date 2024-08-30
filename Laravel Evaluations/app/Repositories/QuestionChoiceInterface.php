<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 1/3/2018
 * Time: 5:03 PM
 */

namespace App\Repositories;


interface QuestionChoiceInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);
}