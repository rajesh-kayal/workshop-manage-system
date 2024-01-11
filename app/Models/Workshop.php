<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop extends Model
{
    protected $table = 'workshop';
    protected $primaryKey = 'id';
    protected $fillable = [
        'title',
        'department',
        'link',
        'contentarea',
        'description',
        'presenter',
        'contactinfo',
        'location',
        'audiance',
        'grade',
        'series',
        'image',
        'mode',
        'platform',
        'begin_registration_date',
        'end_registration_date',
        'status'
    ];

public $timestamps = true;

    use HasFactory;
}
