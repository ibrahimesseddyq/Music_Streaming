<?php

namespace App\Controllers;
use \Core\View;
use \App\Models\Album;
use \App\Models\Artist;
use \App\Models\Song;
Use \App\Query;

class Artists extends \Core\Controller
{

    public function showAction()
    {
        $artist = new Artist($this->route_params['id']);
        $query = new Query;

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $url = 'Artists/mainContentArtists.html';
        } else {
            $url = '/Artists/index.html';
        }
        
        View::renderTemplate($url, [
    
            'artist' => $artist->getName(),
            'ids' => $query->getRandomSongsIds(),
            'songs' => $artist->getSongsInfo(),
            'idsAlbum' => $artist->getArtistSongsIds(),
            'albums' => $query->getArtistAlbums($this->route_params['id'])
        
        ]);
    }

}