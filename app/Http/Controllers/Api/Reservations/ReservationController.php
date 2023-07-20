<?php

namespace App\Http\Controllers\Api\Reservations;

use App\Models\Reservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationResource;
use App\Models\CustomResponse;

class ReservationController extends Controller
{
    public function __construct()
    {
    }

    function index()
    {
        $users = Reservation::latest()->paginate();
        return response()->json(Controller::standard([
            'data'    => ReservationResource::collection($users),
            'message' => 'Reservation found'
        ]));
    }
}
