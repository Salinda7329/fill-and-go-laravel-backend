<?php

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class VerifyFirebaseSession
{
    public function handle(Request $request, Closure $next)
    {
        $cookie = $request->cookie('firebase_session');

        if (!$cookie) {
            return redirect('/login');
        }

        try {
            $auth = (new Factory)->withServiceAccount(config('services.firebase.credentials'))->createAuth();
            $verified = $auth->verifySessionCookie($cookie, true);
            $request->merge(['firebase_uid' => $verified->uid]);
        } catch (\Throwable $e) {
            return redirect('/login')->withErrors(['session' => 'Invalid or expired session']);
        }

        return $next($request);
    }
}
