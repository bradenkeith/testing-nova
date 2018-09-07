<?php

use Faker\Generator as Faker;

$factory->define(App\ReturnedFile::class, function (Faker $faker) {
    return [
       'file_path'  => $faker->name.'.'.$faker->fileExtension(),
       'project_id' => function () {
           return factory(\App\Project::class)->create()->id;
       },
           'email_address_id' => function () {
               return factory(\App\EmailAddress::class)->create()->id;
           },
    ];
});
