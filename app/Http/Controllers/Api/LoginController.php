<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = User::whereEmail($request->email)->first();

        $plainTextToken = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'plain-text-token' => $plainTextToken,
        ]);
    }
}
