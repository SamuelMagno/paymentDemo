<?php

namespace App\Rules;

class Authorization
{
    const AUTHORIZED = "Autorizado";
    public function isPaymentAuthorized($payment)
    {
        try {
            $url = "https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6";
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_TIMEOUT => 30000,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                ),
            ));

            $response = curl_exec($curl);
            $responseObj = json_decode($response);
            curl_close($curl);

            return $responseObj->message == self::AUTHORIZED;
        } catch (\Exception $e) {
            throw new \Exception ('Unauthorized payment', 401);
        }
    }
}