<?php

namespace App\Repositories;


use App\Models\Prices;
use App\Models\PricingType;

class PricesRepository
{

    /**
     * @var $model
     */
    private $model;

    /**
     *
     *
     * @var array
     */
    private $testTypeMapping = array(
        TEST_LANGUAGE_USE_NEW => PricingType::LANGUAGE_USE_NEW,
        TEST_SPEAKING => PricingType::SPEAKING,
        TEST_WRITING => PricingType::WRITING,
        TEST_READING => PricingType::READING,
        TEST_LISTENING => PricingType::LISTENING,
        TEST_LANGUAGE_USE => PricingType::LANGUAGE_USE,
    );

    /**
     * PricesRepository constructor.
     *
     * @param \App\Models\Prices $model
     */

    public function __construct(Prices $model)
    {
        $this->model = $model;
    }

    function createDefaultMultiple($data)
    {
        return $this->model->insert($data);
    }

    function insertOrUpdate(array $rows)
    {
        $table = $this->model->getTable();


        $first = reset($rows);

        $columns = implode( ',',
            array_map( function( $value ) { return "$value"; } , array_keys($first) )
        );

        $values = implode( ',', array_map( function( $row ) {
                return '('.implode( ',',
                        array_map( function( $value ) { return '"'.str_replace('"', '""', $value).'"'; } , $row )
                    ).')';
            } , $rows )
        );

        $updates = implode( ',',
            array_map( function( $value ) { return "$value = VALUES($value)"; } , array_keys($first) )
        );

        $sql = "INSERT INTO {$table}({$columns}) VALUES {$values} ON DUPLICATE KEY UPDATE {$updates}";

        return \DB::statement( $sql );
    }

    function getAll()
    {
        return $this->model->all();
    }

    function getById($id)
    {
        return $this->model->find($id);
    }

    function getClientPrices($clientId = 0, $projectId = 0)
    {
        $query = "SELECT pa.* FROM `prices` pa 
              LEFT JOIN `prices` pb
                ON pa.language_id = pb.language_id
                  AND pa.pricing_type_id = pb.pricing_type_id
                  AND pa.level < pb.level
                  AND (" .
                        ($projectId > 0 && $clientId > 0 ? "(pb.client_id = {$clientId} AND pb.project_id = {$projectId}) OR " : "") .
                        ($clientId > 0 ? "(pb.client_id = {$clientId} AND pb.project_id is NULL) OR " : "") .
                        " pb.level = 0
                    )
              WHERE (" .
                        ($projectId > 0 && $clientId > 0 ? "(pa.client_id = {$clientId} AND pa.project_id = {$projectId}) OR " : "") .
                        ($clientId > 0 ? "(pa.client_id = {$clientId} AND pa.project_id is NULL) OR " : "") .
                        " pa.level = 0
                    )
              AND pb.level is NULL";

        $return = \DB::select($query);

        return $return;
    }

    function getClientPricesGrouped($clientId = 0, $projectId = 0) : array
    {
        $prices = $this->getClientPrices($clientId, $projectId);

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
                'level' => $price->level,
            ];
        }

        return $groupedPrices;
    }

    function update($id, array $attributes)
    {
        return $this->model->find($id)->update($attributes);
    }

    function delete($id)
    {
        return $this->model->find($id)->delete();
    }

    function getTestTypeByPaperType($testType)
    {
        $pricingTypeId = 0;
        if (!empty($this->testTypeMapping[$testType])) {
            $pricingTypeId = $this->testTypeMapping[$testType];
        }

        return $pricingTypeId;
    }

    function getTestTypeMapping()
    {
        return $this->testTypeMapping;
    }

}