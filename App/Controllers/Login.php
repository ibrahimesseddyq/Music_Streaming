<?php

namespace App\Controllers;
use \Core\View;
use \App\Models\User;
use \App\Constants;
class Login extends \Core\Controller
{

    public function createAction()
    {
        if(@$_SESSION['userLoggedIn']){
            $this->redirect('/');
        }
        $us = new User();
        $user = $us->authenticate(@$_POST['loginUsername'], @$_POST['loginPassword']); 
        $error;
        if(@$user->banned){
            $this->redirect('/banned/ban');

        }
        else if ($user) {
            
            $_SESSION['userLoggedIn'] = $user->username;
            $_SESSION['userId']= $user->id;
            echo $_SESSION['userLoggedIn'];
            $this->redirect('/');
        } else {
            View::renderTemplate('Register/index.html', [
                'username' => @$_POST['loginUsername'],
                'error' => Constants::$loginFailed
            ]);

        };
    }
}