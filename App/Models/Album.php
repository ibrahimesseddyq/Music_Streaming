<?php

namespace App\Models;

use PDO;
use \App\Constants;
use Core\View;
use Core\DB;
use \App\Models\Artist;
use \App\Query;

class Album extends \Core\Model
{
    private $id;
    private $title;
    private $artistId;
    private $genre;
    private $artworkPath;

    public function __construct($id)
    {
        parent::__construct();

        $this->id = $id;

        $this->db->query('SELECT * FROM albums WHERE id = :id');
        $this->db->bind(':id', $this->id);
        $album = $this->db->single();

        $this->title = $album->title;
        $this->artistId = $album->artist;
        $this->genre = $album->genre;
        $this->artworkPath = $album->artworkPath;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getArtworkPath() 
    {
        return $this->artworkPath;
    }

    public function getArtist() 
    { 
        return new Artist($this->artistId);
    }

    public function getSongsInfo()
    {
        $query = new Query;
        $data = $query->getAlbumSongs($this->id);

        $array = array();
        foreach($data as $x => $obj) {
            $song = new Song($obj->id);
            $obj->name = $song->getTitle();
            $obj->artist = $song->getArtist()->getName();
            $obj->duration = $song->getDuration();
            $obj->key = $x+1;
            array_push($array, $obj);
        }
        return $array;

    }

    public function getAlbumSongsIds()
    {
        $query = new Query;
        return json_encode($query->getAlbumSongs($this->id));
    }

}
