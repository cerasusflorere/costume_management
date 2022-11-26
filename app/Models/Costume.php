<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Costume extends Model
{
    /** 取得時にJSONに含める属性 */
    protected $visible = [
        'id', 'class_id', 'name', 'kana', 'color_id', 'owner_id', 'location', 'handmade', 'decision', 
        'public_id', 'url', 'usage', 'usage_guraduation','usage_left', 'usage_right', 'created_at', 'updated_at',
        'class', 'color', 'owner', 'costume_comments', 'scenes',
    ];
 
    /** 登録時にJSONに含める属性 */
    protected $fillable = [
        'name', 'class_id', 'kana', 'color_id', 'owner_id', 'location', 'handmade', 'decision', 
        'public_id', 'url', 'usage', 'usage_guraduation', 'usage_left', 'usage_right'
    ];

    public function getCreatedAtAttribute()
    {
        return Carbon::parse($this->attributes['created_at'])->format('Y/m/d H:i');
    }

    public function getUpdatedAtAttribute()
    {
        return Carbon::parse($this->attributes['updated_at'])->format('Y/m/d H:i');
    }

    /**
     * リレーションシップ - classesテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function class()
    {
        return $this->belongsTo('App\Models\Classes', 'class_id');
    }

    /**
     * リレーションシップ - colorsテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function color()
    {
        return $this->belongsTo('App\Models\Color');
    }

    /**
     * リレーションシップ - ownersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo('App\Models\Owner');
    }
    
    /**
     * リレーションシップ - costumes_commetsテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function costume_comments()
    {
        return $this->hasMany('App\Models\Costumes_Comment');
    }

    /**
     * リレーションシップ - scenesテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scenes()
    {
        return $this->hasMany('App\Models\Scene');
    }

    /**
     * リレーションシップ - sceneps_commentsテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function scene_comments()
    {
        return $this->hasManyThrough('App\Models\Scene', 'App\Models\Scenes_Comment');
    }
    
}
