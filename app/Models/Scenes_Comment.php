<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scenes_Comment extends Model
{
    protected $table = 'costume_scenes_comments';
    
    /** 取得時にJSONに含める属性 */
    protected $visible = [
        'id', 'scene_id', 'memo',
    ];

    /** JSONに含める属性 */
    protected $fillable = [
        'scene_id', 'memo',
    ];

    /**
     * リレーションシップ - scenesテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function scene()
    {
        return $this->belongsTo('App\Models\Scene');
    }
}
