<?php

namespace App\Services;

class ExcelService implements ExcelServiceInterface
{

    /**
     * Get email addresses from excel file.
     *
     * @param $rows
     *
     * @return array
     */
    public function getExcelData($rows){

        $excelDataArray = array();
        foreach ($rows as $key => $value){
            $excelDataArray[$key] = $value;
        }

        return $excelDataArray;
    }
}