<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\User;
use App\Services\EmailService;

interface UserInterface
{
    function getAll();

    function getById($id);

    function create(array $attributes);

    function update($id, array $attributes);

    function delete($id);

    function getByRole($roleSlug, $id = null);

    function getByRoleAndParentIdNull($roleSlug);

    function getClients();

    function getWithoutRole($roleSlug);

    function getByParentId($parentId);

    function generateRandomPassword();

    function untrash($id);

    function getUserByRoleId($id);

    function addUserTemporaryDisabled($id, $params);

    function removeUserTemporaryDisabled($id);

    function sendTaskMailToAdmins(EmailService $emailService, Task $task, Task $verifyTask, $params);

    function sendTaskGroupEmptyMailToAdmins(EmailService $emailService, Task $task, $params);

    function getByEmail($email);
}