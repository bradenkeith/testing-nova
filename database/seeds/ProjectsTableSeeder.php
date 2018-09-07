<?php

use Illuminate\Database\Seeder;

class ProjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Project::class, 5)
            ->create()
            ->each(function ($project) {
                $project->emailAddresses()->attach(factory(App\EmailAddress::class, 10)->create());
                factory(App\ProjectFile::class, 3)->create(['project_id'=>$project->id]);
            });
    }
}
