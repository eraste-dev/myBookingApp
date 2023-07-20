<?php

namespace App\Http\Controllers\Api\Reservations;

use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\HotelResource;
use App\Models\CustomResponse;

class HotelController extends Controller
{
    public function __construct()
    {
    }

    function index()
    {
        $users = Hotel::latest()->paginate();
        return response()->json(Controller::standard([
            'data'    => HotelResource::collection($users),
            'message' => 'Hotel found'
        ]));
    }
}
