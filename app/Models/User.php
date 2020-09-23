<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $guarded = ['id', 'privilege_id'];
    public $timestamps = false;

    public static function searchUser($data) {
        return self::select('*')->where('username', $data['username'])->first();
    }
}
