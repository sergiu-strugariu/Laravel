<?php

namespace App\Http\Controllers;


use App\Models\Reference;
use App\Repositories\ReferenceRepository;
use Barryvdh\TranslationManager\Manager;
use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ReferenceController extends Controller
{
    private $referenceRepository;
    private $manager;

    /**
     * ReferenceController constructor.
     *
     * @param \App\Repositories\ReferenceRepository $referenceRepository
     */
    public function __construct(
        ReferenceRepository $referenceRepository,
        Manager $manager
    )
    {
        $this->referenceRepository = $referenceRepository;
        $this->manager = $manager;
    }

    /**
     * Display a listing of references.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = $this->referenceRepository->getAll()->pluck('category', 'category');
        $levels = $this->referenceRepository->getAll()->pluck('level', 'level');

        return view('references.index', [
            'categories' => $categories,
            'levels' => $levels,
        ]);
    }

    /**
     * Ajax function to populate data table.
     */
    public function getTableData(Request $request)
    {
        return DataTables::of($this->referenceRepository->search($request->input('filters')))->make(true);
    }

    /**
     * Generate update form data for Ajax request.
     */
    public function updateFormData($id)
    {
        $reference = $this->referenceRepository->getById($id);

        $translationsModel = Translation::where(['group' => $reference->slug, 'key' => $reference->level])->get();
        $translations = [];
        foreach ($translationsModel as $translation) {
            $translations[$translation->locale] = $translation;
        }

        $response = [
            'reference' => $reference,
            'translations' => $translations,
        ];

        return json_encode($response);
    }

    /**
     * Update the specified reference.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Reference $reference
     * @return array
     */
    public function update(Request $request, Reference $reference)
    {
        $rules = [
            'translations' => 'required',
            'category' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        ### check validation rules
        if (!$validator->passes()) {
            return response()->json(['resMotive' => $validator->messages()]);
        }

        foreach ($request->input('translations') as $locale => $value) {
            $translation = Translation::firstOrNew([
                'locale' => $locale,
                'group' => $reference->slug,
                'key' => $reference->level,
            ]);
            $translation->value = (string) $value ?: null;
            $translation->status = Translation::STATUS_CHANGED;
            $translation->save();
        }

        $reference->category = $request->get('category');
        $reference->save();


        $this->manager->exportTranslations($reference->slug);

        return response()->json(['resType' => 'Success']);
    }
}
