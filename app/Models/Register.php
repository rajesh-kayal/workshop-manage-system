<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Register extends Model
{   protected $table = 'register';
    protected $primaryKey = 'id';
    protected $fillable = [
        'employee_id',
        'workshop_id',
        'session_id',
        'created_date',
        'status',
        'deregister_reason',
        'modify_by',
             ];

public $timestamps = true;

    use HasFactory;
}
