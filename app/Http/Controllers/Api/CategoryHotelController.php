<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoryHotel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CategoryHotelController extends Controller
{
    public function addCatHotel(Request $request)
    {
        $data = $request->validate([
            'cat_hotel_name' => 'required|string',
        ]);

        $data = CategoryHotel::create($data);

        return response()->json([
            'message' => 'Hotel Category added successfully', 
            'data' => [$data]
        ], 201);
    }

    public function getCatHotel($cat_hotel_id)
    {
        try {
            // Retrieve the category hotel
            $categoryHotel = CategoryHotel::findOrFail($cat_hotel_id);
            
            // Retrieve all hotels associated with the category
            $hotels = $categoryHotel->hotelTypes()->with('hotel')->get();
    
            return response()->json(['data' => $hotels], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Category Hotel not found'], 404);
        }
    }
}
