<?php

namespace App\Controllers;

use Laminas\Diactoros\Response\HtmlResponse;

class BaseController
{
    protected $templateEngine;
    private $defaultData = [
        'templates' => [
            'defaultURL' => '',
            'head' => [
                'title' => 'Curso de Programación PHP 2018'
            ]
        ]
    ];

    public function __construct()
    {
        $loader = new \Twig\Loader\FilesystemLoader('../views');
        $this->templateEngine = new \Twig\Environment($loader, [
            'debug' => true,
            'cache' => false,
        ]);
    }

    public function renderHTML($filename, $data = [])
    {
        $data = $this->setMissingData($data);
        return new HtmlResponse($this->templateEngine->render($filename, $data));
    }

    private function setMissingData($data) {
        $data = $this->setMissingDataFromDefaultData($data);
        $sessionUserId = $_SESSION['userId'] ?? null;
        $data['logged'] = $sessionUserId != null;
        return $data;
    }

    private function setMissingDataFromDefaultData($data) {
        $defaultDataKeys = array_keys($this->defaultData['templates']);
        $dataKeys = array_keys($data);
        foreach ($defaultDataKeys as $defaultDataKey) {
            if (!in_array($defaultDataKey, $dataKeys)) {
                $data[$defaultDataKey] = $this->defaultData['templates'][$defaultDataKey];
            }
        }
        return $data;
    }
}
