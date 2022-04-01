<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//         \App\Models\User::factory(10)->create();
        \App\Models\User::create([
            'name'=>"super admin",
            'email'=>"Admin@mail.ru",
            'role'=>'admin',
            'email_verified_at'=> date_create(),
            'password'=>'admin@mail.ru',
            'updated_at'=>NOW(),
            'created_at'=>NOW()
            ]);
//       \App\Models\Post::factory(5)->create();

    }
}
