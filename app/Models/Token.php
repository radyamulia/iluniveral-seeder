<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Token extends Model
{
    // API Request Methods
    // Function to get token for API requests
    public static function getToken()
    {
        $request_uri = "http://157.119.222.108:8100/ws/live2.php";
        $data = [
            "act" => "GetToken",
            // For environment variables, use:
            "username" => env('REQUEST_USER'),
            "password" => env('REQUEST_PASS'),
        ];
        try {
            $response = Http::post($request_uri, $data);
            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['data']['token'])) {
                    return $result['data']['token'];
                } else {
                    throw new \Exception("Login failed: " . ($result['error_code'] ?? 'Unknown error'));
                }
            } else {
                throw new \Exception("HTTP Request failed with status: " . $response->status());
            }
        } catch (\Exception $e) {
            // Log the error or handle it as necessary
            return "Error: " . $e->getMessage();
        }
    }

    // Function to get Periode
    public function getPeriode($token, $filter = '', $order = '', $limit = '', $offset = '')
    {
        return fetchData('GetPeriode', $token, $filter, $order, $limit, $offset);
    }
}
