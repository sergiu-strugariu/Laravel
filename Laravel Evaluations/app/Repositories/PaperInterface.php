<?php
/**
 * Interface for PaperRepository
 */

namespace App\Repositories;

interface PaperInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);

    function getAllTaskPapers($id);

    function createOrSkip(array $attributes);
    
    function cancelAllPapers($task_id);
}