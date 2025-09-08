<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use App\Services\AuthService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Socialite\Facades\Socialite;

use function Laravel\Prompts\password;

class AuthController extends Controller
{
    //

    public function __construct(protected AuthService $authService){}

    public function register(RegistrationRequest $request): JsonResponse
    {
        $validatedData = $request->validated();
        $user =$this->authService->register($validatedData);
        return response()->json([
            'message' => 'User registered successfully.',
            'token' => $user['token'],
        ], Response::HTTP_CREATED);
    }

    function Login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $token = $this->authService->login($credentials);

        if (!$token) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        return response()->json([
            'user' => Auth::user(),
            'token' => $token,
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
     function setPassword(Request $request,$id): JsonResponse
     {

             $validator= Validator::make($request->all(),[
                 'password'=>['required','confirmed',
                             Password::min('8')
                             ->letters()
                             ->mixedCase()
                             ->numbers()
                             ->symbols(),
                         ]
             ]);
             if ($validator->fails()) {
                 return response()->json([
                     'message' => 'The given data was invalid.',
                     'errors' => $validator->errors(),
                 ], 422);
             }
             $user = User::find($id);
             // dd($user);
             $token = JWTAuth::fromUser($user);
             $user->password = Hash::make($request->password);
             $user->email_verified_at = Carbon::now();
             $user->save();
             Auth::login($user);
             return response()->json([
                 'message' => 'Your Password Was Set Successfully',
                 'user' => $user,
                 'token' => $token,
             ]);


     }
     function logout(Request $request):jsonResponse
     {
         $request->user()->currentAccessToken()->delete();

         return response()->json([
             'message' => 'Logged out successfully'
         ], 200);
     }

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

     function forgetPassword(Request $request): JsonResponse
     {

         $validator= Validator::make($request->all(),[
             'email'=>'required|email|exists:users,email',
         ]);
         $status = Password::sendResetLink($request->only('email'));

         return $status === Password::RESET_LINK_SENT
             ? response()->json(['message' => 'Reset token sent to email.'])
             : response()->json(['message' => 'Failed to send reset link.'], 500);
     }

     function resetPassword(Request $request)
     {
//         $validator= Validator::make($request->all(),[
//             'email'=>'required|email|exists:users,email',
//             'password'=>['required','confirmed',
//             Password::min('8')
//             ->letters()
//             ->mixedCase()
//             ->numbers()
//             ->symbols(),
//         ],
//         ]);
//         if ($validator->fails()) {
//             return response()->json([
//                 'message' => 'The given data was invalid.',
//                 'errors' => $validator->errors(),
//             ], 422);
//         }
         $user = User::where('email', $request->email)->first();
         $user->password = Hash::make($request->password);
         $user->save();
         return response()->json([
             'message' => 'Password reset successfully',

         ], 200);
     }
}
