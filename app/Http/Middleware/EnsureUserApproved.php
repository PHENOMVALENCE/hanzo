<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        if ($request->user()->status !== 'approved' && ! $request->user()->hasRole('admin')) {
            return redirect()->route('pending-approval');
        }

        return $next($request);
    }
}
