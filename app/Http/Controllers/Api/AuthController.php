<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validate = Validator::make(
                $request->all(),
                [
                    'name' => 'required|string|max:255',
                    'phone' => 'required|string|max:20|unique:users',
                    'password' => 'required|confirmed|string|min:6',
                ]
            );

            if ($validate->fails()) {
                return errorApi('validation error', 401, $validate->errors());
            }

            $verification_code = rand(100000, 999999);
            $user = User::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'verification_code' => $verification_code,
            ]);

            $data = [
                'user' => $user,
                'access_token' => $user->createToken("API TOKEN")->plainTextToken,
            ];

            return apiResponse('User Create Successfuly', $data);
        } catch (Exception $e) {
            return errorApi("Error", 500, $e->getMessage());
        }
    }


    public function login(Request $request)
    {
        try {
            $validate = Validator::make(
                $request->all(),
                [
                    'phone' => 'required|string',
                    'password' => 'required|string'
                ]
            );

            if ($validate->fails()) {
                return errorApi('validation error', 401, $validate->errors());
            }


            $user = User::where('phone', $request->phone)->first();
            if (!$user || !$user->verified || !Hash::check($request->password, $user->password)) {
                return errorApi("Unauthorized", 401);
            }


            $data = [
                'user' => $user,
                'access_token' => $user->createToken("API TOKEN")->plainTextToken,
            ];
            return apiResponse('success', $data);
        } catch (Exception $e) {
            return errorApi("Error", 500, $e->getMessage());
        }
    }



    public function verify(Request $request)
    {
        try {
            $validate = Validator::make(
                $request->all(),
                [
                    'phone' => 'required|string',
                    'verification_code' => 'required|digits:6'
                ]
            );

            if ($validate->fails()) {
                return errorApi('validation error', 401, $validate->errors());
            }

            $user = User::where('phone', $request->phone)->where('verification_code', $request->verification_code)->first();

            if (!$user) {
                return errorApi("Invalid verification code", 400);
            }
            $user->verified = true;
            $user->save();
            return successApi('Verification Successfuly');
        } catch (Exception $e) {
            return errorApi("Error", 500, $e->getMessage());
        }
    }
}
