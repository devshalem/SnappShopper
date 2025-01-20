<?php

class Flutterwave {
    private $secretKey;
    private $baseUrl = "https://api.flutterwave.com/v3/";

    public function __construct($secretKey) {
        $this->secretKey = $secretKey;
    }

    // Function to initiate payment
    public function initiatePayment($user, $training, $amount) {

        $tx_ref = uniqid('tx_'); // Unique transaction reference
        $currency = "USD";

        // Store the payment record in the database as "pending"
        $payment = new Payment();
        $payment::createPayment($user->ID, $training->ID, $tx_ref, $amount, $currency);

        $payload = [
            "tx_ref" => $tx_ref, // Unique transaction reference
            "amount" => $amount,
            "currency" => $currency, // Change currency if necessary
            "redirect_url" => "https://localhost/proncad.com/api/routes/flutterwave_callback.php",// change when deployed on live server 
            "payment_options" => "card,banktransfer",
            "customer" => [
                "email" => $user->EMAIL,
                "name" => $user->NAME
            ],
            "meta" => [
                "user_id" => $user->ID,
                "training_id" => $training->ID
            ]
        ];

        $response = $this->sendRequest("payments", $payload);
        return $response;
    }

    // Function to handle callback verification
    public function verifyTransaction($transactionId) {
        $url = "transactions/{$transactionId}/verify";
        $response = $this->sendRequest($url);

        if ($response && $response['status'] === 'success') {
            return $response['data'];
        }
        return false;
    }

    // Generic function to send request to Flutterwave API
    private function sendRequest($endpoint, $payload = null) {
        $url = $this->baseUrl . $endpoint;
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => ($payload) ? "POST" : "GET",
            CURLOPT_POSTFIELDS => ($payload) ? json_encode($payload) : null,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $this->secretKey,
                "Content-Type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return false; // Handle error
        }

        return json_decode($response, true);
    }
}
?>
