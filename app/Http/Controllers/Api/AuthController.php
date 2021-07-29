<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function Register(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);
        if($validatedData->fails()){
            return response(['errors' => $validatedData->errors()]);
        }
        $validatedData['password'] = Hash::make($request->password);
        $user = User::create($validatedData);
        $accessToken = $user->createToken('authToken')->accessToken;
        return response([
            'user' => $user,
            'access_token' => $accessToken
        ]);
    }
    public function Login(Request $request){

        $loginData = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($loginData->fails()){
            return response(['errors' => $loginData->errors()]);
        }

        if (!auth()->attempt($request->all())) {
            return response(['message' => 'Invalid Credentials']);
        }

        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        return response(['user'=>auth()->user(), 'access_token' => $accessToken]);

    }

}
