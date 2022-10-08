<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Classes extends Model
{
    protected $table = 'costume_classes';

    /** 取得時にJSONに含める属性 */
    protected $visible = [
        'id', 'order', 'class',
        'costumes',
    ];
 
    /** 登録時にJSONに含める属性 */
    protected $fillable = [
        'order', 'class'
    ];
    
    /**
     * リレーションシップ - costumesテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function costumes()
    {
        return $this->hasMany('App\Models\Costume', 'class_id');
    }
}
