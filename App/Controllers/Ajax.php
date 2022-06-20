<?php

namespace App\Controllers;
use \Core\View;
Use \App\Query;
use Respect\Validation\Validator as v;
use voku\helper\AntiXSS;


class Ajax extends \Core\Controller
{
    protected $query;
    private $antiXSS;

    public function __construct()
    {
        $this->antiXss = new AntiXSS();
        $this->query = new Query;
    }//
    public function uploadAction()
    {
        $validation =[ 
            v::regex('/[a-zA-Z ]+/')->validate($_POST['title']) ,
            v::intVal()->validate($_SESSION['userId']),
            v::intVal()->validate($_POST['album']),
            v::regex('/[a-zA-Z ]+/')->validate($_POST['genre']),
            v::intVal()->validate($_POST['duration'])
            ];

            if(in_array(false,$validation)){
                $this->redirect("/upload/ufile");
                return;
            }
        
            $filename = $_FILES['file']['name'];
            $fileTmpName = $_FILES['file']['tmp_name'];
            $fileSize = $_FILES['file']['size'];
            $fileType = $_FILES['file']['type'];
            $fileError = $_FILES['file']['error'];
            $filett=explode(".",$filename);
            $fileExt= strtolower(end($filett));
            $allowedExts = ['mp3','wav','aif'];
            // echo $fileSize."<br>";
            // echo $fileError."<br>";
            // echo $fileExt."<br>";

            if(!in_array($fileExt,$allowedExts) || $fileError !== 0 ){
                echo "error";
                return;
            }
            $song['title']= $this->antiXss->xss_clean($_POST['title']);
            $song['album']= $this->antiXss->xss_clean($_POST['album']);
            $song['genre']= $this->antiXss->xss_clean($_POST['genre']);
            $song['path']="/storage/".$_SESSION['userLoggedIn']."/".str_replace(' ','_',$_POST['title']).".".$fileExt;
            $song['plays']=0;
            $song['duration']= $this->antiXss->xss_clean($_POST['duration']);
            $song['albumOrder']=$this->query->countAlbumOrder($_POST['album'])[0]->counts;
            if(!$song['title'] || !$song['album'] ||!$song['genre'] ||!$song['duration']){
                $this->redirect("/upload/ufile?error=xss");
            }

            $done = $this->query->addAudioToDb($song);
            echo "done";

                if(!$this->folder_exist("storage/".$_SESSION['userLoggedIn'])){
                    mkdir("storage/".$_SESSION['userLoggedIn']);
                    echo "done";
                }
                move_uploaded_file($fileTmpName,"storage/".$_SESSION['userLoggedIn']."/".str_replace(' ','_',$_POST['title']).".".$fileExt);
                $this->redirect('/');            

    }
    public function findSongAction()
    {
        $song = $this->query->getSongById($_POST['songId']);
        echo json_encode($song);
    }

    public function findArtistAction()
    {
        $artist = $this->query->getArtistById($_POST['artistId']);
        echo json_encode($artist);
    }

    public function findAlbumAction()
    {
        $album = $this->query->getAlbumById($_POST['albumId']);
        echo json_encode($album);
    }

    public function updateCountAction()
    {
        $this->query->incrementPlays($_POST['songId']);
    }
    public function allUsersAction()
    {
        
        echo json_encode($this->query->getAllUsers());
    }
    public function banUserAction()
    {
        $id = $_GET['id'];
        $banned= $_GET['banned'];
        var_dump($_GET);
        $this->query->toggleBanUsers($id,$banned);
    }
    function folder_exist($folder)
    {
        // Get canonicalized absolute pathname
        $path = realpath($folder);
    
        // If it exist, check if it's a directory
        if($path !== false AND is_dir($path))
        {
            // Return canonicalized absolute pathname
            return true;
        }
    
        // Path/folder does not exist
        return false;
    }
    function userNumbersAction(){
        echo json_encode($this->query->getNumUsers());
    }
    function songNumbersAction(){
        echo json_encode($this->query->getNumSongs());
    }
    public function addAlbumAction()
{
    $validation =[ 
        v::regex('/[a-zA-Z ]+/')->validate($_POST['title']) ,
        v::regex('/[a-zA-Z ]+/')->validate($_SESSION['userId']),
        v::intVal()->validate($_POST['artist']),
        v::intVal()->validate($_POST['year'])
        ];
        if(in_array(false,$validation)){
            $this->redirect("/upload/album");
            return;
        }
    $album['title']=$_POST['title'];
    $album['path']="/storage/".$_SESSION['userLoggedIn']."/".str_replace(' ','_',$_POST['title']);
    $done = $this->query->addAlbumToDb($album);
    if(!$this->folder_exist("storage/".$_SESSION['userLoggedIn'])){
        mkdir("storage/".$_SESSION['userLoggedIn']);
        echo "done";
    }
    $this->redirect('/');
    
}
}