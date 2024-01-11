<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employee';
    protected $primaryKey = 'id';
    protected $fillable = [
        'employeeid',
        'first_name',
        'last_name',
        'email',
        'location',
        'department',
        'type',
        'is_active',
        'is_deleted',
        'last_login'
    ];

public $timestamps = true;

    use HasFactory;
}
