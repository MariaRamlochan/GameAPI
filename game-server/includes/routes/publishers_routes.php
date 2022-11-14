<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/PublisherModel.php';

// Callback for HTTP GET /publishers
//-- Supported filtering operation: by publisher name.
function handleGetAllPublishers(Request $request, Response $response, array $args) {
    $publishers = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $publisher_model = new PublisherModel();

    // Retreive the query string parameter from the request's URI.
    $filter_params = $request->getQueryParams();
    if (isset($filter_params["name"])) {
        // Fetch the list of publishers matching the provided name.
        $publishers = $publisher_model->getWhereLike($filter_params["name"]);
    } else {
        // No filtering by publisher name detected.
        $publishers = $publisher_model->getAll();
    }    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($publishers, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetPublisherById(Request $request, Response $response, array $args) {
    $publisher_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $publisher_model = new PlatformModel();

    // Retreive the publisher if from the request's URI.
    $publisher_id = $args["publisher_id"];
    if (isset($publisher_id)) {
        // Fetch the info about the specified publisher.
        $publisher_info = $publisher_model->getPlatformById($publisher_id);
        if (!$publisher_info) {
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
        $response_data = json_encode($publisher_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}