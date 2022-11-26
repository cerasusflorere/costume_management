<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $table = 'costume_owners';
    
    /** JSONに含める属性 */
    protected $visible = [
        'id', 'name', 'costumes',
    ];

    /** 登録時にJSONに含める属性 */
    protected $fillable = [
        'order', 'name',
    ];

    /**
     * リレーションシップ - costumesテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function costumes()
    {
        return $this->hasMany('App\Models\Costume');
    }

    /**
     * リレーションシップ - scenesテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scenes()
    {
        return $this->hasMany('App\Models\Scene');
    }
}
