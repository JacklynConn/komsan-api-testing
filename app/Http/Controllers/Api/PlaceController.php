<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoryPlace;
use App\Models\Place;
use App\Models\PlaceGallery;
use App\Models\PlaceType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;


class PlaceController extends Controller
{
    public function addplace(Request $request){
        $data = $request->validate([
            // Validate incoming request data for places
            'place_name' => 'required|string',
            'place_des' => 'required|text',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'village_code' => 'required|exists:villages,village_code',
            'cat_place_id' => 'required|exists:category_places,cat_place_id',
            'phone' => 'required|unique:places|numeric',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'email' => 'nullable|email',
            'website' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);
         // Create a new place instance
        $place = new Place($data);
        $place->status = $request->input('status', 1); // Default status to 1 if not provided
        $place->save();

        // Find or create the place category
        $categoryPlace = CategoryPlace::findOrFail($data['cat_place_id']);

        
        $placeType = new PlaceType();
        $placeType->place_type_name = $categoryPlace->cat_place_name; // Assign place_type_name from the request
        $place->categoryPlace()->associate($categoryPlace);
        $placeType->place()->associate($place);
        $placeType->save();

            // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $imagePath = '/images/place_gallery/';
            $image->move(public_path($imagePath), $imageName);
    
            // Create place gallery entry
            $gallery = new PlaceGallery();
            $gallery->image = $imagePath . $imageName;
            $gallery->place()->associate($place);
            $gallery->save();
        }
        return response() -> json([
            'message' => 'Place and associated types created successfully',
            'data' => [$data],
        ],201);
    }
    public function getPlace($place_id)
    {
        try {
            $place = Place::with('placeType','placeGallery')->findOrFail($place_id);
            return response()->json(['data' => $place], 200);
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Place not found'], 404);
        }
    }
    public function searchPlace(Request $request)
    {
        try {
            // Get the search query from the request
            $searchQuery = $request->input('name');

            // Start with an instance of the Hotel model
            $query = Place::query();

            // If a search query is provided, apply the search filter
            if ($searchQuery) {
                $query->where('place_name', 'like', '%' . $searchQuery . '%');
            }

            // Retrieve the hotels matching the search criteria
            $places = $query->get();

            // Return the response
            return response()->json(['data' => $places], 200);
        } catch (ModelNotFoundException $exception) {
            // Handle the case where no places are found
            return response()->json(['error' => 'No places found'], 404);
        }
    }

    
}
