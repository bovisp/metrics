<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function register()
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

        return view('users.register')->with(['token' => request('token')]);
    }
}
