<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\RoleInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class RoleController extends Controller
{
    /**
     * @var $roleRepository
     */
    private $roleRepository;

    public function __construct(RoleInterface $roleInterfaece)
    {
        $this->roleRepository = $roleInterfaece;
    }


    /**
     * Generate create form for Ajax request.
     */
    public function createForm()
    {
        return view('roles/_form')->render();
    }

    /**
     * Create a new role.
     *
     * @param  \Illuminate\Http\Request $request
     * @return Role
     */
    public function create(Request $request)
    {
        return json_encode($this->roleRepository->create($request->toArray()));
    }


    /**
     * Remove the specified role.
     *
     * @param  int $id
     * @return bool|null
     */
    public function delete($id)
    {
        return json_encode($this->roleRepository->getById($id)->delete());
    }

    /**
     * Generate update form for Ajax request.
     */
    public function updateForm($id)
    {
        $role = $this->roleRepository->getById($id);

        return view('roles/_form', compact('role'))->render();
    }

    /**
     * Update the specified role.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return bool
     */
    public function update(Request $request, $id)
    {
        return json_encode($this->roleRepository->getById($id)->update($request->toArray()));
    }


}