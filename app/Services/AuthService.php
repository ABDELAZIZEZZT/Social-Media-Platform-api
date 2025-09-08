<?php
namespace App\Services;


use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthService
{
    public function __construct(protected UserRepository $userRepository){}

    public function register($data)
    {
        $user = $this->userRepository->create($data);
        $token = $user->createToken('-AuthToken')->plainTextToken;
        return ['user' => $user, 'token' => $token];
    }

    public function login($credentials)
    {
        if (Auth::attempt($credentials)) {
            return Auth::user()->createToken('api_token')->plainTextToken;
        }
        return null;
    }



}
