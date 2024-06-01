<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryHotelController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\FoodTypeController;
use App\Http\Controllers\Api\PlaceController;
use App\Http\Controllers\Api\CategoryPlaceController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\ProvinceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//AUTH
Route::post("register", [AuthController::class, "register"]);
Route::post("login",    [AuthController::class, "login"]);
Route::post("logout",   [AuthController::class, "logout"]);
Route::post("check-phone", [AuthController::class, "checkPhone"]);
Route::post("verify", [AuthController::class, "verify"]);
Route::post("resend-otp", [AuthController::class, "resendOTP"]);
Route::post("forgot-password", [AuthController::class, "forgotPassword"]);
Route::post("reset-password", [AuthController::class, "resetPassword"]);

//HOTEL
Route::post("add-hotel", [HotelController::class, "addHotel"]);
Route::post("search-hotel", [HotelController::class, "searchHotel"]);
Route::get("get-allHotel", [HotelController::class, "getAllHotel"]);
Route::get("get-hotel/{hotel_id}", [HotelController::class, "getHotel"]);
Route::post("add-cat-hotel", [CategoryHotelController::class, "addCatHotel"]);
Route::get("get-cat-hotel/{cat_hotel_id}", [CategoryHotelController::class, "getCatHotel"]);

//Restaurant
Route::post("add-restaurant", [RestaurantController::class, "addRestaurant"]);
Route::get("get-allRes", [RestaurantController::class, "getAllRestaurant"]);
Route::post("add-foodtype", [FoodTypeController::class, "addFoodType"]);
Route::post("search-restaurant", [RestaurantController::class, "searchRestaurant"]);

//Place
Route::post("add-place", [PlaceController::class, "addPlace"]);
Route::post("add-cat-place", [CategoryPlaceController::class, "addCatPlace"]);
Route::get('get-place/{place_id}', [PlaceController::class, 'getPlace']);
Route::get("get-cat-place/{cat_place_id}", [CategoryPlaceController::class, "getCatPlace"]);
Route::post('places/search', [PlaceController::class, 'searchPlace']);

//Province

Route::get('provinces', [ProvinceController::class, 'getProvinces']);
Route::post("showPlaceInProvince", [ProvinceController::class, 'showPlaceInProvince']);

//Get All Location
Route::get('locations', [LocationController::class, 'getAllLocations']);

// Group Middleware
Route::group(['middleware' => 'auth:api'], function () {
    //AUTH
    Route::get('me', [AuthController::class, 'me']);
});

