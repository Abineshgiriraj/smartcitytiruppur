<?php

namespace App\Http\Controllers;

use App\Models\AccessGate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ulipApiKeyGetController extends Controller
{
   public function getBearerToken()
    {
        $url = "https://www.ulipstaging.dpiit.gov.in/ulip/v1.0.0/user/login"; // Correct endpoint
        $username = "vocpa_usr"; // Replace with correct username
        $password = "vocpa@29112024"; // Replace with correct password

        $data = [
            "username" => $username,
            "password" => $password,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return response()->json(['error' => $error], 500);
        }

        curl_close($ch);

        $responseData = json_decode($response, true);

	$apiAuthKey = DB::table('auth_key')
            ->select('api_key')
            ->where('key_name','Bearer')
            ->first();

if($apiAuthKey->api_key != ''){
DB::table('auth_key')
    ->where('key_name', 'Bearer')
    ->update([
        'api_key' => $responseData['response']['id'], // Update the api_key
    ]);
}else{
DB::table('auth_key')->insert([
    	'key_name' => 'Bearer',
    	'api_key' => $responseData['response']['id'],
]);
}


		
        if (isset($responseData['response']['id'])) {
		
            return response()->json([
                'message' => 'Bearer Token fetched successfully',
                'token' => $responseData['response']['id']
            ]);
        } else {
            return response()->json([
                'error' => 'Failed to fetch Bearer Token',
                'response' => $responseData,
            ], 500);
        }
    }
}
