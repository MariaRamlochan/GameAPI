<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/StreamerModel.php';

// Callback for HTTP GET /streamers
//-- Supported filtering operation: by streamer name.
function handleGetAllStreamers(Request $request, Response $response, array $args) {
    $streamers = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $streamer_model = new StreamerModel();

    // Retreive the query string parameter from the request's URI.
    $filter_params = $request->getQueryParams();
    if (isset($filter_params["name"])) {
        // Fetch the list of streamers matching the provided name.
        $streamers = $streamer_model->getStreamerByName($filter_params["name"]);
    } else {
        // No filtering by streamer name detected.
        $streamers = $streamer_model->getAll();
    }    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($streamers, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetStreamerById(Request $request, Response $response, array $args) {
    $streamer_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $streamer_model = new StreamerModel();

    // Retreive the streamer if from the request's URI.
    $streamer_id = $args["streamer_id"];
    if (isset($streamer_id)) {
        // Fetch the info about the specified streamer.
        $streamer_info = $streamer_model->getStreamerById($streamer_id);
        if (!$streamer_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified streamer.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($streamer_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleCreateStreamers(Request $request, Response $response, array $args) {
    $response_data = array();
    $response_code = HTTP_OK;
    $streamer_model = new StreamerModel();
    $data = $request->getParsedBody();


    foreach($data as $key => $single_streamer){
    // Fetch the info about the specified streamer.
        if(isset($single_streamer["streamer_name"]) && isset($single_streamer["streamer_url"])){

            $name = $single_streamer["streamer_name"];
            $url = $single_streamer["streamer_url"];
            
            $new_streamer_record = array(
                "streamer_name"=>$name,
                "streamer_url"=>$url,
            );

        }else{
            $response_data = makeCustomJSONError("UnsetParamaterException", "All paramaters must be set.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    $streamer_model->createStreamers($new_streamer_record);
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
        $response_data = makeCustomJSONError("Success", "Streamer has been Created!", $response_data);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleUpdateStreamers(Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $response_code = HTTP_OK;
    $streamer_model = new StreamerModel(); 

     //Create Empty array to insert what we would like to update    
     $existing_streamer = array();

    foreach($data as $key => $single_streamer){

        //-- Check data set and retrieve the key and its value
        if(isset($single_streamer["streamer_id"])){
            //Retreive the auhtor Id for the specific game we want to update
            $existing_streamer_id = $single_streamer["streamer_id"];
            if($streamer_model->getStreamerById($existing_streamer_id) == null){
                $response_data = makeCustomJSONError("resourceNotFound", "no streamer ID found");
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
        }

        //-- We retrieve the key and its value
        if(isset($single_streamer["streamer_name"])){
            $existing_streamer["streamer_name"] = $single_streamer["streamer_name"];
        }
        if(isset($single_streamer["streamer_url"])){
            $existing_streamer["streamer_url"] = $single_streamer["streamer_url"];
        }
        //-- We perform an UPDATE SQL statement
        $streamer_model->updateStreamers($existing_streamer, array("streamer_id" => $existing_streamer_id));
    }

    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
        $response_data = makeCustomJSONError("Success", "Streamer has been Updated", $response_data);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleDeleteStreamers(Request $request, Response $response, array $args) {
    $response_data = array();
    $response_code = HTTP_OK;
    $streamer_model = new StreamerModel();
    $data = $request->getParsedBody();
    $streamer_id = "";

    // Retreive the game from the request's URI.
    foreach($data as $key => $single_streamer){
        $streamer_id = $single_streamer["streamer_id"];
        if (isset($streamer_id)) {

            // Fetch the info about the specified game.
            $streamer_model->deleteStreamers(array("streamer_id"=>$streamer_id));
            if (!$data) {
                // No matches found?
                $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified streamer.");
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
        $response_data = makeCustomJSONError("Success", "Authors has been deleted", $response_data);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleDeleteStreamer(Request $request, Response $response, array $args) {
    $streamer_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $streamer_model = new StreamerModel();

    // Retreive the artist from the request's URI.
    $streamer_id = $args["streamer_id"];
    if (isset($streamer_id)) {
        // Fetch the info about the specified streamer.
        $streamer_model->deleteStreamers(array("streamer_id"=>$streamer_id));
        $streamer_info = "Streamer has been DELETED";
        if (!$streamer_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified streamer.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    } 
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($streamer_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}