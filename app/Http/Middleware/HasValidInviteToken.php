<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HasValidInviteToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      $token = DB::table('invite_tokens')
        ->where('token', request('token'))
        ->get()
        ->first();

      if (!$token) {
        abort(403, 'This invite has expired');
      }

      $tokenDate = new Carbon($token->created_at);
      $currentDate = Carbon::now();

      if ($currentDate->diffInHours($tokenDate) > 24) {
        abort(403, 'This invite has expired');
      }

      return $next($request);
    }
}
