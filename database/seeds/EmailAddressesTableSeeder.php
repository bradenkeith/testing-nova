<?php

use Illuminate\Database\Seeder;

class EmailAddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\EmailAddress::class)->create();
    }
}
