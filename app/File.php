<?php

namespace App;

use App\Events\FileDeleted;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $dispatchesEvents = [
        'deleted' => FileDeleted::class,
    ];

    public function project()
    {
        return $this->belongsTo('App\Project');
    }
}
