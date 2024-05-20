<?php

namespace Fleetbase\Storefront\Support;

class MpesaService
{
    public function initiateSTKPush($msisdn, $amount, $reference)
    {
        $reference = abs(rand(1000000,99999999999));    
        $amount_stk = "1";
        $msisdn = "";// //
        $reference_one = "TEST_COLLECTION";
        $reference_two = "TEST_COLLECTION";
        $mobile_callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/mobile_callback_url/'.$reference;
        $timeout_callback_url = 'http://'.$_SERVER['HTTP_HOST'].'/timeout_callback_url/'.$reference;


        //LIVE CREDENTIALS
        $merchant_id = '4103503';
        $pass_key = '';
        $time_stamp = date("YmdHis",time());
        $consumer_key = '';
        $consumer_secret = '';




        $msisdn = (int) filter_var($msisdn, FILTER_SANITIZE_NUMBER_INT);							
        $password = base64_encode( $merchant_id . $pass_key . $time_stamp);


        //AUTHORIZATION CALL

        

        //LIVE CREDENTIALS
        $url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest'; 
        $url_register = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';


        /*


        //TEST CREDENTIALS
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'; 
        $url_register = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';


        */


        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url_register);
        $credentials = base64_encode($consumer_key.':'.$consumer_secret);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Basic '.$credentials)); 
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $curl_response = curl_exec($curl);
        $curl_response = explode('"access_token": ',$curl_response);
        $curl_response1 = explode('"expires_in":',$curl_response[1]);
        $trimmed = rtrim($curl_response1[0],',');
        $trimmed1 = trim($trimmed, '"');
        $curl_response2 = explode('"',$trimmed1);
        $curl_response3 = explode('"',$curl_response2[0]);
        $token = $curl_response3[0];





        //MPESA C2B CALL
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$token.'')); //setting custom header


        $curl_post_data = array(
            //Fill in the request parameters with valid values
            'BusinessShortCode' => ''.$merchant_id.'',
            'Password' => ''.$password.'',
            'Timestamp' => ''.$time_stamp.'',
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => ''.$amount_stk.'',
            'PartyA' => ''.$msisdn.'',
            'PartyB' => ''.$merchant_id.'',
            'PhoneNumber' => ''.$msisdn.'',
            'CallBackURL' => ''.$mobile_callback_url.'',
            'AccountReference' => ''.$reference_one.'',
            'TransactionDesc' => ''.$reference_two.'',
        );

        $data_string = json_encode($curl_post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $curl_response = curl_exec($curl);

        print_r($curl_response);
                        
        $json_decoded = json_decode($curl_response, true);
    }
}