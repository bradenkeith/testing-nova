<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailAddress extends Model
{
    public function projects()
    {
        return $this->belongsToMany('App\Project');
    }

    public function returnedFiles()
    {
        return $this->hasMany('App\ReturnedFile');
    }
}
