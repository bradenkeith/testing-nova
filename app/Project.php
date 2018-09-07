<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public function projectFiles()
    {
        return $this->hasMany('App\ProjectFile');
    }

    public function emailAddresses()
    {
        return $this->belongsToMany('App\EmailAddress');
    }

    public function returnedFiles()
    {
        return $this->hasMany('App\ReturnedFile');
    }
}
