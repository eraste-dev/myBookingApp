<?php

namespace App\Http\Controllers;

use App\Models\CustomResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    static function standard($json = ['message' => "", 'data' => null, 'error' => null, 'status' => false])
    {
        $status  = (isset($json['data']) && $json['data'] != null) ? true : false;
        $data    = $status ? $json['data'] : null;
        $error   = isset($json['error']) ? $json['error'] : null;
        $message = isset($json['message']) ? $json['message'] : '';

        $customResponse = new CustomResponse($status, $data, $message, $error);
        return $customResponse->get();
    }
}
