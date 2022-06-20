<?php

namespace App\Models;

use PDO;
use \App\Constants;
use Core\View;
use Core\DB;

class Song extends \Core\Model
{
    private $id;
    private $title;
    private $artistId;
    private $albumId;
    private $genre;
    private $duration;
    private $path;

    public function __construct($id)
    {
        parent::__construct();

        $this->id = $id;

        $this->db->query('SELECT * FROM songs WHERE id = :id');
        $this->db->bind(':id', $this->id);
        $song = $this->db->single();

        $this->title = $song->title;
        $this->artistId = $song->artist;
        $this->albumId = $song->album;
        $this->genre = $song->genre;
        $this->duration = $song->duration;
        $this->path = $song->path;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getArtist() {
        return new Artist($this->artistId);
    }

    public function getDuration() {
        return $this->duration;
    }


}