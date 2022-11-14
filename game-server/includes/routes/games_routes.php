<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/GameModel.php';

// Callback for HTTP GET /games
//-- Supported filtering operation: by game title.
function handleGetAllGames(Request $request, Response $response, array $args) {
    $games = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $game_model = new GameModel();

    // Retreive the query string parameter from the request's URI.
    $filter_params = $request->getQueryParams();
    if (isset($filter_params["title"])) {
        // Fetch the list of games matching the provided title.
        $games = $game_model->getWhereLike($filter_params["title"]);
    } else {
        // No filtering by game title detected.
        $games = $game_model->getAll();
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

function handleGetGamesByDeveloperId(Request $request, Response $response, array $args) {
    $game_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $game_model = new GameModel();

    // Retreive the game if from the request's URI.
    $developer_id = $args["developer_id"];
    if (isset($developer_id)) {
        // Fetch the info about the specified game.
        $game_info = $game_model->getGamesByDeveloperId($developer_id);
        if (!$game_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified album.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
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

function handleGetGamesByPublisherId(Request $request, Response $response, array $args) {
    $game_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $game_model = new GameModel();

    // Retreive the game if from the request's URI.
    $publisher_id = $args["publisher_id"];
    if (isset($publisher_id)) {
        // Fetch the info about the specified game.
        $game_info = $game_model->getGamesByDeveloperId($publisher_id);
        if (!$game_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified album.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
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

function handleGetGamesByGenreId(Request $request, Response $response, array $args) {
    $game_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $game_model = new GameModel();

    // Retreive the game if from the request's URI.
    $genre_id = $args["genre_id"];
    if (isset($genre_id)) {
        // Fetch the info about the specified game.
        $game_info = $game_model->getGamesByDeveloperId($genre_id);
        if (!$game_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified album.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
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
    $game_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $game_model = new GameModel();
    $data = $request->getParsedBody();

    // Fetch the info about the specified game.
    for ($index = 0; $index < count($data); $index++){
        $single_game = $data[$index];
        $gameId = $single_game["game_id"];
        $gameTitle = $single_game["title"];
        $gameThumbnail = $single_game["thumbnail"];
        $gameStatus = $single_game["status"];
        $gameShortDescription = $single_game["short_description"];
        $gameDescription = $single_game["description"];
        $gameURL = $single_game["game_url"];
        $gameReleaseDate = $single_game["release_date"];
        $genreId = $single_game["genre_id"];
        $platformId = $single_game["platform_id"];
        $publisherId = $single_game["publisher_id"];
        $developerId = $single_game["developer_id"];
        $requirementId = $single_game["requirement_id"];
        $reviewId = $single_game["review_id"];


        $new_game_record = array(
            "game_id"=>$gameId,
            "title"=>$gameTitle,
            "thumbnail"=>$gameThumbnail,
            "status"=>$gameStatus,
            "short_description"=>$gameShortDescription,
            "description"=>$gameDescription,
            "game_url"=>$gameURL,
            "release_date"=>$gameReleaseDate,
            "genre_id"=>$genreId,
            "platform_id"=>$platformId,
            "publisher_id"=>$publisherId,
            "developer_id"=>$developerId,
            "requirement_id"=>$requirementId,
            "review_id"=>$reviewId
        );

        $game_info = $game_model->createArtists($new_game_record);
    }

    $html = var_export($data, true);
    $response->getBody()->write($html);
    return $response->withStatus($response_code);
}

function handleUpdateGames(Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    //-- Go over elements stored in the $data array
    //-- In a for/each loop
    $game_model = new GameModel(); 

    for ($index = 0; $index < count($data); $index++){
        $single_game = $data[$index];
        $gameId = $single_game["game_id"];
        $gameTitle = $single_game["title"];
        $gameThumbnail = $single_game["thumbnail"];
        $gameStatus = $single_game["status"];
        $gameShortDescription = $single_game["short_description"];
        $gameDescription = $single_game["description"];
        $gameURL = $single_game["game_url"];
        $gameReleaseDate = $single_game["release_date"];
        $genreId = $single_game["genre_id"];
        $platformId = $single_game["platform_id"];
        $publisherId = $single_game["publisher_id"];
        $developerId = $single_game["developer_id"];
        $requirementId = $single_game["requirement_id"];
        $reviewId = $single_game["review_id"];

        //-- We retrieve the key and its value
        //-- We perform an UPDATE/CREATE SQL statement

        $existing_game_record = array(
            "title"=>$gameTitle,
            "thumbnail"=>$gameThumbnail,
            "status"=>$gameStatus,
            "short_description"=>$gameShortDescription,
            "description"=>$gameDescription,
            "game_url"=>$gameURL,
            "release_date"=>$gameReleaseDate,
            "genre_id"=>$genreId,
            "platform_id"=>$platformId,
            "publisher_id"=>$publisherId,
            "developer_id"=>$developerId,
            "requirement_id"=>$requirementId,
            "review_id"=>$reviewId
        );

        $game_model->updateArtists($existing_game_record, array("game_id"=>$gameId));
    }

    $html = var_export($data, true);
    $response->getBody()->write($html);
    return $response;
}

function handleDeleteGames(Request $request, Response $response, array $args) {
    $game_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $game_model = new GameModel();
    $data = $request->getParsedBody();

    // Retreive the artist from the request's URI.
    $game_id = $args["game_id"];
    if (isset($game_id)) {
        // Fetch the info about the specified game.
        $game_model->deleteArtists(array("game_id"=>$game_id));
        $game_info = "Game has been DELETED";
        if (!$game_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified artist.");
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