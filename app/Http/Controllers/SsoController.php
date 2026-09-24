<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;

class SsoController extends Controller
{
    public function handleCallback(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return response()->json(['error' => 'SSO Token Missing'], 400);
        }

        // 1. Validate token with Laravel SSO Server
        $response = Http::withHeaders([
            'Host' => 'hsel-sso.ddev.site',
            'Accept' => 'application/json'
        ])
        ->withoutVerifying()
        ->post('https://10.27.101.103/api/sso/validate', [
            'token' => $token
        ]);

        $responseData = $response->json();

        // 2. Foolproof check: If it has 'valid' and it is true, proceed!
        if (is_array($responseData) && isset($responseData['valid']) && $responseData['valid'] == true) {
            $ssoUser = $responseData['user'];
            
            // Safely extract username regardless of AD format
            $rawIdentifier = $ssoUser['username'] ?? $ssoUser['email'] ?? 'unknown_user';
            $shortUsername = str_contains($rawIdentifier, '@') 
                ? explode('@', $rawIdentifier)[0] 
                : $rawIdentifier;

            $shortUsername = substr($shortUsername, 0, 30);

            // Find the user, or create them if they are new
            $user = User::where('login_username', $shortUsername)->first();

            if (!$user) {
                $user = User::create([
                    'login_username' => $shortUsername,
                    'name'           => $ssoUser['name'] ?? $shortUsername,
                    'role'           => 'user', 
                    'login_pwd'      => md5(Str::random(16)),
                    'login_stamp'    => now()
                ]);
            } else {
                $user->update([
                    'name'        => $ssoUser['name'] ?? $shortUsername,
                    'login_stamp' => now()
                ]);
            }

            // ACTUALLY LOG THEM IN AND SET SESSION
            Auth::login($user);
                        
            $request->session()->regenerate(); 

            // Explicitly save the ID to bypass $fillable model restrictions
            $user->current_session_id = $request->session()->getId();
            $user->save();

            // REDIRECT TO DASHBOARD
            return redirect()->route('dashboard');
        }

        // Return direct error payload for debugging if validation fails
        return response()->json([
            'error' => 'SSO Validation Failed',
            'sso_response' => $responseData
        ], 403);
    }
}