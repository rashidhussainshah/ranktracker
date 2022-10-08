<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchIteration extends Model
{
    use HasFactory;
    public $fillable = [
        'user_search_id',
        'task_id',
        'search_results' ,
    ];
}
