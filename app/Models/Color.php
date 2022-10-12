<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $table = 'costume_colors';
    
    /** JSONに含める属性 */
    protected $visible = [
         'color_class', 'id', 'color_class_id', 'color', 'costumes',
    ];

    /** 登録時にJSONに含める属性 */
    protected $fillable = [
        'color_class_id', 'color',
    ];

    /**
     * リレーションシップ - color_classesテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function color_class()
    {
        return $this->belongsTo('App\Models\Color_Class', 'color_class_id', 'id', 'colors');
    }


    /**
     * リレーションシップ - costumesテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function costumes()
    {
        return $this->hasMany('App\Models\Costume', 'color_id');
    }
}
