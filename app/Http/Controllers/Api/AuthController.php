<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    
    public function register(Request $request)
    {
        // return response()->json('dd');
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'phone' => 'required|string|unique:users',
            'password' => 'required|string',
        ]);
        // return response()->json('dd');

        // Create user instance
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'status' => false, // User is inactive until OTP verification
        ]);

        if ($user->save()) {
            // Generate OTP (4-digit number)
            $otp = rand(1000, 9999); 

            // Save OTP to database with expiration time
            Otp::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'expires_at' => now()->addMinutes(2), // OTP expires in 2 minutes
            ]);

            // Send OTP via SMS using curl
            $url = "https://msg.elitbuzz-bd.com/smsapi";
            $data = [
                "api_key" => "C200931167833878873d89.46775453", // Your API key
                "type" => "text", // Content type (assumed as 'text', but please confirm with API documentation)
                "contacts" => $request->phone, // Phone number of the user
                "senderid" => "8809601012461", // Sender ID (replace with actual sender ID)
                "msg" => "Your OTP is: $otp", // Message body
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // Format data correctly
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            return response()->json([
                'message' => 'Successfully created user! Check your phone for the OTP.',
                'otp_response' => $response, // Optionally return the response for debugging
            ], 201);
        } else {
            return response()->json(['error' => 'Provide proper details'], 400);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:4',
        ]);
        $otp = Otp::where('otp', $request->otp)->first();

        if (!$otp) {
            return response()->json(['message' => 'OTP not found.'], 404);
        }

        $otpRecord = Otp::where('user_id', $otp->user_id)
                        ->where('otp', $request->otp)
                        ->where('expires_at', '>', now())
                        ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        $usercheck = User::where('id',$otp->user_id)->first();
        $usercheck->status = true;
        $usercheck->save();

        $otpRecord->delete();

        $user =  $usercheck;
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        return response()->json([
            'message' => 'OTP verified successfully. User is now active.',
            'user' => $user, // Optionally return user data
            'accessToken' => $token,
            'token_type' => 'Bearer',
        ]);
    }



    public function login(Request $request) 

    {
        // Validate the incoming data
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Retrieve credentials
        $credentials = request(['email', 'password']);
        
        // Attempt authentication
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        // Get authenticated user
        $user = $request->user();

        // Check if user is active (status = 1)
        if ($user->status === 0) {
            return response()->json([
                'message' => 'Your account is inactive. Please verify your account.'
            ], 403); // 403 Forbidden because the user is trying to access with an inactive account
        }

        // Generate access token
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->plainTextToken;

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'accessToken' => $token,
            'token_type' => 'Bearer',
        ]);
    }


    public function user(Request $request) {
        return response()->json($request->user());
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
