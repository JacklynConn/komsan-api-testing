<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Models\Province;

use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function getProvinces(){
        return response()->json(['data' => Province::all()], 200);
    }
    public function showPlaceInProvince(Request $request){
        // return response()->json(['data' => $request->input('province_code')], 200);
        $data = $request->validate([
            'province_code' => 'required|exists:provinces,province_code'
        ]);
        $places = Place::whereHas('village.province', function ($query) use ($data) {
            $query->where('province_code', $data['province_code'])
                ->where('places.status','=', 1);
        })->get();
        return response()->json(['data' => $places], 200);
    }
}
