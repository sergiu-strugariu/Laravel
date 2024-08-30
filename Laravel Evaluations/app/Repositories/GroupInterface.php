<?php
/**
 * Interface for GroupRepository
 */

namespace App\Repositories;

interface GroupInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);

    function getAssessorsFromLanguageGroup($language_id, $user_id = 0);

    function getAssessorsByLanguageAndNative($language_id, $native);

    function getAllAssessorsInGroups();

    function getRandomAssessor($language_id, $user_id = 0);
    
    function getRandomAssessorRefuse($task);
}