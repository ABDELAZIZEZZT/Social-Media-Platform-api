<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
// use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
// use Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException as ValidationValidationException;

use function Laravel\Prompts\password;

class AuthController extends Controller
{
    //
    function registration(Request $request){

        $validator= Validator::make($request->all(),[
            'first_name'=>'required|max:255',
            'last_name'=>'required|max:255',
            'email'=>'required|email|max:255|unique:users',
            'job_title'=>'required|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
        if ($request->file('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
        }
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'job_title'=>$request->job_title,
            // 'image'=>'/storage/' . $filePath,
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
    function update(Request$request,$id){
        $user = User::find($id);
        // return response()->json($request->all());
        $validator= Validator::make($request->all(),[
            'first_name'=>'required|max:255',
            'last_name'=>'required|max:255',
            'job_title'=>'required|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }
        if ($request->file('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('uploads', $fileName, 'public');
            $user->image = '/storage/' . $filePath;
        }
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->job_title = $request->job_title;
        $user->save();
        return response()->json($user, 200);

    }

    function forgetPassword(Request $request){

        $validator= Validator::make($request->all(),[
            'email'=>'required|email|exists:users,email',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $token =  rand(100000, 999999);
        $email = $request->email;
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
          ]);

        Mail::send('mail.forgetPassword', ['token' => $token], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password');
        });
        return response()->json([
            'message' => 'the token is sent to your email',
            'redirect' => route('confirmToken', ['email' => $email])
        ], 200);
    }

    function confirmToken(Request $request){
        $validator= Validator::make($request->all(),[
            'token'=>'required|alpha_num|size:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors(),
            ], 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
        if(DB::table('password_resets')->where('email', $request->email)->where('token', $request->token)->exists()){
            DB::table('password_resets')->where('email', $request->email)->delete();
            return response()->json([
                'message' => 'the token is correct and you can reset your password',
                'redirect' => route('resetPassword', ['email' => $request->email])
            ], 200);
        }else{
            return response()->json([
                'message' => 'Token not found',
            ], 404);
        }
    }
    function resetPassword(Request $request){
        $validator= Validator::make($request->all(),[
            'email'=>'required|email|exists:users,email',
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
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        return response()->json([
            'message' => 'Password reset successfully',

        ], 200);
    }
}
