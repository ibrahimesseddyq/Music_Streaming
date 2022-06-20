var currentPlaylist = [],
    shufflePlaylist = [],
    tempPlaylist = [],
    audioElement,
    mouseDown = false,
    currentIndex = 0,
    repeat = false,
    shuffle = false;


function formatTime(seconds) {
    var time, minutes, seconds, extraZero;
        time = Math.round(seconds);
        minutes = Math.floor(time/60);
        seconds = time - (minutes*60);

        extraZero = (seconds < 10) ? "0" : "";
 
        return minutes + ":" + extraZero + seconds;
}

function updateTimeProgressBar(audio) {
    $(".progressTime.current").text(formatTime(audio.currentTime));
    $(".progressTime.remaining").text(formatTime(audio.duration - audio.currentTime));

    var progress = audio.currentTime / audio.duration * 100;
    $(".playbackBar .progress").css("width", progress + "%");
}

function updateVolumeProgressBar(audio) {
    var volume = audio.volume * 100;
    $(".volumeBar .progress").css("width", volume + "%");
}

function playFirstSong() {
    setTrack(tempPlaylist[0].id, tempPlaylist, true);
}


function Audio() {
    this.currentlyPlaying;
    this.audio = document.createElement('audio');

    this.audio.addEventListener("ended", function(){
        nextSong();
    });

    this.audio.addEventListener("canplay", function(){
        $(".progressTime.remaining").text(formatTime(this.duration));
    });

    this.audio.addEventListener("timeupdate", function(){
        if(this.duration) {
            updateTimeProgressBar(this);
        }
    });

    this.audio.addEventListener("volumechange", function() {
        updateVolumeProgressBar(this);
    })

    this.setTrack = function(track) {
        this.currentlyPlaying = track;
        this.audio.src = track.path;
        console.log(this.audio.src)
        console.log(this.currentlyPlaying)

    }

    this.play = function() {
        this.audio.play();
    }

    this.pause = function() {
        this.audio.pause();
    }

    this.setTime = function(seconds) {
        this.audio.currentTime = seconds;
    }
}
