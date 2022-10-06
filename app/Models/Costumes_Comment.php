<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Costumes_Comment extends Model
{
    protected $table = 'costumes_comments';
    
    /** 取得時にJSONに含める属性 */
    protected $visible = [
        'id', 'costume_id', 'memo',
    ];

    /** JSONに含める属性 */
    protected $fillable = [
        'costume_id', 'memo',
    ];

    /**
     * リレーションシップ - costumesテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function costume()
    {
        return $this->belongsTo('App\Models\Costume');
    }
}
