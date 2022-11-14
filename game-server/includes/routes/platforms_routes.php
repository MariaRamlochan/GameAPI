<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/PlatformModel.php';

// Callback for HTTP GET /platforms
//-- Supported filtering operation: by platform type.
function handleGetAllPlatforms(Request $request, Response $response, array $args) {
    $platforms = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $platform_model = new PlatformModel();

    // Retreive the query string parameter from the request's URI.
    $filter_params = $request->getQueryParams();
    if (isset($filter_params["type"])) {
        // Fetch the list of platforms matching the provided type.
        $platforms = $platform_model->getWhereLike($filter_params["type"]);
    } else {
        // No filtering by platform type detected.
        $platforms = $platform_model->getAll();
    }    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($platforms, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetPlatformById(Request $request, Response $response, array $args) {
    $platform_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $platform_model = new PlatformModel();

    // Retreive the platform if from the request's URI.
    $platform_id = $args["platform_id"];
    if (isset($platform_id)) {
        // Fetch the info about the specified platform.
        $platform_info = $platform_model->getPlatformById($platform_id);
        if (!$platform_info) {
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
        $response_data = json_encode($platform_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}