<?php

require __DIR__ . '/vendor/autoload.php';

require_once __DIR__ .'/includes/app_constants.php';
require_once __DIR__ .'/includes/models/BaseModel.php';
require_once __DIR__ .'/includes/models/GameModel.php';
require_once __DIR__ .'/includes/models/ReviewModel.php';
require_once __DIR__ .'/includes/models/AppModel.php';
use GuzzleHttp\Psr7\Request;

//Request data to our Free-to-play games API to populate the game table
$gameClient = new GuzzleHttp\Client(['base_uri' => 'https://www.freetogame.com/api/']);
$gameRequest = $gameClient->request('GET', 'games', 
        [
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]
);

$gameData = $gameRequest->getBody()->getContents();

$gameModel = new GameModel();
$games = json_decode($gameData, true);



//Go through response data to populate game table fields repectively 
foreach ($games as $key => $game) {
    $gameTitle = $game["title"];

   echo "Inserting.... ".$game["title"]. "<br>";

    // Import game into the game table of DB. 
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
     );   
    $gameModel->createGames($new_game);


}


$appModel = new AppModel();
// Load the game apps data from the .json file.
$appData = file_get_contents("response.json");
$apps = json_decode($appData, true);
$game_apps = $apps["data"];

//Go through response data to populate app table fields repectively 
foreach ($game_apps as $game_app) {

    
    //echo $game_app["app_name"];

    echo "Inserting.... ".$game_app["app_name"]. "<br>";

    // Import app into the app table of DB. 
     $new_app = Array(
         "app_name" => $game_app["app_name"],
         "app_category" => $game_app["app_category"],
         "app_developer" => $game_app["app_developer"],
         "num_downloads" => $game_app["num_downloads"],
         "app_description" => $game_app["app_description"],
         "app_url" => $game_app["app_page_link"],
         "app_icon" => $game_app["app_icon"]
     );   
   $appModel->createMobileGames($new_app);
}


