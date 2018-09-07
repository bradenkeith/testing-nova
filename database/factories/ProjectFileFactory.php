<?php

use Faker\Generator as Faker;

$factory->define(App\ProjectFile::class, function (Faker $faker) {
    return [
       'name'       => $faker->sentence(3),
       'file_path'  => $faker->name.'.'.$faker->fileExtension(),
       'project_id' => function () {
           return factory(\App\Project::class)->create()->id;
       },
    ];
});
