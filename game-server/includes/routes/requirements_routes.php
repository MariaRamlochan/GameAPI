<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/RequirementModel.php';

// Callback for HTTP GET /requirements
//-- Supported filtering operation: by requirement os, processor, memory, graphics, storage.
function handleGetAllRequirements(Request $request, Response $response, array $args) {
    $requirements = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $requirement_model = new RequirementModel();

    // Retreive the query string parameter from the request's URI.
    $filter_params = $request->getQueryParams();
    if (isset($filter_params["os"])) {
        // Fetch the list of requirements matching the provided os.
        $requirements = $requirement_model->getWhereLike($filter_params["os"]);
    } else if (isset($filter_params["processor"])) {
        // Fetch the list of requirements matching the provided processor.
        $requirements = $requirement_model->getWhereLike($filter_params["processor"]);
    } else if (isset($filter_params["memory"])) {
        // Fetch the list of requirements matching the provided memory.
        $requirements = $requirement_model->getWhereLike($filter_params["memory"]);
    } else if (isset($filter_params["graphics"])) {
        // Fetch the list of requirements matching the provided graphics.
        $requirements = $requirement_model->getWhereLike($filter_params["graphics"]);
    } else if (isset($filter_params["storage"])) {
        // Fetch the list of requirements matching the provided storage.
        $requirements = $requirement_model->getWhereLike($filter_params["storage"]);
    } else {
        // No filtering by requirement detected.
        $requirements = $requirement_model->getAll();
    }    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($requirements, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetRequirementById(Request $request, Response $response, array $args) {
    $requirement_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $requirement_model = new RequirementModel();

    // Retreive the requirement if from the request's URI.
    $requirement_id = $args["requirement_id"];
    if (isset($requirement_id)) {
        // Fetch the info about the specified requirement.
        $requirement_info = $requirement_model->getDeveloperById($requirement_id);
        if (!$requirement_info) {
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
        $response_data = json_encode($requirement_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}