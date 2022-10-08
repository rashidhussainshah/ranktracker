<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSearchKeywordResult extends Model
{
    use HasFactory;
    public $fillable = [
        'rank_group',
        'rank_absolute',
        'domain',
        'title',
        'description',
        'url',
        'breadcrumb',
        'report_id',
        'features_data',
        'back_links',
        'live_back_links',
        'referring_domains',
        'organic_traffic',
        'paid_traffic',
        'backlink_page_no',
        'type',
    ];

}
