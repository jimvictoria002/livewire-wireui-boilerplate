<?php

namespace App\Http\Controllers\Api\Auth;

use App\Actions\Api\Auth\LogoutUserAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __invoke(Request $request, LogoutUserAction $logoutUser): JsonResponse
    {
        $logoutUser->handle($request->user());

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }
}
