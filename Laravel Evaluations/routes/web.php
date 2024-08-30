<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', 'HomeController@index')->middleware('auth');

Route::get('/dev/{id}', function($id){
    if ($_SERVER['REMOTE_ADDR'] != env('DEV_IP') && $_SERVER['REMOTE_ADDR'] !== '::1' && $_SERVER['REMOTE_ADDR'] !== '127.0.0.1'){
        abort(404);
    }
    Auth::loginUsingId($id);
    return redirect('/');
});

Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout')->middleware('auth');
Route::get('logout', function () {
    return redirect('/');
});

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');



Route::group(['prefix' => 'home'], function () {
    Route::get('/', 'HomeController@index')->name('home')->middleware('auth');

    Route::get('/get-revenue-per-language', 'HomeController@getRevenuePerLanguage')
        ->name('getRevenuePerLanguage')
        ->middleware('role:master|administrator')
        ->middleware('auth');

    Route::get('/get-revenue-per-day', 'HomeController@getRevenuePerDay')
        ->name('getRevenuePerDay')
        ->middleware('role:master|administrator')
        ->middleware('auth');
});

/*Test routes*/
//Route::get('/users', 'UserController@getAllUsers')->middleware('auth');
//Route::get('/getRecover', 'ForgotPasswordController@getRecover');
/*End test routes*/

Route::get('/question-time-metadata', function(){
    $timeLimit = request()->get('t');
    return \Carbon\Carbon::now()->diffInSeconds(\Carbon\Carbon::parse($timeLimit)) * 1000;
});

/* Debug routes */
  //Route::get('/debug-low', 'HomeController@debugLow')->name('debug')->middleware('auth');
/* End debug routes */

/*Group routes*/
Route::get('/groups', 'GroupController@index')->name('groups')->middleware('auth');
Route::get('/groups-data', 'GroupController@getTableData')->name('groups-data')->middleware('auth');
Route::prefix('group')->group(function () {
    Route::post('create', 'GroupController@create')->name('group-create')->middleware('auth')->middleware('canAtLeast:group.create');
    Route::get('create-form', 'GroupController@createForm')->name('group-create-form')->middleware('auth')->middleware('canAtLeast:group.create');;
    Route::post('update/{id}', 'GroupController@update')->name('group-update')->middleware('auth');
    Route::get('update-form/{id}', 'GroupController@updateForm')->name('group-update-form')->middleware('auth');
    Route::get('delete/{id}', 'GroupController@delete')->name('group-delete')->middleware('auth');
//custom
    Route::get('get-assessors', 'GroupUserController@getAssessors')->name('group-get-assessors');

    Route::prefix('{group}')->group(function () {
        Route::get('users', 'GroupUserController@index')->name('group-users')->middleware('auth');
        Route::get('users-data', 'GroupUserController@getTableData')->name('group-users-data')->middleware('auth');
        Route::post('users-data', 'GroupUserController@getTableData')->name('group-users-data')->middleware('auth');

//        Route::get('users', 'GroupController@viewUsers')->name('group-view-users');

        Route::prefix('user')->group(function () {
            Route::post('create', 'GroupUserController@create')->name('group-user-create')->middleware('auth')->middleware('canAtLeast:user.create');
            Route::get('create-form', 'GroupUserController@createForm')->name('group-user-create-form')->middleware('auth')->middleware('canAtLeast:user.create');
            Route::post('update/{user}', 'GroupUserController@update')->name('group-user-update')->middleware('auth')->middleware('canAtLeast:user.update');
            Route::get('update-form/{user}', 'GroupUserController@updateForm')->name('group-user-update-form')->middleware('auth')->middleware('canAtLeast:user.update');
            Route::get('delete/{user}', 'GroupUserController@delete')->name('group-user-delete')->middleware('auth');
        });
    });
});
/*End Group routes*/

/*Paper Type routes*/
Route::prefix('paper')->group(function () {
    Route::get('types', 'PaperTypeController@index')->name('paper-types')->middleware('auth')->middleware('role:master|administrator');
    Route::get('types-data', 'PaperTypeController@getTableData')->name('paper-types-data')->middleware('auth')->middleware('role:master|administrator');

    Route::prefix('type')->group(function () {
        Route::post('create', 'PaperTypeController@create')->name('paper-type-create')->middleware('auth')->middleware('role:master|administrator');
        Route::get('create-form', 'PaperTypeController@createForm')->name('paper-type-create-form')->middleware('auth')->middleware('role:master|administrator');
        Route::post('update/{id}', 'PaperTypeController@update')->name('paper-type-update')->middleware('auth')->middleware('role:master|administrator');
        Route::get('update-form/{id}', 'PaperTypeController@updateForm')->name('paper-type-update-form')->middleware('auth')->middleware('role:master|administrator');
        Route::get('delete/{id}', 'PaperTypeController@delete')->name('paper-type-delete')->middleware('auth')->middleware('role:master|administrator');
    });
});
/*End Paper Type routes*/

/*Task Status routes*/
Route::prefix('task')->group(function () {
    Route::get('statuses', 'TaskStatusController@index')->name('task-statuses')->middleware('auth')->middleware('role:master|administrator');
    Route::get('statuses-data', 'TaskStatusController@getTableData')->name('task-statuses-data')->middleware('auth')->middleware('canAtLeast:project.create_tasks');
    Route::post('status/create', 'TaskStatusController@create')->name('task-status-create')->middleware('auth')->middleware('canAtLeast:project.create_tasks');
    Route::get('status/create-form', 'TaskStatusController@createForm')->name('task-status-create-form')->middleware('auth')->middleware('canAtLeast:project.create_tasks');
    Route::post('status/update/{id}', 'TaskStatusController@update')->name('task-status-update')->middleware('auth')->middleware('canAtLeast:task.update');
    Route::get('status/update-form/{id}', 'TaskStatusController@updateForm')->name('task-status-update-form')->middleware('auth')->middleware('canAtLeast:task.update');
    Route::get('status/delete/{task}', 'TaskStatusController@delete')->name('task-status-delete')->middleware('auth');
});
/*End Paper Type routes*/

/*Task Status routes*/
Route::prefix('project')->group(function () {
    Route::get('/projects', 'ProjectController@getProjectPage')->middleware('canAtLeast:menu.view_projects')->middleware('auth');
    Route::get('/datatables', 'ProjectController@getProjectDatatable')->middleware('canAtLeast:menu.view_projects')->middleware('auth');
    Route::get('/create-form', 'ProjectController@createForm')->middleware('canAtLeast:project.create')->middleware('auth');
    Route::post('/create', 'ProjectController@createProject')->middleware('canAtLeast:project.create')->middleware('auth');
    Route::get('/getParticipants/{parentId}', 'ProjectController@getParticipants')->middleware('auth');
    Route::get('/getClientProjects/{clientId}', 'ProjectController@getClientProjects')->middleware('auth');
    Route::post('/createClient', 'ProjectController@createClientByEmail')->middleware('auth');
    Route::post('/updateClient/{id}', 'ProjectController@updateClient')->middleware('auth');
    Route::get('/{projectId}', 'ProjectController@getProjectById')->middleware('auth');
    Route::post('/update/{id}', 'ProjectController@updateProject')->middleware('auth')->middleware('canAtLeast:project.create');
    Route::get('delete/{id}', 'ProjectController@deleteProject')->middleware('auth');
    Route::post('/updateParticipants/{user_id}/project/{project_id}', 'ProjectController@updateProjectParticipants')->middleware('auth');
    Route::get('/getParticipantsProject/{id}', 'ProjectController@getParticipantsProjects')->middleware('auth');
    Route::get('/getProjectsParticipatings/{user_id}/client/{client_id}', 'ProjectController@getProjectsParticipatings')->middleware('auth');
    //->middleware('canAtLeast:project.delete,project.delete_own')
    Route::get('/search/tasks', 'TaskController@index')->name('project-tasks')->middleware('canAtLeast:project.view_task')->middleware('auth');
    Route::post('/all/taskss-data', 'TaskController@getTableDataSearch')->name('task-statuses-data')->middleware('auth');

    Route::post('tasks-update-batch', 'TaskController@updateBatch')->name('tasks-update-batch')->middleware('auth')->middleware('role:master|administrator|client|css|recruiter');
    Route::delete('tasks-delete-batch', 'TaskController@deleteBatch')->name('tasks-delete-batch')->middleware('auth')->middleware('role:master|administrator|client|css|recruiter');

    Route::post('verify-test-taker', 'TaskController@verifyTestTaker')->name('verify-test-taker')->middleware('auth');
    Route::post('duplicate-task', 'TaskController@duplicateTask')->name('duplicate-task')->middleware('auth');
    
    Route::prefix('{project}')->group(function () {
        Route::get('tasks', 'TaskController@index')->name('project-tasks')->middleware('canAtLeast:project.view_task')->middleware('auth');
        Route::get('tasks-data', 'TaskController@getTableData')->name('task-statuses-data')->middleware('auth');
        Route::post('tasks-data', 'TaskController@getTableData')->name('task-statuses-data')->middleware('auth');
        
        Route::get('filter-by-name/{name}', 'TaskController@projectsByName')->name('project-by-name')->middleware('auth');

        /**
         * @deprecated 21-10-2019
         */
        Route::get('filter-tasks-by-name/{name}', 'TaskController@tasksByName')->name('tasks-by-name')->middleware('auth');


        Route::get('/export-tasks-xls', 'TaskController@exportTasksXLS')->middleware('auth');
        Route::get('/export-grades-csv', 'TaskController@exportGradesCSV')->middleware('auth');
        Route::post('/import-tasks-xls', 'TaskController@importTasksXLS')->middleware('canAtLeast:project.create_tasks')->middleware('auth');

        Route::prefix('task')->group(function () {
            Route::post('create', 'TaskController@create')->name('task-status-create')->middleware('canAtLeast:project.create_tasks')->middleware('auth');
            Route::get('create-form', 'TaskController@createForm')->name('task-status-create-form')->middleware('canAtLeast:project.create_tasks')->middleware('auth');
            Route::post('update/{task}', 'TaskController@update')->name('task-status-update')->middleware('canAtLeast:task.update')->middleware('auth');
            Route::get('update-form/{task}', 'TaskController@updateForm')->name('task-status-update-form')->middleware('canAtLeast:task.update')->middleware('auth');
            Route::get('update-form-data/{task}', 'TaskController@updateFormData')->name('task-status-update-form-data')->middleware('auth');
            Route::get('delete/{task}', 'TaskController@delete')->name('task-status-delete')->middleware('canAtLeast:task.update')->middleware('auth');
        });
    });
    Route::get('language-assessors/{projectType}/{language_id}/{native?}', 'TaskController@assessorsByLanguage')->name('task-assessors-by-language')->middleware('auth');
    Route::get('language-assessors/{projectType}/{language_id}/native/{native}', 'TaskController@assessorsByLanguageAndNative')->name('task-assessors-by-language-and-native')->middleware('auth');
});
/*End Paper Type routes*/

/*Multilanguage routes*/
Route::get('/language/{language}', 'UserController@language')->name("language")->middleware('auth');
/*End Multilanguage routes*/

Route::group(['prefix' => 'user'], function () {
    Route::get('profile', 'UserController@getAccount')->middleware('auth');
    Route::post('update', 'UserController@updateProfileLogin')->middleware('auth');

    Route::get('/update-form/{id}', 'admin\AdministratorController@getUpdateUserForm')->middleware('auth');
    Route::post('/update/{id}', 'admin\AdministratorController@updateUser')->middleware('auth');
});

### ROLE
Route::prefix('role')->group(function () {
    Route::post('create', 'admin\RoleController@create')->name('role-create')->middleware('auth')->middleware('role:master');
    Route::get('create-form', 'admin\RoleController@createForm')->name('role-create-form')->middleware('auth')->middleware('role:master');
    Route::get('delete/{id}', 'admin\RoleController@delete')->name('role-delete')->middleware('auth')->middleware('role:master');
    Route::post('update/{id}', 'admin\RoleController@update')->name('role-update')->middleware('auth')->middleware('role:master');
    Route::get('update-form/{id}', 'admin\RoleController@updateForm')->name('role-update-form')->middleware('auth')->middleware('role:master');
});


### PERMISSION
Route::prefix('permission')->group(function () {
    Route::post('create', 'admin\PermissionController@createPermission')->name('permission-create')->middleware('auth')->middleware('role:master');
    Route::get('create-form', 'admin\PermissionController@createPermissionForm')->name('permission-create-form')->middleware('auth')->middleware('role:master');
    Route::get('delete/{id}', 'admin\PermissionController@deletePermission')->name('permission-delete')->middleware('auth')->middleware('role:master');
    Route::post('update/{id}', 'admin\PermissionController@updatePermission')->name('permission-update')->middleware('auth')->middleware('role:master');
    Route::get('update-form/{id}', 'admin\PermissionController@updatePermissionForm')->name('permission-update-form')->middleware('auth')->middleware('role:master');
});

### PROJECT TYPES
Route::prefix('projectTypes')->group(function () {
    Route::post('create', 'admin\ProjectTypeController@createProjectType')->name('projectType-create')->middleware('auth')->middleware('role:master');
    Route::get('create-form', 'admin\ProjectTypeController@createProjectTypeForm')->name('projectType-create-form')->middleware('auth')->middleware('role:master');
    Route::get('delete/{id}', 'admin\ProjectTypeController@deleteProjectType')->name('projectType-delete')->middleware('auth')->middleware('role:master');
    Route::post('update/{id}', 'admin\ProjectTypeController@updateProjectType')->name('projectType-update')->middleware('auth')->middleware('role:master');
    Route::get('update-form/{id}',
        'admin\ProjectTypeController@updateProjectTypeForm')->name('projectType-update-form')->middleware('auth')->middleware('role:master');
});

### USER
Route::prefix('master')->group(function () {
    Route::get('delete/{id}', 'admin\AdministratorController@deleteUser')->name('master-delete')->middleware('auth')->middleware('role:master');
    Route::post('update/{id}', 'admin\AdministratorController@updateUser')->name('master-update')->middleware('auth')->middleware('role:master');
    Route::get('update-form/{id}', 'admin\AdministratorController@updateUserForm')->name('master-update-form')->middleware('auth')->middleware('role:master');
});


Route::get('/assessor/page/{id}', 'AssessorController@getAssessorPage')->middleware('auth');
Route::get('/assessor/{assessorId}/refuse/{taskId}', 'AssessorController@refuseTask')->middleware('auth');
Route::get('/assessor/{assessorId}/inactivity', 'AssessorController@getInactivity')->middleware('auth');
Route::post('/assessor/{assessorId}/update-inactivity', 'AssessorController@updateInactivity')->middleware('auth');

Route::group(['prefix' => 'admin'], function () {
    Route::get('create', 'admin\AdministratorController@getCreateUser')->middleware('role:master|administrator')->middleware('auth');
    Route::get('/create/manual',
        'admin\AdministratorController@getCreateUser')->middleware('canAtLeast:user.create')->middleware('auth');
    Route::get('/create/automatic',
        'admin\AdministratorController@getCreateUserAutomatically')->middleware('canAtLeast:user.create')->middleware('auth');
    Route::post('/createUserManually', 'admin\AdministratorController@createUserManually')->middleware('canAtLeast:user.create')->middleware('auth');
    Route::post('/createUserAutomatically', 'admin\AdministratorController@createUserAutomatically')->middleware('canAtLeast:user.create')->middleware('auth');

    Route::get('/projectTypes', 'admin\AdministratorController@getProjectTypes')->middleware('role:master|administrator')->middleware('auth');
    Route::get('/projectTypes/datatables', 'admin\AdministratorController@getProjectTypesDatatable')->middleware('role:master|administrator')->middleware('auth');

    Route::get('/tests', 'admin\AdministratorController@getTestsPage')->middleware('role:master|administrator')->middleware('auth');
    Route::get('/tests/datatables', 'admin\AdministratorController@getTestsDatatable')->middleware('role:master|administrator')->middleware('auth');
    Route::post('/tests/update/{id}', 'admin\AdministratorController@updateTest')->middleware('role:master|administrator')->middleware('auth');

    Route::get('/task-updates', 'admin\AdministratorController@getTaskUpdates')->middleware('role:master|administrator')->middleware('auth');
    Route::get('/task-updates/datatables', 'admin\AdministratorController@getTaskUpdatesDatatable')->middleware('role:master|administrator')->middleware('auth');
    Route::get('/task-updates/update-form/{id}', 'admin\AdministratorController@getTaskUpdatesForm')->middleware('role:master|administrator')->middleware('auth');
    Route::post('/task-updates/update/{id}', 'admin\AdministratorController@updateTaskUpdate')->middleware('role:master|administrator')->middleware('auth');

    Route::get('/permissions', 'admin\AdministratorController@getPermissions')->middleware('role:master|administrator')->middleware('auth');
    Route::get('/permissions/datatables', 'admin\AdministratorController@getPermissionDatatable')->middleware('role:master|administrator')->middleware('auth');

    Route::get('/roles', 'admin\AdministratorController@getRoles')->middleware('role:master|administrator')->middleware('auth');
    Route::get('/roles/datatables', 'admin\AdministratorController@getRolesDatatable')->middleware('role:master|administrator')->middleware('auth');

    Route::get('/roles/{slug}', 'admin\AdministratorController@getRolesSlug')->middleware('auth')->middleware('canAtLeast:user.create');
    Route::get('/roles/{id}/datatable', 'admin\AdministratorController@getRoleDatatable')->middleware('auth')->middleware('canAtLeast:user.create');
    Route::get('/roles/{id}/datatable/{parentId}', 'admin\AdministratorController@getClientParticipants')->middleware('auth')->middleware('canAtLeast:user.create');
    Route::get('/roles/{slug}', 'admin\AdministratorController@getRolesSlug')->middleware('canAtLeast:user.create')->middleware('auth');
    Route::get('/roles/{id}/datatable', 'admin\AdministratorController@getRoleDatatable')->middleware('canAtLeast:user.create')->middleware('auth');
    Route::get('/roles/{id}/datatable/{parentId}/{filter}', 'admin\AdministratorController@getClientParticipants')->middleware('canAtLeast:user.create')->middleware('auth');
    Route::get('/roles/assessors/datatable-groups', 'admin\AdministratorController@getGroupAllAssessors')->middleware('canAtLeast:user.create')->middleware('auth');
    Route::get('/roles/{group}/datatable-groups', 'admin\AdministratorController@getGroupAssessors')->middleware('canAtLeast:user.create')->middleware('auth');

    Route::post('/createClientParticipant', 'admin\AdministratorController@createClientParticipant')->middleware('auth')->middleware('canAtLeast:user.create');
    Route::get('/removeClientParticipant/{id}', 'admin\AdministratorController@removeClientParticipant')->middleware('auth')->middleware('canAtLeast:user.create');

    Route::get('/getCssRecruitersDatatable/{filter}', 'admin\AdministratorController@getCssRecruitersDatatable')->middleware('auth')->middleware('canAtLeast:user.create');
    Route::get('/getTdsDatatable/{filter}', 'admin\AdministratorController@getTdsDatatable')->middleware('auth')->middleware('canAtLeast:user.create');

    Route::post('/createUserByRoleId/{id}', 'admin\AdministratorController@createUserByRoleId')->middleware('auth')->middleware('canAtLeast:user.create');
    Route::get('/removeUser/{id}', 'admin\AdministratorController@removeUser')->middleware('auth')->middleware('canAtLeast:user.update');
    Route::get('/activateUser/{id}', 'admin\AdministratorController@activateUser')->middleware('auth')->middleware('canAtLeast:user.update');

    Route::post('/addUserTemporaryDisabled/{id}', 'admin\AdministratorController@addUserTemporaryDisabled')->middleware('auth')->middleware('canAtLeast:user.update');
    Route::post('/removeUserTemporaryDisabled/{id}', 'admin\AdministratorController@removeUserTemporaryDisabled')->middleware('auth')->middleware('canAtLeast:user.update');

    Route::get('/users/datatable', 'admin\AdministratorController@getUsersDatatable')->middleware('auth')->middleware('canAtLeast:user.create,user.update');
    Route::get('/usersCrud', 'admin\AdministratorController@getUsersPage')->middleware('auth')->middleware('canAtLeast:user.create,user.update');
    Route::get('/exportUsers', 'admin\AdministratorController@exportUsers')->middleware('auth')->middleware('canAtLeast:user.create,user.update');

    Route::get('/getClientDetails', 'admin\AdministratorController@getClientDetails')->middleware('canAtLeast:project.create')->middleware('auth');
    Route::post('/createClient', 'admin\AdministratorController@createClient')->middleware('auth');

    Route::get('/logs/getAll', 'admin\LogController@getAll')->middleware('auth')->middleware('role:master|administrator');
    Route::get('/logs/getData/{filter}', 'admin\LogController@getLogsData')->middleware('auth')->middleware('role:master|administrator');

    Route::post('/createTestType', 'admin\TestsManagerController@createTestType')->middleware('auth')->middleware('role:master|administrator');

    Route::post('/sendMailToLanguageAuditManager', 'admin\AdministratorController@sendMailToLanguageAuditManager')->middleware('auth')->middleware('role:assessor|client');

    //test cron test testReminder
    Route::get('/testReminder', 'admin\TestsManagerController@reminderUpdateTask')->middleware('auth')->middleware('role:master|administrator');

    ### user crud - edit
    Route::get('/getUserDetails/{id}', 'admin\AdministratorController@getUserDetails')->middleware('auth')->middleware('role:master|administrator');
    Route::post('/updateUserDetails/{id}', 'admin\AdministratorController@updateUserDetails')->middleware('auth')->middleware('role:master|administrator');

    Route::prefix('tests')->group(function () {
        Route::get('list', 'admin\TestsManagerController@getTestsPage')->middleware('role:master|administrator')->middleware('auth');
        Route::get('{languagePaperType}', 'admin\TestsManagerController@getLanguageTestTypePage')->middleware('role:master|administrator')->middleware('auth');
    });
    Route::prefix('questions')->group(function () {

        Route::get('datatable/{id}', 'admin\TestsManagerController@getTestQuestions')->middleware('role:master|administrator')->middleware('auth');
        Route::get('datatable/{id}/choices', 'admin\TestsManagerController@getTestQuestionsChoices')->middleware('role:master|administrator')->middleware('auth');

        Route::post('createWritingQuestion', 'admin\TestsManagerController@createWritingQuestion')->middleware('role:master|administrator')->middleware('auth');
        Route::post('createReadingQuestion', 'admin\TestsManagerController@createReadingQuestion')->middleware('role:master|administrator')->middleware('auth');
        Route::post('createLanguageUseQuestion', 'admin\TestsManagerController@createLanguageUseQuestion')->middleware('role:master|administrator')->middleware('auth');
        Route::post('createListeningQuestion', 'admin\TestsManagerController@createListeningQuestion')->middleware('role:master|administrator')->middleware('auth');
        Route::post('createLanguageQuestion', 'admin\TestsManagerController@createLanguageQuestion')->middleware('role:master|administrator')->middleware('auth');
        Route::post('createQuestionChoice', 'admin\TestsManagerController@createQuestionChoice')->middleware('role:master|administrator')->middleware('auth');

        Route::get('choice/{action}/{id}', 'admin\TestsManagerController@updateChoiceStatus')->middleware('role:master|administrator')->middleware('auth')->where('action', '\b(?:activate|deactivate)\b');
        Route::get('choice/{id}', 'admin\TestsManagerController@getQuestionChoice')->middleware('role:master|administrator')->middleware('auth');
        Route::post('choice/{id}', 'admin\TestsManagerController@updateQuestionChoice')->middleware('role:master|administrator')->middleware('auth');
        Route::get('{id}/choices', 'admin\TestsManagerController@getQuestionChoices')->middleware('role:master|administrator')->middleware('auth');

        Route::get('testType/{action}/{id}', 'admin\TestsManagerController@updateTestTypeStatus')->middleware('role:master|administrator')->middleware('auth')->where('action', '\b(?:activate|deactivate)\b');

        Route::get('{id}', 'admin\TestsManagerController@getQuestion')->middleware('role:master|administrator')->middleware('auth');
        Route::post('{id}', 'admin\TestsManagerController@updateQuestion')->middleware('role:master|administrator')->middleware('auth');
        Route::get('force-delete/{id}', 'admin\TestsManagerController@forceDeleteQuestion')->middleware('role:master|administrator')->middleware('auth');
        Route::get('{action}/{id}', 'admin\TestsManagerController@updateQuestionStatus')->middleware('role:master|administrator')->middleware('auth')->where('action', '\b(?:activate|deactivate)\b');

    });

    Route::prefix('mails')->group(function () {
        Route::get('/test', 'admin\MailsManagerController@test')->middleware('role:master|administrator')->middleware('auth');
        Route::get('/', 'admin\MailsManagerController@index')->middleware('role:master|administrator')->middleware('auth');
        Route::get('{id}', 'admin\MailsManagerController@getMail')->middleware('role:master|administrator')->middleware('auth');
        Route::post('{id}', 'admin\MailsManagerController@updateMail')->middleware('role:master|administrator')->middleware('auth');

    });

    Route::get('/settings', 'admin\AdministratorController@getSettings')->middleware('auth')->middleware('role:master|administrator');
    Route::get('/settings/datatable', 'admin\AdministratorController@getSettingsDatatable')->middleware('auth')->middleware('role:master|administrator');
    Route::get('/settings/{id}', 'admin\AdministratorController@loadSetting')->middleware('auth')->middleware('role:master|administrator');
    Route::post('/setting/updateSetting/{id}', 'admin\AdministratorController@updateSetting')->middleware('auth')->middleware('role:master|administrator');

    # Languages
    Route::get('/languages', 'admin\LanguagesController@index')->middleware('auth')->middleware('role:master|administrator');
    Route::get('/languages/all', 'admin\LanguagesController@getAllLanguages')->middleware('auth')->middleware('role:master|administrator');
    Route::post('/languages/add', 'admin\LanguagesController@createLanguage')->middleware('auth')->middleware('role:master|administrator');
    Route::post('/languages/edit', 'admin\LanguagesController@editLanguage')->middleware('auth')->middleware('role:master|administrator');
    Route::get('/languages/get/{id}', 'admin\LanguagesController@getLanguage')->middleware('auth')->middleware('role:master|administrator');

    # Prices
    Route::get('/prices', 'admin\PricesController@index')->middleware('auth')->middleware('role:master|administrator');
    Route::post('/prices/save-default', 'admin\PricesController@saveDefault')->middleware('auth')->middleware('role:master|administrator');
    Route::get('/prices/get-project-prices', 'admin\PricesController@getProjectPrices')->middleware('auth')->middleware('role:master|administrator');
});

### Change currency
Route::get('/currency/{currency}', ['uses' => 'HomeController@changeCurrency'])->middleware('auth');

### CEFR
Route::get('cefr', 'ReferenceController@index')->name('cefr')->middleware('auth')->middleware('role:master|administrator');
Route::get('cefr-data', 'ReferenceController@getTableData')->name('cefr-data')->middleware('auth')->middleware('role:master|administrator');
Route::post('cefr-data', 'ReferenceController@getTableData')->name('cefr-data')->middleware('auth')->middleware('role:master|administrator');

Route::prefix('cefr')->group(function () {
    Route::post('update/{reference}', 'ReferenceController@update')->name('cefr-update')->middleware('auth')->middleware('role:master|administrator');
    Route::get('update-form/{reference}', 'ReferenceController@updateForm')->name('cefr-update-form')->middleware('auth')->middleware('role:master|administrator');
    Route::get('update-form-data/{reference}', 'ReferenceController@updateFormData')->name('cefr-update-form-data')->middleware('auth')->middleware('role:master|administrator');
    Route::get('delete/{reference}', 'ReferenceController@delete')->name('cefr-delete')->middleware('auth')->middleware('role:master|administrator');
});

###Tests
Route::group(['prefix' => 'test'], function () {
    Route::get('/instructions/{hash}', 'TestController@getTestInstructionsPage');
    Route::get('/{hash}', 'TestController@getTestPage');
    // Test for individual test type
    Route::get('/instructions/{hash}/{testType}', 'TestController@getTestInstructionsForPaperPage');
    Route::get('/demo/{hash}/{testType}/{step}', 'TestController@getTestDemoForPaperPage');
    Route::get('/{hash}/{testType}', 'TestController@getTestForPaperPage');

    Route::post('/submitWritingAnswer', 'TestController@submitWriting');
    Route::post('/submitReadingAnswer', 'TestController@submitReading');
    Route::post('/submitListeningAnswer', 'TestController@submitListening');
    Route::post('/submitLanguageUseNewAnswer', 'TestController@submitLanguageUseNew');
    Route::post('/submitLanguageUseAnswer', 'TestController@submitLanguageUse');
    Route::post('/insertCurrentAudio', 'TestController@insertCurrentAudio');
});

###Tasks
Route::get('tasks', 'TaskController@tasks')->name('tasks')->middleware('canAtLeast:project.view_task')->middleware('auth');

Route::group(['prefix' => 'task'], function () {
    Route::get('/{task}', 'TaskController@getTaskPage')->name('task')->middleware('canAtLeast:project.view_task')->middleware('auth');
    Route::post('/{task}/update', 'TaskController@updateField')->name('task-update-field')->middleware('auth');
    Route::get('/{task}/refuse', 'TaskController@refuse')->name('task-refuse')->middleware('canAtLeast:task.refuse')->middleware('auth');
    Route::post('/{task}/assessments', 'TaskController@assessments')->name('task-assessments')->middleware('auth');
    Route::post('/{task}/request-updates', 'TaskController@requestUpdates')->name('task-request-updates')->middleware('auth');
    Route::get('/{task}/resend-mail', 'TaskController@resendMail')->name('task-resend-mail')->middleware('auth');
    Route::get('/{task}/reset', 'TaskController@reset')->name('task-reset')->middleware('canAtLeast:test.reset_online_test')->middleware('auth');
    Route::get('/{task}/reset-link', 'TaskController@resetLink')->name('task-reset-link')->middleware('canAtLeast:test.reset_online_test')->middleware('auth');
    Route::get('/{task}/downloadAsPdf', 'TaskController@downloadAsPdf')->middleware('auth');
    Route::get('/{task}/downloadAsDocx', 'TaskController@downloadAsDocx')->middleware('auth');
    Route::get('/{paper}/reset-report', 'TaskController@resetReport')->name('paper-reset-report')->middleware('canAtLeast:test.reset_online_test')->middleware('auth');
    Route::get('/{paper}/reset-test', 'TaskController@resetTest')->name('paper-reset-test')->middleware('canAtLeast:test.reset_online_test')->middleware('auth');
    Route::post('/{paper}/delete-test', 'TaskController@deleteTest')->name('delete-test')->middleware('canAtLeast:task.update')->middleware('auth');
    Route::post('/{log}/delete-log', 'TaskController@deleteLog')->name('delete-test')->middleware('auth')->middleware('role:master|administrator');
    Route::post('/{task}/upload-attachment', 'TaskController@uploadAttachment')->name('upload-attachment')->middleware('role:master|administrator|tds}client|css|assessor')->middleware('auth');
    Route::post('/attachment/{attachment}/delete-attachment', 'TaskController@deleteAttachment')->name('delete-attachment')->middleware('role:master|administrator|css|client')->middleware('auth');
    Route::get('/{paper}/test-table', 'TaskController@getTestTable')->middleware('auth')->middleware('role:master|administrator');
    Route::post('/task-batch-get-test-types', 'TaskController@getBatchTestTypes')->middleware('canAtLeast:task.update')->middleware('auth');
});


### Tests Results
Route::group(['prefix' => 'results'], function () {
    Route::get('/', 'TestsResultsController@viewAll')->middleware('auth')->middleware('role:master|administrator');
    Route::get('/datatable', 'TestsResultsController@getDatatable')->middleware('auth')->middleware('role:master|administrator');
    Route::get('/test/{id}/result', 'TestsResultsController@viewTestResult')->middleware('auth')->middleware('canAtLeast:test.view_answers');
    Route::get('/downloadPDF/{test_id}', 'TestsResultsController@downloadPDF')->middleware('auth')->middleware('canAtLeast:test.view_answers');
});

### Item Statistics
Route::group(['prefix' => 'item-statistics'], function () {
    Route::get('/', 'ItemStatisticsController@viewAll')->middleware('auth')->middleware('role:master|administrator');
    Route::get('/get-statistics', 'ItemStatisticsController@getStatistics')->middleware('auth')->middleware('role:master|administrator');
});

### Billing
Route::group(['prefix' => 'billing'], function () {
    Route::get('/', 'BillingController@index')->middleware('auth')->middleware('role:master|administrator');
    Route::get('/get-client-projects/{id}', 'BillingController@getClientProjects')->middleware('auth')->middleware('role:master|administrator');
    Route::get('/generate-invoice/{id}', 'BillingController@generateInvoice')->middleware('auth')->middleware('role:master|administrator');
    Route::get('/generate-invoice-preview/{id}', 'BillingController@generateInvoicePreview')->middleware('auth')->middleware('role:master|administrator');
    Route::get('/get-billing-information', 'BillingController@getBillingInformation')->middleware('auth')->middleware('role:master|administrator');
});

### Billing
Route::group(['prefix' => 'invoices'], function () {
    Route::get('/', 'InvoicesController@index')->middleware('auth')->middleware('role:master|administrator');
    Route::get('/view-file/{fileName}', 'InvoicesController@viewFile')->middleware('auth')->middleware('role:master|administrator');
    Route::get('/all', 'InvoicesController@getAllInvoices')->middleware('auth')->middleware('role:master|administrator');
    Route::get('/export-annex/{id}', 'InvoicesController@exportAnnex')->middleware('auth')->middleware('role:master|administrator');
});