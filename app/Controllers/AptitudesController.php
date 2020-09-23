<?php

namespace App\Controllers;

use Respect\Validation\Validator;
use App\Models\Aptitude;

class AptitudesController extends BaseController
{
    public function getAddAptitudeAction($request)
    {
        $responseMessage = $this->handleAddAptitudeAction($request);        
        $viewData = $this->getViewData();
        $viewData['responseMessage'] = $responseMessage;
        return $this->renderHTML('addAptitude.twig', $viewData);
    }

    private function getViewData()
    {
        return [
            'head' => [
                'title' => 'Add aptitude'
            ]
        ];
    }

    private function handleAddAptitudeAction($request)
    {
        $responseMessage = [];

        if ($request->getMethod() == 'POST') {
            $postData = $request->getParsedBody();
            $aptitudeValidator = Validator::key('name', Validator::stringType()->notEmpty());

            try {
                $aptitudeValidator->assert($postData);
                $files = $request->getUploadedFiles();
                $logo = $files['logo'];
                
                $aptitude = new Aptitude();
                $aptitude->name = $postData['name'];
                $aptitude->important = in_array('important', array_keys($postData));
                if ($logo->getError() == UPLOAD_ERR_OK) {
                    $filename = $logo->getClientFilename();
                    $logoDir = "uploads/$filename";
                    $logo->moveTo($logoDir);
                    $aptitude->logo_filename = $filename;
                }
                $aptitude->save();

                $responseMessage['text'] = 'Aptitude saved successfully';
                $responseMessage['type'] = 'success';
            } catch (\Exception $e) {
                $responseMessage['text'] = 'Incorrect inputs!';
                $responseMessage['type'] = 'error';
            }
        }
        return $responseMessage;
    }
}
