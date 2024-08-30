<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 12/21/2017
 * Time: 12:25 PM
 */

namespace App\Repositories;


interface PaperAnswerInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);

}