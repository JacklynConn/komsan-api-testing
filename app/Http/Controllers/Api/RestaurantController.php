<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\FoodType;
use App\Models\Restaurant;
use App\Models\RestaurantGallery;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function addRestaurant(Request $request)
    {
        // Validate the request
        $data = $request->validate([
            'res_name' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'res_des' => 'required|string',
            'village_code' => 'required|exists:villages,village_code',
            'food_type_id' => 'required|exists:food_types,food_type_id',
            'food_price' => 'required|numeric',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'res_email' => 'nullable|email',
            'res_phone' => 'required|unique:restaurants|numeric',
            'res_web' => 'nullable|string',
            'open_time' => 'required|string',
            'close_time' => 'required|string',
            'status' => 'nullable|boolean',
        ]);

        // Create and save the restaurant
        $restaurant = new Restaurant($data);
        $restaurant->status = $request->input('status', 1); // Default status to 1 if not provided
        $restaurant->save();

        // Create and save the food
        $foodtype = FoodType::findOrFail($data['food_type_id']);
        $food = new Food();
        $food->food_name = $foodtype->food_type_name; // Set the food name to the food type name
        $food->food_price = $request->input('food_price');
        $food->foodType()->associate($foodtype);
        $food->restaurant()->associate($restaurant);
        $food->save();

        // Handle image upload and create the gallery entry
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = '/images/restaurants/';
            $image->move(public_path($imagePath), $imageName);

            $gallery = new RestaurantGallery();
            $gallery-> image = $imagePath . $imageName;
            $gallery->restaurant()->associate($restaurant);
            $gallery->save();
        }

        return response()->json([
            'message' => 'Restaurant added successfully',
            'data' => [
                $data
            ],
        ], 201);
    }

    public function getAllRestaurant()
    {
        try{
            $restaurant = Restaurant::with(['village.province', 'foods', 'restaurantGallery'])->get();

        $restaurant->transform(function ($restaurant) {
            $restaurant->restaurantGallery->transform(function ($gallery) {
                $gallery->image = asset($gallery->image);
                return $gallery;
            });
            return $restaurant;
        });
        return response()->json(['data' => $restaurant], 200);
        }catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while retrieving hotels'], 500);
        }
    }

    public function searchRestaurant(Request $request)
    {
        $search = $request->input('name');
        $restaurant = Restaurant::where('res_name', 'like', '%' . $search . '%')->get();

        if ($restaurant->isEmpty()) {
            return response()->json(['message' => 'No restaurants found'], 200);
        }
    }
    


}
