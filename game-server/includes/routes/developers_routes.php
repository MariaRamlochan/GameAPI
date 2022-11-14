<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/DeveloperModel.php';

// Callback for HTTP GET /developers
//-- Supported filtering operation: by developer name.
function handleGetAllDevelopers(Request $request, Response $response, array $args) {
    $developers = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $developer_model = new DeveloperModel();

    // Retreive the query string parameter from the request's URI.
    $filter_params = $request->getQueryParams();
    if (isset($filter_params["name"])) {
        // Fetch the list of developers matching the provided name.
        $developers = $developer_model->getWhereLike($filter_params["name"]);
    } else {
        // No filtering by developer name detected.
        $developers = $developer_model->getAll();
    }    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($developers, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetDeveloperById(Request $request, Response $response, array $args) {
    $developer_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $developer_model = new DeveloperModel();

    // Retreive the developer if from the request's URI.
    $developer_id = $args["developer_id"];
    if (isset($developer_id)) {
        // Fetch the info about the specified developer.
        $developer_info = $developer_model->getDeveloperById($developer_id);
        if (!$developer_info) {
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
        $response_data = json_encode($developer_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}