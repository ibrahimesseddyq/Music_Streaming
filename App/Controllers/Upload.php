<?php

namespace App\Controllers;
use \Core\View;
use \App\Models\User;
use \App\Constants;

class Upload extends \Core\Controller
{

    public function ufileAction()
    {
        if(!@$_SESSION['userLoggedIn']){
            $this->redirect('/login/create');
        }

            View::renderTemplate('Upload/index.html', [
                "userlogged"=> @$_SESSION["userLoggedIn"],
                "uploadpage"=>true,
                "xss"=>@$_GET['error']
            ]);
    }
}