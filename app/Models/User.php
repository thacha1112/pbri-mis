<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $fillable = ['personnals_id'];

    public function personnal()
    {
        return $this->belongsTo(\App\Models\Common\Personnel::class, 'personnals_id', 'id');
    }

    // สร้าง Accessor ให้เรียก $user->email ได้เหมือนปกติ
    public function getEmailAttribute()
    {
        return $this->personnal ? $this->personnal->email : null;
    }
}