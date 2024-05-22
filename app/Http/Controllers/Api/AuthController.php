<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // update email from users table
        $validateData = $request->validate([ // modified #IT-122 Mak Mach 2024-05-12
            'name' => 'required|max:255',
            'phone' => 'required|unique:users',
            'email' => 'nullable|email|unique:users', // modified #IT-122 Mak Mach 2024-05-12
            'password' => 'required|min:6',
        ]);

        // modified #IT-122 Mak Mach 2024-05-12
        $validateData['password'] = Hash::make($validateData['password']);
        if ($request->hasFile('profile_img')) {
            $profile = $request->file('profile_img');
            $profile_name = time() . "." . $profile->getClientOriginalExtension();
            $distinationPath = public_path('profile');
            $profile->move($distinationPath, $profile_name);
            $validateData['profile_img'] = $profile_name;
        }

        $user = User::create($validateData);
        $accessToken = $user->createToken('authToken')->accessToken;
        return response(['user' => $user, 'access_token' => $accessToken, 'message' => 'User created successfully']);
    }

    public function login(Request $request)
    {

        $data = $request->validate([
            'phone' => 'required',
            'password' => 'required',
        ]);

        if (!auth()->attempt($data)) {
            return response(['message' => 'Invalid credentials'], 401);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return response(['user' => auth()->user(), 'access_token' => $accessToken, 'message' => 'User login successfully']);
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
}
