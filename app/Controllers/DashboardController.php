<?php

namespace App\Controllers;

class DashBoardController extends BaseController {
    public function indexAction() {
        return $this->renderHTML('dashboard.twig', $this->getViewData());
    }

    private function getViewData() {
        return [
            'logged' => true
        ];
    }
}