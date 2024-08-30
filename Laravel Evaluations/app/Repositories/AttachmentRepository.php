<?php
/**
 * Repository to handle data flow for Attachment model
 */

namespace App\Repositories;

use App\Models\Attachment;
use App\Models\Task;
use Illuminate\Http\UploadedFile;


class AttachmentRepository implements AttachmentInterface
{
    /**
     * @var $model
     */
    private $model;

    /**
     * AttachmentRepository constructor.
     *
     * @param \App\Models\Attachment $model
     */

    public function __construct(Attachment $model)
    {
        $this->model = $model;
    }

    /**
     * Get all Attachments.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get Attachment by id.
     *
     * @param integer $id
     *
     * @return \App\Models\Attachment
     */
    public function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new Attachment.
     *
     * @param array $attributes
     *
     * @return \App\Models\Attachment
     */
    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    /**
     * Create a new Attachment.
     *
     * @param \App\Models\Task $task
     * @param UploadedFile $attachment
     *
     * @return \App\Models\Attachment
     */
    public function createTaskAttachment(Task $task, UploadedFile $attachment)
    {
        $filepath = 'assets/attachments/tasks/' . $task->id;
        $attachmentName = $attachment->getClientOriginalName();

        if(file_exists($filepath.DIRECTORY_SEPARATOR.$attachmentName)){
            $parts = explode('.', $attachmentName);
            unset($parts[count($parts)-1]);
            $fileName = implode('', $parts);
            $attachmentName = $fileName.'-'.time().'.'.$attachment->getClientOriginalExtension();
        }


        $attachment->move(public_path($filepath), $attachmentName);

        return $this->create([
            'filepath' => $filepath,
            'filename' => $attachment->getClientOriginalName(),
            'filetype' => $attachment->getClientOriginalExtension(),
            'url' => '/' . $filepath . '/' . $attachmentName,
            'model_id' => $task->id,
            'model' => 'Task',
        ]);
    }

    /**
     * Update an Attachment.
     *
     * @param integer $id
     * @param array $attributes
     *
     * @return \App\Models\Attachment
     */
    public function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    /**
     * Delete an Attachment.
     *
     * @param integer $id
     *
     * @return boolean
     */
    public function delete($id)
    {
        $model = $this->model->find($id);
        if ($model) {
            @unlink(public_path($model->filepath . DIRECTORY_SEPARATOR . $model->filename));
            return $model->delete();
        }

        return false;
    }

}