<?php
include('includes/includedFiles.php');
?>

<script>
    //openPage('browse.php');
</script>
<h1 style="text-align: center; font-size: 60px;">Popular Right Now</h1>
<!--<h1 style="text-align: center; font-size: 60px;">Made For --><?php //echo $userLoggedIn->getFirstName();?><!--</h1>-->
<div class="toggleContainer">
    <h2 class="popularStringArtist toggleItem"> <span id="togAlbum" onclick="openPage('index.php?section=album')">Albums </span></h2>
    <h2 class="popularStringArtist toggleItem"> <span id="togSong" onclick="openPage('index.php?section=songs')">Songs </span></h2>
    <h2 class="popularStringArtist toggleItem"> <span id="togArtist" onclick="openPage('index.php?section=artist')">Artist </span></h2>
</div>

<div class="gridViewContainerArtistPage" id="albumSection">

    <?php
    $albumQuery = mysqli_query($con,"SELECT * FROM albums ORDER BY RAND() LIMIT 100");

    if (mysqli_num_rows($albumQuery) < 1){
        echo "no album matches ".$term;
    }

    while ($row = mysqli_fetch_array($albumQuery)){
        echo "<div class='gridViewItemArtistPage'>
                    <span onclick='openPage(\"album.php?id=".$row['id']."\")'>
                       <div class='albumGridItem' style='background-image: url(\"/hashify".$row['artworkPath']."\") , url(\"/hashify/assests/Images/album.png\")'></div>
                        <div class='gridViewInfo'>
                         ".$row['title']."
                    </div>
                    </span>
                    </div>";
    }
    ?>
</div>

<div class="trackListContainer borderBottom" id="songSection">

    <ul class="trackList">
        <?php
        $songQuery = mysqli_query($con, "SELECT id FROM songs ORDER BY RAND() LIMIT 100  ");
        if (mysqli_num_rows($songQuery) == 0){
            echo "<span class='noResult'>No Songs Matches ".$term."</span>";
        }
        $songIdArray = array();
        $i = 1;

        while ($row = mysqli_fetch_array($songQuery)){
            if ($i > 30){
                break;
            }

            array_push($songIdArray,$row['id']);
            $albumSong = new Songs($con,$row['id']);
            $albumArtist = $albumSong->getArtist();
            $currentSongAlbum = $albumSong->getAlbum();


            echo "
                                <li class='trackListRow' oncontextmenu='showOptionsMenuCM(event,this)'>
                                <input type='hidden' class='songIdCM' value='".$albumSong->getId()."'>                                <div class='trackCount'>
                                <img onclick='manualPlaylist=false;setTrack(\"".$albumSong->getId()."\",tempPlaylist,true)' src='assests/Images/icons/play-white.png' class='play' alt=''>
                                <span class='trackNumber'>$i</span>
                                </div>
                                 <div class='trackInfo' >
                                <span class='trackName ' onclick='manualPlaylist=false;setTrack(\"".$albumSong->getId()."\",tempPlaylist,true)'>".$albumSong->getTitle()."</span><br>
                                 <span role=\"link\" onclick=\"openPage('artist.php?id=".$albumArtist->getId()."')\"><span class='artistName'>".$albumArtist->getName()."</span></span>
                                  <span class='artistAlbumNameSeperator'>•</span>
                                <span role=\"link\" onclick=\"openPage('album.php?id=".$currentSongAlbum->getAlbumId()."')\"> <span class='albumName'> ".$currentSongAlbum->getTitle()."</span></span>
                                </div>
                                <div class='trackOptions'>
                                <input type='hidden' class='songId' value='".$albumSong->getId()."'>
                                    <img src='assests/Images/icons/more.png' onclick='showOptionsMenu(this)'  class='optionButton' alt=''>
                                </div>
                                <div class='trackDuration'>
                                <span class='duration'>".$albumSong->getDuration()."</span>
</div>
                            </li>
                        ";
            $i++;
        }
        ?>
        <script>
            tempSongId = '<?php echo json_encode($songIdArray)?>';
            tempPlaylist  = JSON.parse(tempSongId);
           // console.log(tempPlaylist+"  "+tempSongId);
        </script>
    </ul>
</div>
<nav class="optionMenu">
    <input type="hidden" class="songId">
    <?php echo Playlist::getDropdownPlaylist($con,$userLoggedIn->getUserName())?>
    <div class="item" id="songPlayNext" onclick="playNext(this)">Play Next</div>
    <div class="item" id="addSongToQueue" onclick="addSongToQueue()">Add To Queue</div>


</nav>


<div class="artistContainer" id="artistSection">
    <?php
    $artistQuery = mysqli_query($con,"SELECT id FROM artist ORDER BY RAND() LIMIT 100 ");

    if (mysqli_num_rows($artistQuery) == 0){
        echo "<span class='noResult'>No Artist Matches ".$term."</span>";
    }
    while($row = mysqli_fetch_array($artistQuery)){
        $artistFound = new Artist($con,$row['id']);
        echo "<div class='searchResultRow'>
                <div class='artistName'>
                    <span role='link' onclick='openPage(\"artist.php?id=".$artistFound->getId()."\")'> 
                    ". $artistFound->getName()."
                     </span>
                </div>
                       </div>";
    }
    ?>

</div>




<script>
//    toggleAlbum();

    <?php
    if(isset($_GET['section'])){
        if ($_GET['section'] == 'album') {
            echo "toggleAlbum()";
        }
        elseif($_GET['section'] == 'artist') {
            echo "toggleArtist()";
        }
        elseif($_GET['section'] == 'songs') {
            echo "toggleSong()";
        }else{
            echo "toggleAlbum()";
        }
    }else{
        echo "toggleAlbum()";
    }


    ?>


    function toggleSong() {
        var songSectionSp = document.getElementById('songSection');
        var artistSectionSp = document.getElementById('artistSection');
        var albumSectionSp = document.getElementById('albumSection');
        // console.log(songSectionSp,artistSectionSp,albumSectionSp);

        let albumButton = document.getElementById('togAlbum');
        let songButton = document.getElementById('togSong');
        let artistButton = document.getElementById('togArtist');
        // console.log(songButton);
        songButton.style.color = "#08d85c";
        albumButton.style.color = "#fff";
        artistButton.style.color = "#fff";
        songSectionSp.style.display = 'block';
        artistSectionSp.style.display = 'none';
        albumSectionSp.style.display = 'none';
    }
    function toggleArtist() {
        var songSectionSp = document.getElementById('songSection');
        var artistSectionSp = document.getElementById('artistSection');
        var albumSectionSp = document.getElementById('albumSection');

        // console.log(songSectionSp,artistSectionSp,albumSectionSp);

        let albumButton =document.getElementById('togAlbum');
        let artistButton =document.getElementById('togArtist');
        let songButton = document.getElementById('togSong');
        artistButton.style.color = "#08d85c";
        songButton.style.color = "#fff";
        albumButton.style.color = "#fff";


        songSectionSp.style.display = 'none';
        artistSectionSp.style.display = 'grid';
        albumSectionSp.style.display = 'none';
    }
    function toggleAlbum() {
        var songSectionSp = document.getElementById('songSection');
        var artistSectionSp = document.getElementById('artistSection');
        var albumSectionSp = document.getElementById('albumSection');

        // console.log(songSectionSp,artistSectionSp,albumSectionSp);

        let albumButton =document.getElementById('togAlbum');
        let artistButton =document.getElementById('togArtist');
        let songButton = document.getElementById('togSong');
        albumButton.style.color = "#08d85c";
        songButton.style.color = "#fff";
        artistButton.style.color = "#fff";

        songSectionSp.style.display = 'none';
        artistSectionSp.style.display = 'none';
        albumSectionSp.style.display = 'block';
    }
</script>

