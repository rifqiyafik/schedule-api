<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'pattern', 'start_date'];

    protected $casts = [
        'pattern' => 'array',
        'start_date' => 'date',
    ];
}
