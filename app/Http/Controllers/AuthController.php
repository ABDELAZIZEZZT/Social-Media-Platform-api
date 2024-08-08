<?php

namespace App\Http\Controllers;

use App\Models\User;
// use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

use function Laravel\Prompts\password;

class AuthController extends Controller
{
    //
    function registration(Request $request){

        $validator= Validator::make($request->all(),[
            'first_name'=>'required|max:255',
            'last_name'=>'required|max:255',
            'email'=>'required|email|max:255|unique:users',
            'password'=>['required','confirmed',
                        Password::min('8')
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols(),
                    ],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = JWTAuth::fromUser($user);
        if($user){
            return response()->json([
                'message' => 'User registered successfully',
                'user' => $user,
                'token' => $token,
            ]);
        }




    }
    function login(Request $request){
        $validator= Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>['required',
                        Password::min('8')
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols(),],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ]);
        }

        $credentials = $request->only('email', 'password');
        $token = JWTAuth::attempt($credentials);
        try {
            if (! $token) {
                return response()->json([
                    'message' => 'Invalid email or password',
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Could not create token',
            ]);
        }

        $user=Auth::user();
        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);

    }

    function logout(Request $request){
        // dd($request->all());
        try {
            // Invalidate the current token
            JWTAuth::parseToken()->invalidate();

            return response()->json([
                'message' => 'User logged out successfully',
            ], 200);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Failed to logout, token invalid.',
            ], 400);
        }
    }
    function update($request,$id){
        $user = User::find($id);
        $validator= Validator::make($request->all(),[
            'first_name'=>'required|max:255',
            'last_name'=>'required|max:255',
            'email'=>'required|email|max:255|unique:users',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }
        
    }
}
