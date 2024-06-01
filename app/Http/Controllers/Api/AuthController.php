<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function checkPhone(Request $request)
    {
        $phone = $request->phone;
        $user = User::where('phone', $phone)->first();
        if ($user && $user->status == 1) {
            return response()->json(['status' => 'exists'], 200);
        }
        return response()->json(['status' => '0'], 200);
    }

    public function register(Request $request)
    {
        // update email from users table
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $existingUser = User::where('phone', $request->phone)->first();
        if ($existingUser) {
            return response()->json(['error' => 'User already exists'], 409);
        }

        $validateData = $request->all();
        $validateData['password'] = Hash::make($validateData['password']);
        // status equals 0 means user is inactive
        $validateData['status'] = 0;

        if ($request->hasFile('profile_img')) {
            $profile = $request->file('profile_img');
            $profile_name = time() . "." . $profile->getClientOriginalExtension();
            $distinationPath = public_path('profile');
            $profile->move($distinationPath, $profile_name);
            $validateData['profile_img'] = $profile_name;
        }

        $user = User::create($validateData);
        return response()->json(['success' => true, 'user' => $user, 'message' => 'User created successfully'], 201);
    }

    // public function verify(Request $request)
    // {
    //     // Verify the code using Firebase (front-end part)
    //     $user = User::where('phone', $request->phone)->first();

    //     if (!$user) {
    //         return response()->json(['error' => 'User not found'], 404);
    //     }

    //     $user->status = 1;
    //     $user->save();

    //     return response()->json(['success' => true, 'user' => $user], 200);
    // }
    public function verify(Request $request){

        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'verification_code' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::where('phone', $request->phone)->first();

        if($user){
            $user->status = 1;
            $user->save();
            return response()->json(['success' => true, 'user' => $user, 'message' => 'User verified successfully'], 200);
        }

        return response()->json(['error' => 'User not found'], 404);
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $credentials = $request->only('phone', 'password');
        $user = User::where('phone', $request->phone)->first();

        if($user){
            if($user->status == 0){
                return response()->json(['error' => 'Your account is not verified yet.'], 403);
            }

            if(Auth::attempt($credentials)){
                $accessToken = auth()->user()->createToken('authToken')->accessToken;
                return response()->json(['success' => true, 'token' => $accessToken, 'user' => auth()->user(), 'message' => 'User login successfully'], 200);
            }
        }else{
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }
    // modified #IT-122 Mak Mach 2024-05-12

    public function logout(Request $request)
    {
        auth()->logout();
        return response(['message' => 'User logout successfully']);
    }

    public function updateUser(Request $request, $id)
    {
        $data = $request->all();
        $user = User::find($id);

        if (!$user) {
            return response(['message' => 'User not found'], 404);
        }

        if ($user) {
            if ($request->hasFile('profile')) {
                $profile = $request->file('profile');
                $profile_name = time() . "." . $profile->getClientOriginalExtension();
                $distinationPath = public_path('/profile');
                $profile->move($distinationPath, $profile_name);
                $data['profile_img'] = $profile_name;


                $oldImage = public_path('/profile/' . $user->profile_img);
                if (file_exists($oldImage)) {
                    unlink($oldImage);
                }
            }
            $user->update($data);
            return response(['user' => $user, 'message' => 'User updated successfully']);
        }
    }

    public function me()
    {
        return response(['user' => auth()->user()]);
    }
}
