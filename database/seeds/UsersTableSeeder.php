<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $iExist = App\User::where('email', 'youremail@address.com')->count();

        if ($iExist == 0) {
            $user = factory(App\User::class)->create([
                'name'     => 'Your Name',
                'email'    => 'youremail@address.com',
                'password' => bcrypt('Af2URF2oCp'),
            ]);
            $user->assignRole('super_administrator');
        }
    }
}
