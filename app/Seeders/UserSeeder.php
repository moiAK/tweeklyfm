<?php namespace App\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->username = "ssx";
        $user->name = "Scott Wilcox";
        $user->email = "scott@dor.ky";
        $user->password = Hash::make("password");
        $user->save();
        $this->command->info("User created");
    }
}
