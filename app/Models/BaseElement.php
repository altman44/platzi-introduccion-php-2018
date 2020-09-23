<?php

namespace App\Models;

class BaseElement {
    private $name;

    public function setName($name) {
        if ($name) {
            $this->name = $name;
        }
    }

    public function getName() {
        return $this->name;
    }
}