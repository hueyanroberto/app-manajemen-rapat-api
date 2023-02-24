<?php

namespace App\Http\Middleware;

use App\Models\UserOrganization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsInOrganization
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
        $organization_id = $request['organization_id'];
        $user = Auth::user();
        $userOrganization = UserOrganization::where('user_id', $user->id)->where('organization_id', $organization_id)->first();
        if (!$userOrganization) {
            return response()->json(['status' => 'unauthorized', 'data' => null]);
        }
        return $next($request);
    }
}
