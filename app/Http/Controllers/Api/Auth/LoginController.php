<?php

namespace App\Http\Controllers\Api\Auth;

use App\Actions\Auth\LoginAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginFormRequest;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginFormRequest $request, LoginAction $loginUser): JsonResponse
    {
        $res = $loginUser->handle($request->validated());

        $user = $res['user'];
        $requiresTwoFactor = $res['requiresTwoFactor'] ?? false;

        $user->tokens()->where('name', 'api')->delete();

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => $user,
            'requires_two_factor' => $requiresTwoFactor,
        ]);
    }
}
