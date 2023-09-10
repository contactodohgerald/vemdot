<?php

namespace App\Traits;
use App\Traits\ReturnTemplate;
use Exception;
use Illuminate\Support\Facades\Http;

trait PaymentHandler {
    use ReturnTemplate;

    public $key = 'sk_live_56873aec64a59e3322867033283cc21f4df04281';

    public function redirectToGateway($data) {
        try{
            $response = Http::withHeaders([
                "Authorization" => "Bearer $this->key",
                "Content-Type" => "application/json",
            ])->post('https://api.paystack.co/transaction/initialize', array_merge($data, ['callback_url' => route('payment.verify')]));

            return json_decode($response, true);
        }catch(Exception $e) {
            return false;
        }
    }

    public function handleGatewayCallback($ref) {
        $response = Http::withHeaders([
            "Authorization" => "Bearer $this->key",
            "Content-Type" => "application/json",
        ])->get('https://api.paystack.co/transaction/verify/'.$ref);

        return json_decode($response, true);
    }
    
    public function verifyAccountDetail($acct_no, $bank_code) {

        $response = Http::withHeaders([
            "Authorization" => "Bearer $this->key",
            "Content-Type" => "application/json",
        ])->get('https://api.paystack.co/bank/resolve', [
            "account_number" => $acct_no,
            "bank_code" => $bank_code
        ]);

        return json_decode($response, true);
    }

    public function verifyUserAccount($data) {
        $response = Http::withHeaders([
            "Authorization" => "Bearer $this->key",
            "Content-Type" => "application/json",
        ])->get('https://api.paystack.co/bank/resolve', [
            'account_number' => $data['account_number'],
            'bank_code' => $data['bank_code'],
        ]);

        return json_decode($response, true);
    }

    function payWithExistingCard($data){

        $response = Http::withHeaders([
            "Authorization" => "Bearer $this->key",
            "Content-Type" => "application/json",
        ])->post('https://api.paystack.co/transaction/charge_authorization', array_merge($data, ['callback_url' => route('payment.verify')]));

        return json_decode($response, true);
    }

    function transferRecipient($data){
        $response = Http::withHeaders([
            "Authorization" => "Bearer $this->key",
            "Content-Type" => "application/json",
        ])->post('https://api.paystack.co/transferrecipient', $data);

        return json_decode($response, true);
    }
    
    function initiateTransfer($data){
        $response = Http::withHeaders([
            "Authorization" => "Bearer $this->key",
            "Content-Type" => "application/json",
        ])->post('https://api.paystack.co/transfer', $data);

        return json_decode($response, true);
    }
    
    function finalizeTransfer($data){
        $response = Http::withHeaders([
            "Authorization" => "Bearer $this->key",
            "Content-Type" => "application/json",
        ])->post('https://api.paystack.co/transfer/finalize_transfer', $data);

        return json_decode($response, true);
    }

}
