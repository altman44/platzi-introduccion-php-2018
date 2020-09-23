<?php

namespace App\Controllers;

use Respect\Validation\Validator;
use Laminas\Diactoros\Response\RedirectResponse;
// use App\Controllers\IndexController;
use App\Models\{User, Privilege};

class AuthController extends BaseController
{
    private const DEFAULT_PRIVILEGE_ID = 1;
    private const TYPES_OF_ENTRY = [
        'login' => [
            'actionForm' => 'login',
            'actionTextToEntry' => 'Sign In'
        ],
        'register' => [
            'actionForm' => 'register',
            'actionTextToEntry' => 'Sign Up'
        ]
    ];

    public function loginAction($request)
    {
        $view = 'login.twig';
        $viewData = $this->getViewData('login');
        $viewData['responseMessage'] = [
            'text' => '',
            'type' => ''
        ];

        if ($request->getMethod() == 'POST') {
            $viewData['responseMessage']['text'] = 'You entered some incorrect values';
            $viewData['responseMessage']['type'] = 'error';

            $data = $request->getParsedBody();
            if (self::correctKeysInUserData($data, 'login')) {
                $loginValidator = $this->getUserDataValidator('login');

                try {
                    $loginValidator->assert($data);
                    $user = User::searchUser($data);
                    if ($user) {
                        if (password_verify($data['password'], $user->getAttribute('password_hash'))) {
                            $viewData['responseMessage'] = [];
                            return $this->enterTheApp($user->id);
                        } else {
                            $viewData['responseMessage']['text'] = 'Incorrect password.';
                        }
                    }
                } catch (\Exception $err) {
                }
            }
        }
        return $this->renderHTML($view, $viewData);
    }

    public function registerAction($request)
    {
        $viewData = $this->getViewData('register');

        if ($request->getMethod() == 'POST') {
            $viewData['responseMessage'] = [
                'text' => 'You entered some incorrect values',
                'type' => 'error'
            ];

            $data = $request->getParsedBody();
            if (self::correctKeysInUserData($data, 'register')) {
                $registerValidator = $this->getUserDataValidator('register');
                try {
                    $registerValidator->assert($data);
                    $user = User::searchUser($data);
                    if (!$user) {
                        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
                        try {
                            $privilege_id = Privilege::searchPrivilegeId($data['privilege']);
                            $privilege_id = $privilege_id != null ? $privilege_id : self::DEFAULT_PRIVILEGE_ID;
                            $user = new User();
                            $user->username = $data['username'];
                            $user->password_hash = $password_hash;
                            $user->privilege_id = $privilege_id;
                            $user->save();

                            $viewData['responseMessage']['text'] = 'You have registered successfully!';
                            $viewData['responseMessage']['type'] = 'success';
                            return $this->enterTheApp($user->id);
                        } catch (\Illuminate\Database\QueryException $err) {
                            $viewData['responseMessage']['text'] = 'There was a problem creating the account. Please, try again.';
                        } catch (\Exception $err) {
                            $viewData['responseMessage']['text'] = "The privilege entered doesn't exist.";
                        }
                    }
                } catch (\Exception $err) {
                }
            }
        }
        $view = 'register.twig';
        return $this->renderHTML($view, $viewData);
    }

    public function logoutAction() {
        unset($_SESSION['userId']);
        return new RedirectResponse(BASE_ROUTE.'/');
    }

    private function enterTheApp($id) {
        $_SESSION['userId'] = $id;
        return new RedirectResponse(BASE_ROUTE.'/dashboard');
    }

    private function getUserDataValidator($typeOfEntry)
    {
        $validator = Validator::key('username', Validator::stringType()->notEmpty())
            ->key('password', Validator::stringType()->notEmpty());
        if ($typeOfEntry == 'register') {
            $validator = $validator->key('privilege', Validator::stringType()->notEmpty()->containsAny(Privilege::getNames()));
        }
        return $validator;
    }

    private static function correctKeysInUserData($data, $typeOfEntry)
    {
        return array_key_exists('username', $data)
            && array_key_exists('password', $data)
            && ($typeOfEntry == 'login' 
                || ($typeOfEntry == 'register' && array_key_exists('privilege', $data)));
    }

    private function getViewData($typeOfEntry)
    {
        $viewData = [
            'action' => $typeOfEntry,
            'actionForm' => self::TYPES_OF_ENTRY[$typeOfEntry]['actionForm'],
            'actionTextToEntry' => self::TYPES_OF_ENTRY[$typeOfEntry]['actionTextToEntry'],
        ];
        if ($typeOfEntry == 'register') {
            $viewData['privileges'] = Privilege::orderPrivileges();
        }
        return $viewData;
    }
}
