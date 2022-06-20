<?php

namespace App\Controllers;
use \Core\View;
use \App\Models\User;
use \App\Constants;

class Banned extends \Core\Controller
{

    public function banAction()
    {
        

            View::renderTemplate('Banned/index.html',[
                "userlogged"=> @$_SESSION["userLoggedIn"]

            ]);
    }
}