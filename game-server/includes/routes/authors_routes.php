<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/AuthorModel.php';

// Callback for HTTP GET /authors
//-- Supported filtering operation: by author name.
function handleGetAllAuthors(Request $request, Response $response, array $args) {
    $authors = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $author_model = new AuthorModel();

    // Retreive the query string parameter from the request's URI.
    $filter_params = $request->getQueryParams();
    if (isset($filter_params["name"])) {
        // Fetch the list of authors matching the provided name.
        $authors = $author_model->getWhereLike($filter_params["name"]);
    } else {
        // No filtering by author name detected.
        $authors = $author_model->getAll();
    }    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($authors, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetAuthorById(Request $request, Response $response, array $args) {
    $author_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $author_model = new AuthorModel();

    // Retreive the author if from the request's URI.
    $author_id = $args["author_id"];
    if (isset($author_id)) {
        // Fetch the info about the specified author.
        $author_info = $author_model->getAuthorById($author_id);
        if (!$author_info) {
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
        $response_data = json_encode($author_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}