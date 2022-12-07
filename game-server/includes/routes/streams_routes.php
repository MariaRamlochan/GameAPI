<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/StreamModel.php';
require_once __DIR__ . './../models/StreamerModel.php';
require_once __DIR__ . './../models/GameModel.php';

// Callback for HTTP GET /streams
//-- Supported filtering operation: by stream title.
function handleGetAllStreams(Request $request, Response $response, array $args) {
    $input_page_number = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
    $input_per_page = filter_input(INPUT_GET, "per_page", FILTER_VALIDATE_INT);

    $streams = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $stream_model = new StreamModel();

    if (isset($input_page_number) && isset($input_per_page)){
        $stream_model->setPaginationOptions($input_page_number, $input_per_page);
    } else {
        $stream_model->setPaginationOptions(1, 1000);
    }

    // Retreive the query string parameter from the request's URI.
    $filter_params = $request->getQueryParams();
    if (isset($filter_params["title"])) {
        // Fetch the list of streams matching the provided title.
        $streams = $stream_model->getStreamByTitle($filter_params["title"]);
    } else {
        // No filtering by stream title detected.
        $streams = $stream_model->getAll();
    }    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($streams, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetStreamById(Request $request, Response $response, array $args) {
    $stream_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $stream_model = new StreamModel();

    // Retreive the stream if from the request's URI.
    $stream_id = $args["stream_id"];
    if (isset($stream_id)) {
        // Fetch the info about the specified stream.
        $stream_info = $stream_model->getStreamById($stream_id);
        if (!$stream_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified stream.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($stream_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleCreateStreams(Request $request, Response $response, array $args) {
    $response_data = array();
    $response_code = HTTP_OK;
    $stream_model = new StreamModel();
    $streamer_model = new StreamerModel();
    $game_model = new GameModel();
    $data = $request->getParsedBody();


    foreach($data as $key => $single_stream){
    // Fetch the info about the specified stream.
        if(isset($single_stream["title"]) && isset($single_stream["streamer_id"]) 
        && isset($single_stream["game_id"])){

            $title = $single_stream["title"];
            $streamerId = $single_stream["streamer_id"];
            $gameId = $single_stream["game_id"];
            

            if($game_model->getGameById($gameId)){
                if($streamer_model->getStreamerById($streamerId)){

                    $new_stream_record = array(
                        "streamer_id"=>$streamerId,
                        "game_id"=>$gameId,
                        "title"=>$title
                    );

                }else {
                    $response_data = makeCustomJSONError("UnsetParamaterException", "Invalid streamer id.");
                    $response->getBody()->write($response_data);
                    return $response->withStatus(HTTP_NOT_FOUND);
                }
            }else{
                $response_data = makeCustomJSONError("UnsetParamaterException", "Invalid game id.");
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }

            

        }else{
            $response_data = makeCustomJSONError("UnsetParamaterException", "All paramaters must be set.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
        
        $stream_model->createStreams($new_stream_record);
    }
    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
        $response_data = makeCustomJSONError("Success", "Stream has been Created!", $response_data);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleUpdateStreams(Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $response_code = HTTP_OK;
    $stream_model = new StreamModel(); 
    $streamer_model = new StreamerModel();
    $game_model = new GameModel();

     //Create Empty array to insert what we would like to update    
     $existing_stream = array();

    foreach($data as $key => $single_stream){

        //-- Check data set and retrieve the key and its value
        if(isset($single_stream["stream_id"])){
            //Retreive the stream Id for the specific stream we want to update
            $existing_stream_id = $single_stream["stream_id"];
            if($stream_model->getStreamById($existing_stream_id) == null){
                $response_data = makeCustomJSONError("resourceNotFound", "no stream ID found");
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
        }

        $streamerId = $single_stream["streamer_id"];
        $gameId = $single_stream["game_id"];

        //-- We retrieve the key and its value
        if(isset($single_stream["title"])){
            $existing_stream["title"] = $single_stream["title"];
        }

        if(isset($streamerId)){
            if($streamer_model->getStreamerById($streamerId)){
                $existing_stream["streamer_id"] = $single_stream["streamer_id"];
            }else{
                $response_data = makeCustomJSONError("UnsetParamaterException", "Invalid streamer id.");
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
        }

        if(isset($gameId)){
            if($game_model->getGameById($gameId)){
                $existing_stream["game_id"] = $single_stream["game_id"];
            }else{
                $response_data = makeCustomJSONError("UnsetParamaterException", "Invalid game id.");
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
        }
        //-- We perform an UPDATE SQL statement
        $stream_model->updateStreams($existing_stream, array("stream_id" => $existing_stream_id));
    }

    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
        $response_data = makeCustomJSONError("Success", "Stream has been Updated", $response_data);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleDeleteStreams(Request $request, Response $response, array $args) {
    $response_data = array();
    $response_code = HTTP_OK;
    $stream_model = new StreamModel();
    $data = $request->getParsedBody();
    $stream_id = "";

    // Retreive the stream from the request's URI.
    foreach($data as $key => $single_stream){
        $stream_id = $single_stream["stream_id"];
        if (isset($stream_id)) {

            // Fetch the info about the specified stream.
            $stream_model->deleteStreams(array("stream_id"=>$stream_id));
            if (!$data) {
                // No matches found?
                $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified stream.");
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
        $response_data = makeCustomJSONError("Success", "Streams has been deleted", $response_data);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleDeleteStream(Request $request, Response $response, array $args) {
    $stream_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $stream_model = new StreamModel();

    // Retreive the artist from the request's URI.
    $stream_id = $args["stream_id"];
    if (isset($stream_id)) {
        // Fetch the info about the specified stream.
        $stream_model->deleteStreams(array("stream_id"=>$stream_id));
        $stream_info = "Stream has been DELETED";
        if (!$stream_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified stream.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    } 
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($stream_info, JSON_INVALID_UTF8_SUBSTITUTE);
        $response_data = makeCustomJSONError("Success", "Stream has been deleted", $response_data);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}