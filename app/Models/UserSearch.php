<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserSearch extends Model
{
    use HasFactory;
    /**
     * Set the user_id.
     *
     * @return void
     */
    public function setUserIdAttribute()
    {
        $this->attributes['user_id'] = Auth::user()->id;
    }
}
