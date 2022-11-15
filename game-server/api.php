<?php

require __DIR__ . '/vendor/autoload.php';

require_once __DIR__ .'/includes/app_constants.php';
require_once __DIR__ .'/includes/models/BaseModel.php';
require_once __DIR__ .'/includes/models/GameModel.php';

use GuzzleHttp\Psr7\Request;

$client = new GuzzleHttp\Client(['base_uri' => 'https://www.freetogame.com/api/']);
$request = $client->request('GET', 'games', 
        [
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]
);

$data = $request->getBody()->getContents();
$games = json_decode($data, true);

$gameModel = new GameModel();

//var_dump($games);exit;

foreach ($games as $key => $game) {
    # code...
    echo "Inserting.... ".$game["title"]. "<br>";
    // Import to the DB. 
     $new_game = Array(
        "title" => $game["title"],
        "thumbnail" => $game["thumbnail"],
        "short_description" => $game["short_description"],
        "game_url" => $game["game_url"],
        "genre" => $game["genre"],
        "platform" => $game["platform"],
        "publisher" => $game["publisher"],
        "release_date" => $game["release_date"],
        "developer" => $game["developer"]

     ) ;   
    // createGames
    $gameModel->createGames($new_game);
}