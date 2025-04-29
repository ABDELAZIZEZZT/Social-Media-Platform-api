<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Socialite\Facades\Socialite;

use function Laravel\Prompts\password;

class AuthController extends Controller
{
    //

    public function register(RegistrationRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $user = User::create($validatedData);
        $token = $user->createToken('-AuthToken')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully.',
            'token' => $token
        ], Response::HTTP_CREATED);
    }

    function Login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'not correct credentials'
            ],Response::HTTP_UNAUTHORIZED);
        }

        $user = Auth::user();
        $token = $user->createToken( '-AuthToken')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], Response::HTTP_OK);
    }
//     function registrationProvider($provider){
//         if($provider == 'twitter'){
//             return Socialite::driver($provider)->redirect();
//         }
//         return Socialite::driver($provider)->stateless()->redirect();
//     }
//     function redirectProvider($provider){
//         try {
//             if($provider == 'twitter'){
//                 $socialUser = Socialite::driver($provider)->user();
//             }else{
//                 $socialUser = Socialite::driver($provider)->stateless()->user();
//             }

//             $user = User::where('provider_id', $socialUser->getId())->first();
//             if (!$user) {
//                 $user = User::create([
//                     'first_name' => $socialUser->getName(),
//                     'last_name' => $socialUser->getname(),
//                     'email' => $socialUser->getEmail(),
//                     'provider' => $provider,
//                     'provider_id' => $socialUser->getId(),
//                     'password' =>  encrypt('123456dummy'),
//                     'image'=>$socialUser->getAvatar(),
//                 ]);
//             }
//             if(!$user->hasVerifiedEmail()){
//                 // return redirect()->route('view/setPassword/{id}',['id'=>$user->id]);
//             }return redirect('/blogs');
//         } catch (\Exception $e) {
//             dd($e);
// //            return redirect('/register')->withErrors(['error' => 'Failed to register with ' . $provider . '. Please try again later.']);
//         }

//     }
    // function viewSetPassword($id){
    //     return response()->json([
    //         'message' => 'Please set your password by providing a new password and confirming it.',
    //         'redirect' => route('setPassword', ['password','confirmPassword','id' => $id]),
    //     ]);
    // }
    // function setPassword(Request $request,$id){

    //         $validator= Validator::make($request->all(),[
    //             'password'=>['required','confirmed',
    //                         Password::min('8')
    //                         ->letters()
    //                         ->mixedCase()
    //                         ->numbers()
    //                         ->symbols(),
    //                     ]
    //         ]);
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'message' => 'The given data was invalid.',
    //                 'errors' => $validator->errors(),
    //             ], 422);
    //         }
    //         $user = User::find($id);
    //         // dd($user);
    //         $token = JWTAuth::fromUser($user);
    //         $user->password = Hash::make($request->password);
    //         $user->email_verified_at = Carbon::now();
    //         $user->save();
    //         Auth::login($user);
    //         return response()->json([
    //             'message' => 'Your Password Was Set Successfully',
    //             'user' => $user,
    //             'token' => $token,
    //         ]);

    //     }
    // }
    // function logout(Request $request){
    //     $request->user()->currentAccessToken()->delete();

    //     return response()->json([
    //         'message' => 'Logged out successfully'
    //     ], 200);
    // }

    // function logoutAllDevices(Request $request): JsonResponse
    // {
    //     $request->user()->tokens()->delete();

    //     return response()->json([
    //         'message' => 'Logged out from all devices'
    //     ], 200);
    // }
    // function update(Request$request,$id){
    //     $user = User::find($id);
    //     // return response()->json($request->all());
    //     $validator= Validator::make($request->all(),[
    //         'first_name'=>'required|max:255',
    //         'last_name'=>'required|max:255',
    //         'job_title'=>'required|max:255',
    //         'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'message' => 'The given data was invalid.',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }
    //     if ($request->file('image')) {
    //         $file = $request->file('image');
    //         $fileName = time() . '.' . $file->getClientOriginalExtension();
    //         $filePath = $file->storeAs('uploads', $fileName, 'public');
    //         $user->image = '/storage/' . $filePath;
    //     }
    //     $user->first_name = $request->first_name;
    //     $user->last_name = $request->last_name;
    //     $user->job_title = $request->job_title;
    //     $user->save();
    //     return response()->json($user, 200);

    // }

    // function forgetPassword(Request $request){

    //     $validator= Validator::make($request->all(),[
    //         'email'=>'required|email|exists:users,email',
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'message' => 'The given data was invalid.',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     $token =  rand(100000, 999999);
    //     $email = $request->email;
    //     DB::table('password_resets')->insert([
    //         'email' => $request->email,
    //         'token' => $token,
    //         'created_at' => Carbon::now()
    //       ]);

    //     Mail::send('mail.forgetPassword', ['token' => $token], function($message) use($request){
    //         $message->to($request->email);
    //         $message->subject('Reset Password');
    //     });
    //     return response()->json([
    //         'message' => 'the token is sent to your email',
    //         'redirect' => route('confirmToken', ['email' => $email])
    //     ], 200);
    // }

    // function resetPassword(Request $request){
    //     $validator= Validator::make($request->all(),[
    //         'email'=>'required|email|exists:users,email',
    //         'password'=>['required','confirmed',
    //         Password::min('8')
    //         ->letters()
    //         ->mixedCase()
    //         ->numbers()
    //         ->symbols(),
    //     ],
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'message' => 'The given data was invalid.',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }
    //     $user = User::where('email', $request->email)->first();
    //     $user->password = Hash::make($request->password);
    //     $user->save();
    //     return response()->json([
    //         'message' => 'Password reset successfully',

    //     ], 200);
    // }

}
