<?php

namespace App;

use \Core\DB;
use Respect\Validation\Validator as v;

class Query
{
    protected $db;
    
    public function __construct()
    {
        $this->db = new DB;
    }

    public function getAll($table)
    {
        $this->db->query("SELECT * FROM {$table}");

        $results = $this->db->resultset();

        return $results;
    }
    public function getAllWhere($table,$where)
    {
        $this->db->query("SELECT * FROM {$table}" . " WHERE {$where}");

        $results = $this->db->resultset();

        return $results;
    }
    public function getNumberOfSongs($id)
    {
        $this->db->query('SELECT id FROM songs WHERE album = :album');

        $this->db->bind(':album', $id);

        return  count($this->db->resultset());
    }

    public function getRandomSongsIds()
    {
        $this->db->query('SELECT id FROM songs ORDER BY RAND() LIMIT 10');

        return json_encode($this->db->resultset());
    }

    public function getSongById($id)
    {
        $this->db->query('SELECT * FROM songs WHERE id = :id');

        $this->db->bind(':id', $id);

        return $this->db->single();

    }

    public function getArtistById($id)
    {
        $this->db->query('SELECT * FROM artists WHERE id = :id');

        $this->db->bind(':id', $id);

        return $this->db->single();

    }

    public function getAlbumById($id)
    {
        $this->db->query('SELECT * FROM albums WHERE id = :id');

        $this->db->bind(':id', $id);

        return $this->db->single();

    }

    public function incrementPlays($id)
    {
        $this->db->query('UPDATE songs SET plays = plays + 1 WHERE id = :id');

        $this->db->bind(':id', $id);

        $this->db->execute();

    }
    /*
                $song['title']=$_POST['title'];
            $song['artist']=$_SESSION['userId'];
            $song['album']=$_POST['album'];
            $song['genre']=$_POST['genre'];
            $song['path']="/".$_SESSION['userLoggedIn']."/".str_replace(' ','_',$_POST['title']).".".$fileExt;
            $song['plays']=0;
            $song['duration']=$_POST['duration'];
            $song['albumOrder']=$this->query->countAlbumOrder($_POST['album'])[0]->counts;
        */
    public function addAudioToDb($songInfo){
        $title = $songInfo['title'];
        $artist = $_SESSION['userId'];
        $album = $songInfo['album'];
        $genre = $songInfo['genre'];
        $path = $songInfo['path'];
        $plays = $songInfo['plays'];
        $duration = $songInfo['duration'];
        $albumOrder = $songInfo['albumOrder'];
        $this->db->query("INSERT INTO songs (title, artist, album,genre,path,plays,duration,albumOrder) VALUES ('$title', $artist, $album, '$genre','$path',$plays,$duration,$albumOrder)");
        $this->db->execute();
    }
    public function addAlbumToDb($songInfo){
        $title = $songInfo['title'];
        $genre = $songInfo['genre'];
        $artworkPath = $songInfo['artworkPath'];
        $artist = $songInfo['artist'];

        $this->db->query("INSERT INTO albums (title, artist,genre,artworkPath) VALUES ('$title', $artist,'$genre','$artworkPath')");
        $this->db->execute();
    }
    public function findArtistByUserId($userId){
        $this->db->query("SELECT * FROM artists WHERE user_fk = :userId");
        $this->db->bind(':userId', $userId);
        return $this->db->single()->name;
    }
    public function countAlbumOrder($albumId)
    {
        $this->db->query('SELECT count(*) as counts FROM songs WHERE album = :album ');
        $this->db->bind(':album', $albumId);
        return $this->db->resultset();

    }
    public function getAllUsers()
    {
        $this->db->query('SELECT * FROM users ');
        return $this->db->resultset();

    }
    public function toggleBanUsers($id,$banned)
    {
        $ban =$banned == 0 ? 1 : 0;
        $this->db->query('UPDATE users SET banned ='.$ban." WHERE id =".$id);
        return $this->db->execute();

    }
    public function getNumUsers()
    {
        $this->db->query("SELECT COUNT(*) as ct FROM users");
        
        return $this->db->resultset()[0]->ct;

    }
    public function getNumSongs()
    {
        $this->db->query("SELECT COUNT(*) as ct FROM songs");
        
        return $this->db->resultset()[0]->ct;

    }
    public function getAlbumSongs($id) 
    {

        $this->db->query('SELECT id FROM songs WHERE album = :album ORDER BY albumOrder ASC');

        $this->db->bind(':album', $id);

        return $this->db->resultset();
    }

    public function getArtistSongs($id) 
    {

        $this->db->query('SELECT id FROM songs WHERE artist = :artist ORDER BY plays ASC');

        $this->db->bind(':artist', $id);

        return $this->db->resultset();
    }

    public function getArtistAlbums($id)
    {
        $this->db->query('SELECT * FROM albums WHERE artist = :artist');

        $this->db->bind(':artist', $id);

        return $this->db->resultSet();
    }

    public function searchArtistsInfo($data)
    {
        $this->db->query("SELECT * FROM artists WHERE name LIKE '$data%' LIMIT 10");

        return $this->db->resultSet();

    }
    public function searchAlbumsInfo($data)
    {
        $this->db->query("SELECT * FROM albums WHERE title LIKE '$data%' LIMIT 10");

        return $this->db->resultSet();

    }

    public function searchSongsInfo($data)
    {
        $this->db->query("SELECT songs.id as id, songs.title as title, songs.duration as duration, artists.name as artist FROM songs, artists WHERE songs.artist = artists.id AND songs.title LIKE '$data%' LIMIT 10");

        return $this->db->resultSet();

    }

    public function searchSongsId($data)
    {
        $this->db->query("SELECT id FROM songs WHERE title LIKE '$data%' LIMIT 10");

        return $this->db->resultSet();

    }

    public function  getLastId(){
        $db = new DB;
        $db->query("SELECT LAST_INSERT_ID() as id from users");
        return $db->resultset()[0]->id;
    }

    public function addAlbum($title,$genre,$fileVar){
        $validation =[ 
            v::regex('/[a-zA-Z ]+/')->validate($_POST['title']) ,
            v::intVal()->validate($_SESSION['userId']),
            v::regex('/[a-zA-Z ]+/')->validate($_POST['genre']),
            ];
            if(in_array(false,$validation)){
                $this->redirect("/albums/list");
                return;
            }
        
            $filename = $fileVar['image']['name'];
            $fileTmpName = $fileVar['image']['tmp_name'];
            $fileSize = $fileVar['image']['size'];
            $fileType = $fileVar['image']['type'];
            $fileError = $fileVar['image']['error'];

            $filett=explode(".",$filename);
            $fileExt= strtolower(end($filett));
            $allowedExts = ['png','jpg','jpeg'];
            // echo $fileSize."<br>";
            // echo $fileError."<br>";
            // echo $fileExt."<br>";

            if(!in_array($fileExt,$allowedExts) || $fileError !== 0 ){
                echo "error";
                return;
            }
            $album['title']=$title;
            $album['genre']=$genre;
            $album['artworkPath']="img/albums/".time().$filename;
            $album['artist']=$_SESSION['userId'];
            $done = $this->addAlbumToDb($album);
            echo "done";
            move_uploaded_file($fileTmpName,$album['artworkPath']);
            header("Location: /");    
    } 
    public function deleteAlbum($id){
        $this->db->query("DELETE FROM albums WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();
        $this->db->query("DELETE FROM songs WHERE album = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();
        header("Location: /albums/list");
    }
}
