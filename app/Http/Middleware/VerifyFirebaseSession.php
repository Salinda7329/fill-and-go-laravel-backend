<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;

class VerifyFirebaseSession
{
    public function handle(Request $request, Closure $next)
    {
        $cookie = $request->cookie('firebase_session');

        if (!$cookie) {
            Log::warning('No firebase_session cookie found', ['url' => $request->url()]);
            return redirect('/login')->withErrors(['session' => 'No session found']);
        }

        try {
            $auth = (new Factory)->withServiceAccount(config('services.firebase.credentials'))->createAuth();
            $verified = $auth->verifySessionCookie($cookie, true);
            $request->merge(['firebase_uid' => $verified->claims()->get('sub')]);
            Log::info('Session cookie verified', ['firebase_uid' => $verified->claims()->get('sub'), 'url' => $request->url()]);
        } catch (\Throwable $e) {
            Log::error('Session verification failed:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => $request->url()
            ]);
            return redirect('/login')->withErrors(['session' => 'Invalid or expired session']);
        }

        return $next($request);
    }
}
