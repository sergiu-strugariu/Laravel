<?php

namespace App\Http\Controllers;

use App\Models\PaperType;
use App\Repositories\PaperTypeRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PaperTypeController extends Controller
{
    private $paperTypeRepository;

    /**
     * PaperTypeController constructor.
     *
     * @param \App\Repositories\PaperTypeRepository $paperTypeRepository
     */
    public function __construct(PaperTypeRepository $paperTypeRepository)
    {
        $this->paperTypeRepository = $paperTypeRepository;
    }

    /**
     * Display a listing of paper types.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('paper.type/index');
    }

    /**
     * Ajax function to populate data table.
     */
    public function getTableData()
    {
        return DataTables::of(PaperType::query())->make(true);
    }

    /**
     * Generate create form for Ajax request.
     */
    public function createForm()
    {
        return view('paper.type/_form')->render();
    }

    /**
     * Create a new paper type.
     *
     * @param  \Illuminate\Http\Request $request
     * @return PaperType
     */
    public function create(Request $request)
    {
        return json_encode($this->paperTypeRepository->create($request->toArray()));
    }

    /**
     * Display the specified paper type.
     *
     * @param  int $id
     * @return PaperType
     */
    public function view($id)
    {
        return $this->paperTypeRepository->getById($id);
    }

    /**
     * Generate update form for Ajax request.
     */
    public function updateForm($id)
    {
        $paperType = $this->paperTypeRepository->getById($id);

        return view('paper.type/_form', compact('paperType'))->render();
    }

    /**
     * Update the specified paper type.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return bool
     */
    public function update(Request $request, $id)
    {
        return json_encode($this->paperTypeRepository->getById($id)->update($request->toArray()));
    }

    /**
     * Remove the specified paper type.
     *
     * @param  int $id
     * @return bool|null
     */
    public function delete($id)
    {
        return json_encode($this->paperTypeRepository->getById($id)->delete());
    }
}
