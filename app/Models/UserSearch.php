<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class UserSearch extends Model
{
    use HasFactory;

    public const STATUS_COMPLETE = 'complete';

    /**
     * @param $query
     * @return mixed
     */
    public function scopeCurrentUser($query)
    {
        return $query->where('user_id', Auth::user()->id);
    }

    /**
     * Set the user_id.
     *
     * @return void
     */
    public function setUserIdAttribute()
    {
        $this->attributes['user_id'] = Auth::user()->id;
    }

    /**
     * Relationship.
     *
     * @return void
     */
    public function searchResults(): HasMany
    {
        return $this->hasMany(UserSearchKeywordResult::class, 'user_search_id');
    }

    /**
     * @return HasMany
     **/
    public function searchIterations(): HasMany
    {
        return $this->hasMany(SearchIteration::class, 'user_search_id');
    }
}
