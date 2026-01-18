<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    public static function sendResponse($result, $message, $code = 200)
    {
        $response = [
            'success' => true,
            'msg'     => $message,
            'result'  => $result
        ];

        return response()->json($response, $code);
    }

    public static function sendError($message, $code = 400)
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    public function notification($message, $type = 'success')
    {
        $notification = [
            'alert-type' => $type,
            'message' => $message
        ];

        return $notification;
    }
}
