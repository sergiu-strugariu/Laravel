<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 12/19/2017
 * Time: 2:40 PM
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Repositories\LogInterface;
use Yajra\DataTables\Facades\DataTables;

class LogController extends Controller
{
    /**
     * @var $logRepository
     */
    private $logRepository;

    /**
     * LogController constructor.
     * @param LogInterface $logInterface
     */
    public function __construct(LogInterface $logInterface)
    {
        $this->logRepository = $logInterface;
    }

    public function getAll()
    {
        $logTypes = Log::pluck('type', 'id')->unique();
        return view('logs.index', compact('logTypes'));
    }

    public function getLogsData($filter = null)
    {
        if($filter === "All"){
            return DataTables::of(Log::query())->make(true);
        }else{
            return DataTables::of(Log::where('type', $filter))->make(true);
        }


    }
}