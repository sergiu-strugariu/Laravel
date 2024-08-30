<?php

namespace App\Http\Controllers\admin;

use App\Models\Role;
use App\Repositories\ProjectTypeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProjectTypeController
{

    /**
     * @var $projectTypeRepository
     */
    private $projectTypeRepository;

    public function __construct(ProjectTypeInterface $projectTypeInterface)
    {

        $this->projectTypeRepository = $projectTypeInterface;
    }


    /**
     * Get project type creation form
     *
     * @return string
     */
    public function createProjectTypeForm()
    {
        return view('projectTypes/_form')->render();
    }

    /**
     * Create a new projectType.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Role
     */
    public function createProjectType(Request $request)
    {
        return json_encode($this->projectTypeRepository->create($request->toArray()));
    }

    /**
     * Remove the specified projectType.
     *
     * @param  int $id
     * @return bool|null
     */
    public function deleteProjectType($id)
    {
        return json_encode($this->projectTypeRepository->getById($id)->delete());
    }

    /**
     * Generate update form for Ajax request.
     */
    public function updateProjectTypeForm($id)
    {
        $projectType = $this->projectTypeRepository->getById($id);

        return view('projectTypes/_form', compact('projectType'))->render();
    }

    /**
     * Update the specified projectType.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return bool
     */
    public function updateProjectType(Request $request, $id)
    {
        return json_encode($this->projectTypeRepository->getById($id)->update($request->toArray()));
    }


}