<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MakeUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new user from the command line';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $data = [];

      $data['name'] = $this->ask('Full name');
      $data['email'] = $this->ask('Email address');

      $data['password'] = $data['confirmPassword'] = '';

      do {
        if ($data['password'] !== $data['confirmPassword']) {
          $this->info('Your \'password\' and \'confirm password\' entries are not correct.');
        }

        $data['password'] = $this->secret('Password');
        $data['confirmPassword'] = $this->secret('Confirm password');
      } while ($this->passwordsMatch($data) === false);

      $validator = $this->validateData($data);

      if ($validator->fails()) {
        $this->outputErrors(($validator->errors())->all());
      }

      $user = $this->persistUser($data);

      $this->info("User {$user->name} was successfully created!");
    }

    protected function passwordsMatch($data) {
      return ($data['password'] !== '' && $data['confirmPassword'] !== '') && 
        $data['password'] === $data['confirmPassword'];
    }

    protected function validateData($data) {
      return Validator::make($data, [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8'],
      ]);
    }

    protected function outputErrors($errors) {
      foreach($errors as $error) {
        $this->info($error);
      }

      dd();
    }

    protected function persistUser($user) {
      return User::create([
        'name' => $user['name'],
        'email' => $user['email'],
        'password' => Hash::make($user['password']),
      ]);
    }
}
