<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/GameModel.php';
require_once __DIR__ . './../helpers/Paginator.php';
require_once __DIR__ . './../helpers/WebServiceInvoker.php';


// Callback for HTTP GET /games
//-- Supported filtering operation: by game title, genre, platform.
function handleGetAllGames(Request $request, Response $response, array $args) {
    $input_page_number = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
    $input_per_page = filter_input(INPUT_GET, "per_page", FILTER_VALIDATE_INT);
    
    $games = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $game_model = new GameModel(); 

    $game_model->setPaginationOptions($input_page_number, $input_per_page);

    // Retreive the query string parameter from the request's URI.
    $filter_params = $request->getQueryParams();
    if (isset($filter_params["title"])) {
        // Fetch the list of games matching the provided title.
        $games = $game_model->getGameByTitle($filter_params["title"]);
    } else if (isset($filter_params["genre"])) {
        // Fetch the list of games matching the provided genre.
        $games = $game_model->getGamesByGenre($filter_params["genre"]);
    } else if (isset($filter_params["platform"])) {
        // Fetch the list of games matching the provided platform.
        $games = $game_model->getGamesByPlatform($filter_params["platform"]);
    } else if (isset($filter_params["publisher"])) {
        // Fetch the list of games matching the provided publisher.
        $games = $game_model->getGamesByPublisher($filter_params["publisher"]);
    } else if (isset($filter_params["developer"])) {
        // Fetch the list of games matching the provided developer.
        $games = $game_model->getGamesByDeveloper($filter_params["developer"]);
    } else {
        // No filtering by game title detected.
        $games = $game_model->getAll();
        //print_r(json_encode($games));

    }    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        // $response_data['games'] = $games;
        // $response_data['comments'] = getComments();
        //$response_data = json_encode($response_data, JSON_INVALID_UTF8_SUBSTITUTE);
        $response_data = json_encode($games, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetGameById(Request $request, Response $response, array $args) {
    $game_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $game_model = new GameModel();

    // Retreive the game if from the request's URI.
    $game_id = $args["game_id"];
    if (isset($game_id)) {
        // Fetch the info about the specified game.
        $game_info = $game_model->getGameById($game_id);
        if (!$game_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified game.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($game_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleCreateGames(Request $request, Response $response, array $args) {
    $response_code = HTTP_OK;
    $new_game_record = array();
    $game_model = new GameModel();
    $data = $request->getParsedBody();

    

    // Fetch the info about the specified game.
    foreach($data as $key => $single_game){
        if(isset($single_game["title"]) && isset($single_game["thumbnail"]) && isset($single_game["game_url"]) && isset($single_game["release_date"])
         && isset($single_game["genre"]) && isset($single_game["platform"])&& isset($single_game["developer"])
         && isset($single_game["short_description"]) && isset($single_game["game_url"])){

            $gameTitle = $single_game["title"];
            $gameThumbnail = $single_game["thumbnail"];
            $gameDescription = $single_game["short_description"];
            $gameURL = $single_game["game_url"];
            $gameReleaseDate = $single_game["release_date"];
            $genre = $single_game["genre"];
            $platform = $single_game["platform"];
            $publisher = $single_game["publisher"];
            $developer = $single_game["developer"];
    
            $new_game_record = array(
                "title"=>$gameTitle,
                "thumbnail"=>$gameThumbnail,
                "short_description"=>$gameDescription,
                "game_url"=>$gameURL,
                "release_date"=>$gameReleaseDate,
                "genre"=>$genre,
                "platform"=>$platform,
                "publisher"=>$publisher,
                "developer"=>$developer);
        }else{
            $response_data = makeCustomJSONError("UnsetParamaterException", "All paramaters must be set.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        } 
    }
        
    $game_model->createGames($new_game_record);
    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
        $response_data = makeCustomJSONError("Success", "Games has been Created!", $response_data);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleUpdateGames(Request $request, Response $response, array $args) {
    
    $data = $request->getParsedBody();
    $response_code = HTTP_OK;
    $game_model = new GameModel(); 
    
    //Create Empty array to insert what we would like to update    
    $existing_game = array();
   
    //Check which key we want to update
    foreach($data as $key => $single_game){

        

        //-- Check data set and retrieve the key and its value
        if(isset($single_game["game_id"])){
            //Retreive the Game Id for the specific game we want to update
            $existing_gameId = $single_game["game_id"];
            if($game_model->getGameById($existing_gameId) == null){
                $response_data = makeCustomJSONError("resourceNotFound", "no gameID found");
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
        }
        
        if(isset($single_game["title"])){
            $existing_game["title"] = $single_game["title"];
        }
        if(isset($single_game["thumbnail"])){
            $existing_game["thumbnail"] = $single_game["thumbnail"];
        }
        if(isset($single_game["game_url"])){
            $existing_game["game_url"] = $single_game["game_url"];
        }
        if(isset($single_game["release_date"])){
            $existing_game["release_date"] = $single_game["release_date"];
        }
        if(isset($single_game["genre"])){
            $existing_game["genre"] = $single_game["genre"];
        }
        if(isset($single_game["platform"])){
            $existing_game["platform"] = $single_game["platform"];
        }
        if(isset($single_game["developer"])){
            $existing_game["developer"] = $single_game["developer"];
        }
        if(isset($single_game["short_description"])){
            $existing_game["short_description"] = $single_game["short_description"];
        }
        if(isset($single_game["game_url"])){
            $existing_game["game_url"] = $single_game["game_url"];
        }

        //-- We perform an UPDATE SQL statement
         $game_model->updateGames($existing_game, array("game_id" => $existing_gameId));
    }
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
        $response_data = makeCustomJSONError("Success", "Games has been Updated", $response_data);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleDeleteGameById(Request $request, Response $response, array $args) {
    $game_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $game_model = new GameModel();

    // Retreive the gama from the request's URI.
    $game_id = $args["game_id"];
    if (isset($game_id)) {
        // Fetch the info about the specified game.
        $game_model->deleteGames(array("game_id"=>$game_id));
        $game_info = "Game has been DELETED";
        if (!$game_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified game.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    } 
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($game_info, JSON_INVALID_UTF8_SUBSTITUTE);
        $response_data = makeCustomJSONError("Success", "Game has been deleted", $response_data);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleDeleteGames(Request $request, Response $response, array $args) {
    $response_data = array();
    $response_code = HTTP_OK;
    $game_model = new GameModel();
    $data = $request->getParsedBody();
    $game_id = "";

    // Retreive the game from the request's URI.
    foreach($data as $key => $single_game){
        $game_id = $single_game["game_id"];
        if (isset($game_id)) {

            // Fetch the info about the specified game.
            $game_model->deleteGames(array("game_id"=>$game_id));
            if (!$data) {
                // No matches found?
                $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified game.");
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
        }
    }

    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
        $response_data = makeCustomJSONError("Success", "Games has been deleted", $response_data);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}