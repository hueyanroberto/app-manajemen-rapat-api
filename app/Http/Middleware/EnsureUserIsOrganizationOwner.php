<?php

namespace App\Http\Middleware;

use App\Models\UserOrganization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsOrganizationOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $userOrganization = UserOrganization::where('organization_id', $request['organization_id'])->where('user_id', $user->id)->first();
        if ($userOrganization->role_id != 1) {
            return response()->json(['status' => 'unauthorized', 'data' => null]);
        }
        return $next($request);
    }
}
