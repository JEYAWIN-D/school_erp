<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applied to student/parent portal routes.
 * Denies access when the linked student has portal_blocked = true.
 *
 * Usage (in routes/portal.php or bootstrap/app.php):
 *   ->middleware('portal.access')
 *
 * Register in bootstrap/app.php:
 *   ->withMiddleware(function ($m) {
 *       $m->alias(['portal.access' => CheckPortalAccess::class]);
 *   })
 */
class CheckPortalAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && method_exists($user, 'student') && $user->student?->portal_blocked) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your portal access has been restricted due to pending fee dues. '
                         . 'Please contact the school office. Reason: '
                         . ($user->student->portal_block_reason ?? 'Fee defaulter'),
            ]);
        }

        return $next($request);
    }
}
