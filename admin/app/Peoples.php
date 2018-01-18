<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Peoples extends Model
{
    protected $fillable=[
    	'lname',
    	'fname',
    	'sex',
    	'email',
    	'phone',
    	'password'
    ];
    public $timestamps=false;
}
