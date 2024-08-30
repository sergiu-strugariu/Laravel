<?php

namespace App\Http\Controllers;

use App\Models\TaskStatus;
use App\Repositories\TaskStatusRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TaskStatusController extends Controller
{
    private $taskStatusRepository;

    /**
     * TaskStatusController constructor.
     *
     * @param \App\Repositories\TaskStatusRepository $taskStatusRepository
     */
    public function __construct(TaskStatusRepository $taskStatusRepository)
    {
        $this->taskStatusRepository = $taskStatusRepository;
    }

    /**
     * Display a listing of task statuses.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('task.status/index');
    }

    /**
     * Ajax function to populate data table.
     */
    public function getTableData()
    {
        return DataTables::of(TaskStatus::query())->make(true);
    }

    /**
     * Generate create form for Ajax request.
     */
    public function createForm()
    {
        return view('task.status/_form')->render();
    }

    /**
     * Create a new task status.
     *
     * @param  \Illuminate\Http\Request $request
     * @return TaskStatus
     */
    public function create(Request $request)
    {
        return json_encode($this->taskStatusRepository->create($request->toArray()));
    }

    /**
     * Display the specified task status.
     *
     * @param  int $id
     * @return TaskStatus
     */
    public function view($id)
    {
        return $this->taskStatusRepository->getById($id);
    }

    /**
     * Generate update form for Ajax request.
     */
    public function updateForm($id)
    {
        $taskStatus = $this->taskStatusRepository->getById($id);

        return view('task.status/_form', compact('taskStatus'))->render();
    }

    /**
     * Update the specified task status.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return bool
     */
    public function update(Request $request, $id)
    {
        return json_encode($this->taskStatusRepository->getById($id)->update($request->toArray()));
    }

    /**
     * Remove the specified task status.
     *
     * @param  int $id
     * @return bool|null
     */
    public function delete($id)
    {
        return json_encode($this->taskStatusRepository->getById($id)->delete());
    }
}
