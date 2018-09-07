<?php

namespace App;

class ReturnedFile extends File
{
    protected $fillable = [
        'project_id',
        'email_address_id',
        'file_path',
    ];

    public function emailAddress()
    {
        return $this->belongsTo('App\EmailAddress');
    }
}
