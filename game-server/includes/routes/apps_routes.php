<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/AppModel.php';

// Callback for HTTP GET /apps
//-- Supported filtering operation: by app name.
function handleGetAllApps(Request $request, Response $response, array $args) {
    $apps = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $app_model = new AppModel();

    // Retreive the query string parameter from the request's URI.
    $filter_params = $request->getQueryParams();
    if (isset($filter_params["name"])) {
        // Fetch the list of apps matching the provided name.
        $apps = $app_model->getAppGameByName($filter_params["name"]);
    } else if (isset($filter_params["category"])) {
        // Fetch the list of apps matching the provided category.
        $apps = $app_model->getAppGamesByCategory($filter_params["category"]);
    } else if (isset($filter_params["downloads"])) {
        // Fetch the list of apps matching the provided downloads.
        $apps = $app_model->getAppGamesByNumberOfDownloads($filter_params["downloads"]);
    } else if (isset($filter_params["developer"])) {
        // Fetch the list of apps matching the provided developer.
        $apps = $app_model->getAppGamesByDeveloper($filter_params["developer"]);
    } else {
        // No filtering by app name detected.
        $apps = $app_model->getAll();
    }    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($apps, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetAppById(Request $request, Response $response, array $args) {
    $app_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $app_model = new AppModel();

    // Retreive the app if from the request's URI.
    $app_id = $args["app_id"];
    if (isset($app_id)) {
        // Fetch the info about the specified app.
        $app_info = $app_model->getAppGameById($app_id);
        if (!$app_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified app.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($app_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleCreateApps(Request $request, Response $response, array $args) {
    $app_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $app_model = new AppModel();
    $data = $request->getParsedBody();

    // Fetch the info about the specified app.
    for ($index = 0; $index < count($data); $index++){
        $single_app = $data[$index];
        //$appId = $single_app["app_id"];
        $appName = $single_app["app_name"];
        $appCategory = $single_app["app_category"];
        $appDeveloper = $single_app["app_developer"];
        $numDownloads = $single_app["num_downloads"];
        $appDescription = $single_app["app_description"];
        $appURL = $single_app["app_url"];
        $appIcon = $single_app["app_icon"];

        $new_app_record = array(
           //"app_id"=>$reviewId,
            "app_name"=>$appName,
            "app_category"=>$appCategory,
            "app_developer"=>$appDeveloper,
            "num_downloads"=>$numDownloads,
            "app_description"=>$appDescription,
            "app_url"=>$appURL,
            "app_icon"=>$appIcon
        );
        $app_info = $app_model->createAppGames($new_app_record);
    }

    $html = var_export($data, true);
    $response->getBody()->write($html);
    return $response->withStatus($response_code);
}

function handleUpdateApps(Request $request, Response $response, array $args) {

    $data = $request->getParsedBody();
    $response_code = HTTP_OK;

    //-- Go over elements stored in the $data array
    //-- In a for/each loop
    $app_model = new AppModel(); 

     //Create Empty array to insert what we would like to update    
     $existing_apps_record = array();
   
    //-- We retrieve the key and its value
    foreach($data as $key => $single_app){

        //Retreive the Game Id for the specific game we want to update
        $existing_appId = $single_app["app_id"];

        //-- Check data set and retrieve the key and its value
        if(isset($single_app["app_name"])){
            $existing_apps_record["app_name"] = $single_app["app_name"];
        }
        if(isset($single_app["app_category"])){
            $existing_apps_record["app_category"] = $single_app["app_category"];
        }
        if(isset($single_app["app_url"])){
            $existing_apps_record["app_url"] = $single_app["app_url"];
        }
        if(isset($single_app["app_icon"])){
            $existing_apps_record["app_icon"] = $single_app["app_icon"];
        }
        if(isset($single_app["app_category"])){
            $existing_apps_record["app_category"] = $single_app["app_category"];
        }
        if(isset($single_app["app_developer"])){
            $existing_apps_record["app_developer"] = $single_app["app_developer"];
        }
        if(isset($single_app["app_description"])){
            $existing_apps_record["app_description"] = $single_app["app_description"];
        }
        if(isset($single_app["num_downloads"])){
            $existing_apps_record["num_downloads"] = $single_app["num_downloads"];
        }

        
        //-- We perform an UPDATE/CREATE SQL statement
        $app_model->updateAppGames($existing_apps_record, array("app_id"=>$existing_appId));
    }

    $html = var_export($data, true);
    $response->getBody()->write($html);
    return $response->withStatus($response_code);
}

function handleDeleteApp(Request $request, Response $response, array $args) {
    $app_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $app_model = new AppModel();
    $data = $request->getParsedBody();

    // Retreive the app from the request's URI.
    $app_id = $args["app_id"];
    if (isset($app_id)) {
        // Fetch the info about the specified app.
        $app_model->deleteAppGames(array("app_id"=>$app_id));
        $app_info = "App has been DELETED";
        if (!$app_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified app.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    } 
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($app_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

