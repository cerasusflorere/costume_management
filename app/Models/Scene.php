<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scene extends Model
{
    /** 取得時にJSONに含める属性 */
    protected $visible = [
        'id', 'character_id', 'costume_id', 
        'first_page', 'final_page', 'usage', 'usage_guraduation', 'usage_left', 'usage_right', 'created_at', 'updated_at',
        'character', 'costume', 'scene_comments',
    ];

    /** JSONに含める属性 */
    protected $fillable = [
        'character_id', 'costume_id', 
        'first_page', 'final_page', 'usage', 'usage_guraduation', 'usage_left', 'usage_right'
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
     * リレーションシップ - charactersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function character()
    {
        return $this->belongsTo('App\Models\Character');
    }

    /**
     * リレーションシップ - costumesテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function costume()
    {
        return $this->belongsTo('App\Models\Costume');
    }

    /**
     * リレーションシップ - scenes_commentsテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scene_comments()
    {
        return $this->hasMany('App\Models\Scenes_Comment');
    }
}
