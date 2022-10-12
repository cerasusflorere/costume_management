<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color_Class extends Model
{
    protected $table = 'costume_color_classes';
    
    /** JSONに含める属性 */
    protected $visible = [
        'id', 'color_class', 'colors',
    ];

    /** 登録時にJSONに含める属性 */
    protected $fillable = [
        'color_class', 
    ];

    /**
     * リレーションシップ - colorsテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function colors()
    {
        return $this->hasMany('App\Models\Color', 'color_class_id');
    }
}
