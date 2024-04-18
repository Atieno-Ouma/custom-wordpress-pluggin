<?php
if ( ! empty( $_POST['iban'] ) && check_ajax_referer( 'bank_transfer_verification', 'nonce', false ) ) {
    $iban = sanitize_text_field( $_POST['iban'] );

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://bank-iban-swift-api.p.rapidapi.com/IbanValidate",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode(['iban_number' => $iban]),
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: bank-iban-swift-api.p.rapidapi.com",
            "X-RapidAPI-Key: b4d3cbf52bmsh268a9e63ddcfe76p1a0eeejsn066a2536ac02",
            "content-type: application/json"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        $result = array(
            'success' => false,
            'message' => 'cURL Error #: ' . $err
        );
    } else {
        $apiResponse = json_decode($response, true);
        if ($apiResponse['status'] === 200) {
            $result = array(
                'success' => true,
                'message' => 'IBAN is valid.'
            );
        } else {
            $result = array(
                'success' => false,
                'message' => $apiResponse['message'] || 'IBAN verification failed.'
            );
        }
    }

    echo json_encode($result);
    wp_die();
}