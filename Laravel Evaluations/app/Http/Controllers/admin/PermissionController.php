<?php

namespace App\Http\Controllers\admin;

use App\Repositories\ModuleInterface;
use App\Repositories\PermissionInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PermissionController
{

    /**
     * @var $moduleRepository
     */
    private $moduleRepository;

    /**
     * @var $permissionRepository
     */
    private $permissionRepository;

    public function __construct(
        PermissionInterface $permissionInterface,
        ModuleInterface $moduleInterface)
    {
        $this->permissionRepository = $permissionInterface;
        $this->moduleRepository = $moduleInterface;
    }

    /**
     * Generate create form for Ajax request.
     */
    public function createPermissionForm()
    {
        $modules = $this->moduleRepository->getAll()->pluck('name', 'id');
        return view('permissions/_form', compact('modules'))->render();
    }

    /**
     * Create a new permission.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Role
     */
    public function createPermission(Request $request)
    {
        return json_encode($this->permissionRepository->create($request->toArray()));
    }

    /**
     * Remove the specified permission.
     *
     * @param  int $id
     * @return bool|null
     */
    public function deletePermission($id)
    {
        return json_encode($this->permissionRepository->getById($id)->delete());
    }

    /**
     * Generate update form for Ajax request.
     */
    public function updatePermissionForm($id)
    {
        $permission = $this->permissionRepository->getById($id);
        $modules = $this->moduleRepository->getAll()->pluck('name', 'id');
        return view('permissions/_form', compact('permission', 'modules'))->render();
    }

    /**
     * Update the specified role.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return bool
     */
    public function updatePermission(Request $request, $id)
    {
        return json_encode($this->permissionRepository->getById($id)->update($request->toArray()));
    }

}