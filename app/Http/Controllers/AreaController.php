<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $language = $request->language ?? 'bn';

        if (!in_array($language, ['en', 'bn'])) {
            $language = 'bn';
        }

        $newList = [];
        foreach (config("areas.{$language}") as $id => $area) {
            $newList[] = [
                'id' => $id,
                'name' => $area
            ];
        }

        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => '',
            'data' => $newList
        ]);
    }
}
