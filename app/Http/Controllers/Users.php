<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\userLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class Users extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response(['Usuarios'=>$users]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);
        if (empty($user)) {
            return response(['Message'=>'User 404'],404);
        }
        return response(['User' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (empty($user)) {
            return response(['Message'=>'User 404'],404);
        }
            $validatedData = Validator::make($request->all(), [
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed',
                'level' => ['required' ,'string' , new userLevel]
            ]);

            if($validatedData->stopOnFirstFailure()->fails()){
                return response(['errors' => $validatedData->errors()]);
            }

            $ValidData=$validatedData->validated();
            $ValidData['password'] = Hash::make($request->password);
            $user->update($ValidData);
            return response([
                'user' => $user
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (empty($user)) {
            return response(['Message'=>'User 404'],404);
        }
        $user->delete();
        return response(['Message' => 'Usuario Eliminado']);
    }
}
