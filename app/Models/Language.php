<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model {
    protected $table = 'languages';

    public function getDate() {
        return $this->date_beginning;
    }

    public function getDateAsString() {
        return $this->date_beginning->getDateAsString();
    }
}