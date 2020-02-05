<?php

use App\Model\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        do {
            $key = Str::random(32);
        } while (User::where('apikey', $key)->count() > 0);
        $user->name = 'system-cache';
        $user->email = 'system-cache@repo.local';
        $user->password = Hash::make(empty($password) ? Str::random(16) : $password);
        $user->apikey = $key;
        $user->save();
    }
}
