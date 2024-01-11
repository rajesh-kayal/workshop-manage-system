<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workshop_Session extends Model
{
    protected $table = 'workshop_session';
    protected $primaryKey = 'id';
    protected $fillable = [
        'session_id',
        'workshop_id',
        'maxcapacity',
        'workshopdate',
        'lastregdate',
        'starttime',
        'endtime',
        'created_date',
        'is_deleted'
    ];

public $timestamps = true;

    use HasFactory;
}
