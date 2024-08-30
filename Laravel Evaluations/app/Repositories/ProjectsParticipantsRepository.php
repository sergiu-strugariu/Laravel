<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 12/4/2017
 * Time: 12:48 PM
 */

namespace App\Repositories;


use App\Models\ProjectParticipant;

class ProjectsParticipantsRepository implements ProjectParticipantsInterface
{

    /**
     * @var $model
     */
    private $model;

    /**
     * ProjectParticipantRepository constructor.
     *
     * @param \App\Models\ProjectParticipant $model
     */

    public function __construct(ProjectParticipant $model)
    {
        $this->model = $model;
    }

    /**
     * Get all project participants
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    function getAll()
    {
        return $this->model->all();
    }

    /**
     * Get project participant by id
     *
     * @param $id
     * @return mixed
     */
    function getById($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create project participant
     *
     * @param array $attributes
     * @return bool
     */
    function create(array $attributes)
    {
        $projectParticipant = new ProjectParticipant();
        $projectParticipant->user_id = $attributes['user_id'];
        $projectParticipant->project_id = $attributes['project_id'];
        return $projectParticipant->save();
    }

    /**
     * Update project participant
     *
     * @param $id
     * @param array $attributes
     * @return mixed
     */
    function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }


    /**
     * Delete project participant
     *
     * @param $id
     * @return mixed
     */
    function delete($id)
    {
        return $this->model->find($id)->delete();
    }

    /**
     * Delete project participant by participant id
     *
     * @param $participantId
     * @return mixed
     */
    function deleteProjectParticipant($participantId)
    {
       return $this->model->where('user_id', $participantId)->delete();
    }

    /**
     * Get participants by user_id
     *
     * @param $user_id
     * @param $project_id
     * @return mixed
     */
    function getByUserAndProjectId($user_id, $project_id){
        return $this->model->where('user_id', $user_id)->where('project_id', $project_id)->first();
    }

    /**
     * Return project participant by user id
     *
     * @param $userId
     * @return mixed
     */
    function getByUserId($userId){
        return $this->model->where('user_id', $userId)->with('project')->get();
    }


    /**
     * Return project participants by project id
     *
     * @param $projectId
     * @return mixed
     */
    function getByProjectId($projectId)
    {
        return $this->model->where('project_id', $projectId)->get();
    }
}