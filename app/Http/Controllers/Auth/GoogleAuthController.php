<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Google\Client as GoogleClient;
use Google\Service\Oauth2;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    private function getClient(): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google_client.client_id'));
        $client->setClientSecret(config('services.google_client.client_secret'));
        $client->setRedirectUri(config('services.google_client.redirect_uri'));
        $client->addScope('email');
        $client->addScope('profile');

        return $client;
    }

    public function redirect(): RedirectResponse
    {
        return redirect()->away($this->getClient()->createAuthUrl());
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()->route('home')
                ->with('status', 'Google sign-in was cancelled.');
        }

        $googleClient = $this->getClient();
        $token = $googleClient->fetchAccessTokenWithAuthCode($request->get('code'));

        if (isset($token['error'])) {
            return redirect()->route('home')
                ->with('status', 'Google authentication failed. Please try again.');
        }

        $googleClient->setAccessToken($token);
        $oauth2 = new Oauth2($googleClient);
        $googleUser = $oauth2->userinfo->get();

        $googleId = $googleUser->getId();
        $email = $googleUser->getEmail();
        $name = $googleUser->getName();
        $avatar = $googleUser->getPicture();

        // Find existing client by google_id or email
        $client = Client::where('google_id', $googleId)->first();

        if (! $client) {
            $client = Client::where('email', $email)->first();

            if ($client) {
                // Link Google account to existing email-registered client
                $client->update([
                    'google_id' => $googleId,
                    'avatar' => $avatar,
                ]);
            } else {
                // Create new client
                $client = Client::create([
                    'google_id' => $googleId,
                    'name' => $name,
                    'email' => $email,
                    'avatar' => $avatar,
                    'password' => bcrypt(Str::random(24)),
                    'email_verified_at' => now(),
                ]);
            }
        } else {
            // Update avatar on each login
            $client->update(['avatar' => $avatar]);
        }

        Auth::login($client, remember: true);

        return redirect()->intended(route('dashboard'));
    }
}
