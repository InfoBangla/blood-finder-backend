<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => '',
            'data' => [
                'token' => 'my-token'
            ]
        ]);
    }

    public function logout(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => '',
            'data' => [
                'token' => 'my-token'
            ]
        ]);
    }
}
