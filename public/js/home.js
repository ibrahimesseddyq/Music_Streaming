$(document).ready(function () {

    var newPlaylist = $('.info').data('ids');
    audioElement = new Audio();
    console.log(newPlaylist[0].id)
    setTrack(newPlaylist[0].id, newPlaylist, false);
    updateVolumeProgressBar(audioElement.audio);

    $("#nowPlayingBarContainer").on("mousedown touchstart mousemove touchmove", function (e) {
        e.preventDefault();
    })

    $(".playbackBar .progressBar").mousedown(function () {
        mouseDown = true;
    });

    $(".playbackBar .progressBar").mousemove(function (e) {
        if (mouseDown == true) {
            timeFromOffset(e, this);
        }
    });

    $(".playbackBar .progressBar").mouseup(function (e) {
        timeFromOffset(e, this);
    });

    $(".volumeBar .progressBar").mousedown(function () {
        mouseDown = true;
    });

    $(".volumeBar .progressBar").mousemove(function (e) {
        if (mouseDown == true) {
            var percentage = e.offsetX / $(this).width();

            if (percentage >= 0 && percentage <= 1) {
                audioElement.audio.volume = percentage;
            }

        }
    });

    $(".volumeBar .progressBar").mouseup(function (e) {
        var percentage = e.offsetX / $(this).width();

        if (percentage >= 0 && percentage <= 1) {
            audioElement.audio.volume = percentage;
        }
    });

    $(document).mouseup(function () {
        mouseDown = false;
    });

    $(document).on('click', '.logo img', function(e){
        e.preventDefault();
        $("#mainContent").empty().load("/home/index", function() {
            history.pushState(null, null, '/');
        });
        
    });

    $(document).on('click', '.navItemLink.search', function(e){
        e.preventDefault();
        $("#mainContent").load("/search/index", function() {
            history.pushState(null, null, '/search');
        });
        
    });


    $(".searchInput").focus();
    
    $(document).on('keyup', ".searchInput", function(e) {
        setTimeout(function(){
            search(e)}, 2000);
    });  

});

function artistProfile(id) {
    $("#mainContent").load('/artists/show/' + id, function () {
        history.pushState(null, null, '/artists/show/' + id);
    });

}

function search(e) {
    e.preventDefault();
    var val = $('.searchInput').val();
    if (val) {
        $.post('/search/execute', {
            search: val
        }, function (data) {
            $('.searchResultsContainer').html(data);
        });
    } else {
        return;
    }
};


function timeFromOffset(mouse, progressBar) {
    var percentage, seconds;
    percentage = mouse.offsetX / $(progressBar).width() * 100;
    seconds = audioElement.audio.duration * (percentage / 100);
    audioElement.setTime(seconds);
}

function prevSong() {
    if (audioElement.audio.currentTime >= 3 || currentIndex == 0) {
        audioElement.setTime(0);
    } else {
        currentIndex = currentIndex - 1;
        setTrack(currentPlaylist[currentIndex].id, currentPlaylist, true);
    }
}

function nextSong() {
    
    if (repeat == true) {
        audioElement.setTime(0);
        playSong();
        return;
    }
    console.log("NEXT", currentIndex);

    if(currentIndex == currentPlaylist.length - 1) {
        currentIndex = 0
    } else {
        currentIndex++;
    }

    var trackToPlay = shuffle ? shufflePlaylist[currentIndex].id : currentPlaylist[currentIndex].id;
    setTrack(trackToPlay, currentPlaylist, true);
}

function setRepeat() {
    repeat = !repeat;
    var imageName = repeat ? "repeat-active.png" : "repeat.png";
    $(".controlButton.repeat img").attr("src", "/img/icons/" + imageName);
}

function setMute() {
    audioElement.audio.muted = !audioElement.audio.muted;
    var imageName = audioElement.audio.muted ? "volume-mute.png" : "volume.png";
    $(".controlButton.volume img").attr("src", "/img/icons/" + imageName);
}

function loadAlbum (event, id){
    event.preventDefault();
    $("#mainContent").load('/albums/show/'+ id, function(){
        history.pushState(null, null, '/albums/show/'+ id);
    });  
};

Array.prototype.indexOfObject = function (object) {
    for (var i = 0; i < this.length; i++) {
        if (JSON.stringify(this[i]) === JSON.stringify(object))
            return i;
    }
}

function setShuffle() {
    shuffle = !shuffle;
    var imageName = shuffle ? "shuffle-active.png" : "shuffle.png";
    $(".controlButton.shuffle img").attr("src", "/img/icons/" + imageName);

    if(shuffle == true) {
        //Randomize playlist
        shuffleArray(shufflePlaylist);
        currentIndex = shufflePlaylist.indexOfObject({id: audioElement.currentlyPlaying.id});

    } else {
        currentIndex = currentPlaylist.indexOfObject({id: audioElement.currentlyPlaying.id});
    }
}

function shuffleArray(a) {
    var j, x, i;
    for (i = a.length - 1; i > 0; i--) {
        j = Math.floor(Math.random() * (i + 1));
        x = a[i];
        a[i] = a[j];
        a[j] = x;
    }
}

function setTrack(trackId, newPlaylist, play) {


    if(newPlaylist != currentPlaylist) {
        currentPlaylist = newPlaylist;
        shufflePlaylist = currentPlaylist.slice();
        shuffleArray(shufflePlaylist);
    }

    if(shuffle == true) {
        currentIndex = shufflePlaylist.indexOfObject({
            id: (trackId).toString()
        });
    } else {
        currentIndex = currentPlaylist.indexOfObject({
            id: (trackId).toString()
        });
    }
    // console.log(currentPlaylist, currentIndex, trackId.toString());

    pauseSong();

    $.post('/ajax/findSong', {
        songId: trackId
    }, function (data) {

        var track = JSON.parse(data);
        $('.trackName span').text(track.title);

        $.post('/ajax/findArtist', {
            artistId: track.artist
        }, function (data) {


            var artist = JSON.parse(data);
            $('.trackInfo .artistName span').text(artist.name);
            $(document).off('click', '.trackInfo .artistName span').on("click", '.trackInfo .artistName span', function(){
                $("#mainContent").load('/artists/show/'+ artist.id, function(){
                    history.pushState(null, null, '/artists/show/'+ artist.id);
                });
            });
        });

        $.post('/ajax/findAlbum', {
            albumId: track.album
        }, function (data) {

            var album = JSON.parse(data);
            $('.albumLink img').attr("src", album.artworkPath);
        });

        audioElement.setTrack(track);

        if (play) {
            playSong();
        } 

    });  

}

function playSong() {

    if (audioElement.audio.currentTime == 0) {
        $.post('/ajax/updateCount', {
            songId: audioElement.currentlyPlaying.id
        });
    }

    $('.controlButton.play').hide();
    $('.controlButton.pause').show();
    audioElement.play();
}

function pauseSong() {
    $('.controlButton.pause').hide();
    $('.controlButton.play').show();
    audioElement.pause();
}