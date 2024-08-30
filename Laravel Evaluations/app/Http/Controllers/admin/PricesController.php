<?php
/**
 * Created by PhpStorm.
 * User: LOW1
 * Date: 1/17/2020
 * Time: 10:02 AM
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prices;
use App\Models\PricingType;
use App\Repositories\PricesRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;
use App\Models\Language;
use App\Models\PaperType;

class PricesController extends Controller
{
    /**
     * @var
     */
    private $pricesRepository;

    public function __construct(PricesRepository $pricesRepository)
    {
        $this->pricesRepository = $pricesRepository;
    }

    public function index()
    {
        $languages = Language::with([
            'language_paper_type'
        ])->get();

        $pricingTypes = PricingType::get()->toArray();
        $pricingTypesMap = $this->pricesRepository->getTestTypeMapping();

        $prices = Prices::select(['id', 'language_id', 'pricing_type_id', 'price'])
            ->where("level", "=", 0)
            ->get();

        $groupedPrices = [];

        foreach ($prices as $price) {
            if (empty($groupedPrices[$price->language_id])) {
                $groupedPrices[$price->language_id] = [];
            }

            if (empty($groupedPrices[$price->language_id][$price->pricing_type_id])) {
                $groupedPrices[$price->language_id][$price->pricing_type_id] = [];
            }

            $groupedPrices[$price->language_id][$price->pricing_type_id] = [
                'id' => $price->id,
                'price' => $price->price,
            ];
        }

        return view("admin.prices.index", compact('languages', 'pricingTypes', 'groupedPrices', 'pricingTypesMap'));
    }

    public function saveDefault(Request $request, Response $response)
    {
        $create = $request->get('create');
        $update = $request->get('update');

        if ($create) $this->pricesRepository->createDefaultMultiple($create);
        if ($update) $this->pricesRepository->insertOrUpdate($update);

        return ajaxResponse(SUCCESS);
    }

    public function getProjectPrices(Request $request, Response $response)
    {
        $clientId = $request->get('clientId', 0);
        $projectId = $request->get('projectId', 0);

        $languages = Language::with([
            'language_paper_type'
        ])->get();

        $pricingTypes = PricingType::get()->toArray();
        $pricingTypesMap = $this->pricesRepository->getTestTypeMapping();

        $groupedPrices = $this->pricesRepository->getClientPricesGrouped($clientId, $projectId);

        return [
            'languages' => $languages,
            'pricingTypes' => $pricingTypes,
            'groupedPrices' => $groupedPrices,
            'pricingTypesMap' => $pricingTypesMap
        ];
    }
}