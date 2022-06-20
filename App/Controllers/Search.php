<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\Album;
use \App\Models\Artist;
use \App\Models\Song;
use \App\Query;

class Search extends \Core\Controller
{
    public function indexAction()
    {
        $query = new Query;

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $url = 'Search/mainContentSearch.html';
        } else {
            $url = '/Search/index.html';
        }
        
        View::renderTemplate($url, [
            'ids' => $query->getRandomSongsIds()
            ,            "userlogged"=> @$_SESSION["userLoggedIn"],

        ]);
    }

    public function executeAction()
    {
        if(!@$_SESSION['userLoggedIn']){
            $this->redirect('/login/create');
        }
        $query = new Query;
        View::renderTemplate('Search/artistsSearch.html', [
            'artists' => $query->searchArtistsInfo($_POST['search']),
            'songs' => $query->searchSongsInfo($_POST['search']),
            'idsAlbum' => json_encode($query->searchSongsId($_POST['search'])),
            'albums' => $query->searchAlbumsInfo($_POST['search']),
            "userlogged"=> @$_SESSION["userLoggedIn"]

        ]);
    }
}
