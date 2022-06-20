<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Album;
use \App\Query;

class Home extends \Core\Controller
{
    public function indexAction()
    {
        if(!@$_SESSION['userLoggedIn']){
            $this->redirect('/login/create');
        }
        $query = new Query();
       
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $url = '/Home/mainContent.html';
        } else {
            $url = '/Home/index.html';
        }
        View::renderTemplate($url, [
                'albums' => $query->getAll("albums"),
                'ids' => $query->getRandomSongsIds(),
                "userlogged"=> @$_SESSION["userLoggedIn"]
            ]);
    }
    public function logoutAction(){
            unset($_SESSION['userLoggedIn']);
            unset($_SESSION['userId']);

            $this->redirect("/login/create");
        
    }
}
function getRandomSongsIds(){  
    $query = new Query();
    return $query->getRandomSongsIds();
}