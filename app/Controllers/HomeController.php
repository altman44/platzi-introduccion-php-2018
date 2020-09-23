<?php

namespace App\Controllers;

class HomeController extends BaseController {
    public function homeAction() {
        return $this->renderHTML('home.twig');
    }

    private function getViewData() {
        return [];
    }
}