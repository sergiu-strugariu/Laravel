<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 12/4/2017
 * Time: 12:48 PM
 */

namespace App\Repositories;


interface ProjectParticipantsInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);

    function deleteProjectParticipant($participantId);

    function getByUserId($userId);

    function getByProjectId($projectId);

    function getByUserAndProjectId($userId, $projectId);
}