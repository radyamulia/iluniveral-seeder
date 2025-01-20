<?php

use Illuminate\Support\Facades\Http;

if (!function_exists('fetchData')) {
    function fetchData($act, $token, $filter = '', $order = '', $limit = '', $offset = '')
    {
        // $request_uri = env('REQUEST_URI');
        $request_uri = "http://157.119.222.108:8100/ws/live2.php";

        $data = [
            "act" => $act,
            "token" => $token,
            "filter" => $filter,
            "order" => $order,
            "limit" => $limit,
            "offset" => $offset,
        ];

        try {
            $response = Http::post($request_uri, $data);

            if ($response->successful()) {
                $result = $response->json();

                if (isset($result['data'])) {
                    return $result['data'];
                } else {
                    throw new \Exception("Data not found: " . ($result['error_code'] ?? 'Unknown error'));
                }
            } else {
                throw new \Exception("HTTP Request failed with status: " . $response->status());
            }
        } catch (\Exception $e) {
            // Log the error or handle it as necessary
            return "Error: " . $e->getMessage();
        }
    }
}
