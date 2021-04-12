<?php

namespace App\Services;

class NotificationService {

    const SENT = "Enviado";

    public function sendNotification($payment)
    {
        try{
            $url = "https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04";
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

            return $responseObj->message == self::SENT;
        } catch (\Exception $e) {
            throw new \Exception ('Cannot send notification', 500);
        }
    }
}