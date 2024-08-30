<?php
/**
 * Created by PhpStorm.
 * User: LOW1
 * Date: 1/15/2020
 * Time: 9:00 PM
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use Yajra\DataTables\Facades\DataTables;
use App\Repositories\LanguageRepository;

class LanguagesController extends Controller
{
    /**
     * @var
     */
    private $languageRepository;

    public function __construct(LanguageRepository $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    public function index(Request $request, Response $response)
    {
        return view("admin.languages.index");
    }

    public function getAllLanguages(Request $request, Response $response)
    {
        return Datatables::of(Language::query())->make(true);
    }



    /**
     * Create language
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Support\MessageBag
     */
    public function createLanguage(Request $request)
    {

        $rules = [
            'name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if (!$validator->passes()) {
            return ($validator->messages());
        }

        try {

            $language = Language::where('name', $request->name)->first();

            if ($language != null) {
                return ajaxResponse(ERROR, 'The language already exists!');
            }

            $language = new Language();
            $language->name = $request->name;
            $language->save();

            $group = new Group();
            $group->language_id = $language->id;
            $group->save();

            return ajaxResponse(SUCCESS, 'The language was created!', $language);

        } catch (Exception $e) {
            Log::info($e->getMessage());
        }

        return ajaxResponse(ERROR, 'Something went wrong');
    }

    /**
     * Edit language
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Support\MessageBag
     */
    public function editLanguage(Request $request)
    {
        $rules = [
            'name' => 'required|min:3',
            'languageId' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if (!$validator->passes()) {
            return ($validator->messages());
        }

        $id = $request->get('languageId');
        $this->languageRepository->update($id, $request->only(['name']));

        return ajaxResponse(SUCCESS);
    }

    public function getLanguage($id, Request $request)
    {

        $language = Language::find($id);

        return [
            "language" => $language
        ];
    }
}