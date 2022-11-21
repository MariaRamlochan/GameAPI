<?php

$artist_model->updateArtists($existing_artist_record, array("ArtistId"=>$artistId));

$artistId = $single_artist["ArtistId"];
$artistName = $single_artist["Name"];

//-- We retrieve the key and its value
//-- We perform an UPDATE/CREATE SQL statement

$existing_artist_record = array(
	"Name"=>$artistName
);


update game set 
game_tile = 'new_value', game_url = 'nafees.come'
where 
game_id = 1;
