<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'costume_sections';
    
    /** JSONに含める属性 */
    protected $visible = [
        'id', 'section', 'characters',
    ];

    /** 登録時にJSONに含める属性 */
    protected $fillable = [
        'section', 
    ];

    /**
     * リレーションシップ - charactersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function characters()
    {
        return $this->hasMany('App\Models\Character');
    }
}
