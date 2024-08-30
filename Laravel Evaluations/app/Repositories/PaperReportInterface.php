<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 1/5/2018
 * Time: 4:16 PM
 */

namespace App\Repositories;


interface PaperReportInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);

    function filterResults($filters);

}