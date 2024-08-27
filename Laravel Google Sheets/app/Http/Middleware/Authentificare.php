<?php

namespace App\Http\Middleware;

use App\Models\Verifications;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class Authentificare
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Verifications::IsUserAuth($request)) {
            return redirect('');
        }

        if (!Verifications::IsUserOrganizator($request)) {
            return redirect('');
        }
        
        return $next($request);
    }
}
