<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Privilege extends Model {
    protected $table = 'privileges';

    public static function searchPrivilegeId($privilege) {
        $privilegeFound = self::select('id')->where('name', $privilege)->first();
        return $privilegeFound == null ? $privilegeFound : $privilegeFound->getAttribute('id');
    }

    public static function orderPrivileges() {
        return self::orderBy('view_importance', 'asc')->get();
    }

    public static function getNames() {
        $names = [];
        foreach(self::select('name')->get()->toArray() as $row) {
            array_push($names, $row['name']);
        }
        return $names;
    }
}