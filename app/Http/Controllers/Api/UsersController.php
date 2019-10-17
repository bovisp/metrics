<?php

namespace App\Http\Controllers\Api;

use App\User;
use Carbon\Carbon;
use App\Mail\SendInvite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    public function index()
    {
      return User::select('id', 'name', 'email')->get();
    }

    public function invite()
    {
      DB::table('invite_tokens')->insert([
        'token' => $token = Str::random(40),
        'created_at' => Carbon::now()
      ]);
      
      Mail::to(request('email'))->send(new SendInvite($token));

      return response('Invite sent.');
    }
}
