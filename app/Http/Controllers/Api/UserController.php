<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\CustomResponse;

class UserController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    function index()
    {
        $users = User::latest()->paginate();
        return UserResource::collection($users);
        // return response()->json(Controller::standard(['data' => $users, 'message' => 'users found']));
    }
}
