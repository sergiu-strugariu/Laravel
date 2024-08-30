<?php
/**
 * Interface for AttachmentRepository
 */

namespace App\Repositories;

use App\Models\Task;
use Illuminate\Http\UploadedFile;

interface AttachmentInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function createTaskAttachment(Task $task, UploadedFile $attachment);

    function update($id, array $attributes);

    function delete($id);
}