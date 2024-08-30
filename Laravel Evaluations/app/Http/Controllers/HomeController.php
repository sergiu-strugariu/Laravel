<?php

namespace App\Http\Controllers;

use App\Http\Middleware\Language;
use App\Models\Group;
use App\Models\Paper;
use App\Models\RevenuePerDay;
use App\Models\RevenuePerLanguage;
use App\Models\Role;
use App\Models\TaskStatus;
use App\Repositories\GroupRepository;
use App\Repositories\PermissionInterface;
use App\Repositories\ProjectTypeInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\RoleInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Services\EmailService;
use App\Models\Task;
use View;

class HomeController extends Controller
{

    /**
     * @var $roleRepository
     */
    private $roleRepository;

    /**
     * @var $projectTypeRepository
     */
    private $projectTypeRepository;

    /**
     * @var $permissionRepository
     */
    private $permissionRepository;

    /**
     * @var $emailService
     */
    private $emailService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        RoleInterface $roleRepository,
        ProjectTypeInterface $projectTypeRepository,
        PermissionInterface $permissionRepository,
        EmailService $emailService
    )
    {

        $this->middleware('auth');
        $this->roleRepository = $roleRepository;
        $this->projectTypeRepository = $projectTypeRepository;
        $this->permissionRepository = $permissionRepository;
        $this->emailService = $emailService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $projectTypes = $this->projectTypeRepository->getAll();
        $permissions = $this->permissionRepository->getAll();
        $roles = $this->roleRepository->getAll();
        return view('home', compact('roles', 'projectTypes', 'permissions'));
    }

    /**
     *  Changes user currency
     *
     * @param $currency
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function changeCurrency($currency){

        $currency = strtoupper($currency);

        if( !in_array($currency, config('currencies.all')) ){
            return back();
        }

        Session::put('currency', $currency);

        return back();

    }

    public function getRevenuePerLanguage() {
        $revenue = RevenuePerLanguage::with('language')->get();
        $parsedData = [];
        foreach($revenue as $entry) {
            $languageId = $entry['language_id'];
            if (!isset($parsedData[$languageId])) {
                $parsedData[$languageId] = [
                    'label' => $entry['language']['name'],
                    'revenue' => 0,
                ];
            }

            $parsedData[$languageId]['revenue'] += $entry['revenue'];
        }

        return ['revenue' => $parsedData];
    }

    public function getRevenuePerDay(Request $request) {

        $dateEnd = $request->get('dateEnd', Carbon::now());

        $minDate = RevenuePerDay::orderBy('day')
            ->limit(1)
            ->get();

        $dateStart = $request->get('dateStart', ($minDate ? $minDate[0]['day'] : null));

//        $dateStart = $request->get('dateStart', Carbon::now()->subMonth(3)->format("d-m-Y 00:00:00"));


        $revenue = RevenuePerDay
            ::where("day", ">=", Carbon::parse($dateStart))
            ->where("day", "<=", Carbon::parse($dateEnd))
            ->orderBy('day')
            ->get();

        $parsedData = [];
        foreach($revenue as $entry) {
            $day = $entry['day'];
            if (!isset($parsedData[$day])) {
                $parsedData[$day] = [
                    'label' => $entry['day'],
                    'revenue' => 0,
                ];
            }

            $parsedData[$day]['revenue'] += $entry['revenue'];
        }

        return [
            'revenue' => $parsedData,
            'minDate' => ($minDate ? $minDate[0]['day'] : null),
        ];
    }

    /**
     * Debug action
     */
    public function debugLow()
    {
        return [];

    }
}
