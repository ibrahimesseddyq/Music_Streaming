<?php

namespace App\Controllers;
use \Core\View;
use \App\Models\Album;
use \App\Models\Artist;
use \App\Models\Song;
Use \App\Query;

class Albums extends \Core\Controller
{

    public function showAction()
    {
        if(!@$_SESSION['userLoggedIn']){
            $this->redirect('/login/create');
        }
        $album = new Album($this->route_params['id']);
        $query = new Query;

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $url = 'Albums/mainContentAlbums.html';
        } else {
            $url = '/Albums/index.html';
        }
        $artis=$query->getArtistById($_SESSION['userId']);
        if(is_object($artis)){
            $artis=$artis->name;
        }
        View::renderTemplate($url, [
            'albumTitle' => $album->getTitle(),
            'artist' =>$artis ,
            'number' => $query->getNumberOfSongs($this->route_params['id']),
            'artwork' => $album->getArtworkPath(),
            'songs' => $album->getSongsInfo(),
            'idsAlbum' => $album->getAlbumSongsIds(),
            'ids' => $query->getRandomSongsIds(),
            "userlogged"=> @$_SESSION["userLoggedIn"],

        ]);
    }
    public function listAction()
    {
        if(!@$_SESSION['userLoggedIn']){
            $this->redirect('/login/create');
        }
        $query = new Query;
        View::renderTemplate('Albums/list.html', [
            'albums' => @$query->getAllWhere("albums", "artist =". $_SESSION['userId']) ?? [],
            'albumcolumns' => @array_keys(get_object_vars($query->getAllWhere("albums", "artist =". $_SESSION['userId'])[0])) ?? [],
            "userlogged"=> @$_SESSION["userLoggedIn"],
            "myalbum"=>true

        ]);
}
    public function addAlbumAction(){
        if(!@$_SESSION['userLoggedIn']){
            $this->redirect('/login/create');
        }

        $query = new Query;
        $query->addAlbum($_POST['title'], $_POST['genre'], $_FILES);
        $this->redirect('/albums/list');
    }
    public function deleteAlbumAction(){
        $id = $_GET['id'];
        if(!@$_SESSION['userLoggedIn']){
            $this->redirect('/login/create');
        }
        $query = new Query;
        $query->deleteAlbum($id);
        $this->redirect('/albums/list');
    }
}