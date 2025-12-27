<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BloodGroupController extends Controller
{
    public function index(Request $request)
    {
        $language = $request->language ?? 'bn';

        if (!in_array($language, ['en', 'bn'])) {
            $language = 'bn';
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => '',
            'data' => config("bloodGroups.{$language}"),
            'errors' => []
        ]);
    }
}
