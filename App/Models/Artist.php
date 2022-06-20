<?php

namespace App\Models;

use PDO;
use \App\Constants;
use Core\View;
use Core\DB;
use \App\Query;


class Artist extends \Core\Model
{
    private $id;
    private $name;
    
    public function __construct($id)
    {
        parent::__construct();
    
        $this->id = $id;
    
        $this->db->query('SELECT * FROM artists WHERE id = :id');
        $this->db->bind(':id', $this->id);
        $artist = $this->db->single();
    
        $this->name = $artist->name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSongsInfo()
    {
        $query = new Query;
        $data = $query->getArtistSongs($this->id);

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

    public function getArtistSongsIds()
    {
        $query = new Query;
        return json_encode($query->getArtistSongs($this->id));
    }
}
