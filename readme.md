#Testing Nova
This project is to show examples on my approach to testing Nova. This app is TDD, so everything created in this app should have a corresponding test. Tests are broken up into the the Feature directory under Nova and by model from there.

There is also an API that was utilized for front end access, so the example code of how I went about that is also in this project.


##Project Overview
This is an app I made for a customer that required a dropbox style application with some custom fucntionality. Some of functionality has been scrubbed due to proprietary processes, as well as code being stripped due to premium themese, etc. What is left is a Nova code base focusing on:

* Projects, they can have:
  * Files and Email Addresses
* Email Addresses have an action to be notified they have access to a project
* Email Addresses can receive an email with a signed URL
* Signed URLs can be used to see and download files
* Email Addresses can upload files back to the UI

Nova has administration screens for each model, actions, and relationships described above.

##Setup
1. Unzip the App
2. Run `cp .env.example .env && php artisan key:generate`
3. Drop Nova into the project root at /nova (tested up to Nova release 1.0.12)
4. Run `composer install`
5. Run `php artisan nova:install`
6. Run `vendor/phpunit/phpunit/phpunit` from this directory

There is also one Dusk test to test the one front end interface.
1. Run `php artisan dusk`

Homestead is installed per project here and can be run to view the UI if desired.
1. [Follow these instructions](https://laravel.com/docs/5.7/homestead#per-project-installation)
2. Run `vagrant up`