<?php

namespace App\Services;


use Exception;

class SmartBillService
{
    const INVOICE_URL = 'https://ws.smartbill.ro/SBORO/api/invoice';
    const EMAIL_URL = 'https://ws.smartbill.ro/SBORO/api/document/send';
    const PARAMS_PDF = '/pdf?cif=%s&seriesname=%s&number=%s';
    const PARAMS_DELETE = '?cif=%s&seriesname=%s&number=%s';

    const DEBUG_ON_ERROR = false; // use this only in development phase; DON'T USE IN PRODUCTION !!!
    private $hash;
    private $companyVatCode;

    public function __construct()
    {
        $user = env("SMARTBILL_USER");
        $token = env("SMARTBILL_TOKEN");
        $this->companyVatCode = env("SMARTBILL_VATCODE");

        $this->hash = base64_encode($user . ':' . $token);
    }

    private function _cURL($url, $data, $request, $headAccept)
    {
        $headers = array($headAccept, "Authorization: Basic " . $this->hash);

        $ch = curl_init($url);
        // curl_setopt($ch, CURLOPT_MUTE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if (!empty($data)) {
            $headers[] = "Content-Type: application/json; charset=utf-8";
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        if (!empty($request)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // debugging
        $isDebug = self::DEBUG_ON_ERROR;
        if (!empty($isDebug)) {
            $debug = array(
                'URL: ' => $url,
                'data: ' => $data,
                'headers: ' => $headAccept,
            );
            echo '<pre>', print_r($debug, true), '</pre>';
        }

        return $ch;
    }

    private function _callServer($url, $data = '', $request = '', $headAccept = "Accept: application/json")
    {
        if (!isset($data['companyVatCode'])) {
            $data['companyVatCode'] = $this->companyVatCode;
        }

        if (strpos($url, '/pdf?') || $request == "DELETE") {
            $data = '';
        }

        if (empty($url)) return FALSE;

        $ch = $this->_cURL($url, $data, $request, $headAccept);
        $return = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status != 200) {
            $errorMessage = json_decode($return, true);

            if (false !== strpos($url, self::EMAIL_URL)) {
                $errorMessage = !empty($errorMessage['status']['code']) ? $errorMessage['status']['message'] : $return;
            } else {
                $errorMessage = !empty($errorMessage['errorText']) ? $errorMessage['errorText'] : $return;
            }

            //dd($status);
            //throw new Exception($errorMessage);
            // empty response
            $return = '';
        } elseif (false === strpos($url, '/pdf?')) {
            $return = json_decode($return, true);
        }

        return $return;
    }

    public function createInvoice($data)
    {
        return $this->_callServer(self::INVOICE_URL, $data);
    }

    public function PDFInvoice($seriesName, $number)
    {
        $seriesName = urlencode($seriesName);
        $url = sprintf(self::INVOICE_URL . self::PARAMS_PDF, $this->companyVatCode, $seriesName, $number);
        return $this->_callServer($url, [], '', "Accept: application/octet-stream");
    }

    public function deleteInvoice($seriesName, $number)
    {
        $seriesName = urlencode($seriesName);
        $url = sprintf(self::INVOICE_URL . self::PARAMS_DELETE, $this->companyVatCode, $seriesName, $number);
        return $this->_callServer($url, [], 'DELETE');
    }

}