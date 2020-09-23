<?php

namespace App\Controllers;

use App\Models\{Aptitude, GeneralData, Language};
use App\Util\DateClass;

class IndexController extends BaseController
{
    private const MIN_MONTHS_OF_EXPERIENCE = 12;
    private $competences = [];

    public function __construct()
    {
        parent::__construct();
        $this->competences = [
            'aptitudes' => Aptitude::all(),
            'languages' => Language::all()
        ];
    }

    public function indexAction()
    {
        return $this->renderHTML('index.twig', $this->getViewData());
    }

    private function getViewData()
    {
        return [
            'head' => [
                'title' => 'Curso de Introducción PHP 2018'
            ],
            'generalData' => GeneralData::all()[0],
            'aptitudes' => $this->competences['aptitudes'],
            'languages' => $this->getAllowedLanguages()
        ];
    }

    private function getAllowedLanguages()
    {
        $allowedLanguages = [];
        foreach ($this->competences['languages'] as $language) {
            $duration = DateClass::getDuration($language->date_beginning);
            $monthsOfExperience = ($duration->y * 12) + $duration->m;
            if ($monthsOfExperience >= self::MIN_MONTHS_OF_EXPERIENCE) {
                $experience = DateClass::getDurationAsString($duration, true);
                array_push($allowedLanguages, [
                    'name' => $language->name,
                    'experience' => $experience
                ]);
            }
        }
        return $allowedLanguages;
    }
}
