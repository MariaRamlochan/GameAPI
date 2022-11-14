<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/GenreModel.php';

// Callback for HTTP GET /genres
//-- Supported filtering operation: by genre type.
function handleGetAllGenres(Request $request, Response $response, array $args) {
    $genres = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $genre_model = new GenreModel();

    // Retreive the query string parameter from the request's URI.
    $filter_params = $request->getQueryParams();
    if (isset($filter_params["type"])) {
        // Fetch the list of genres matching the provided type.
        $genres = $genre_model->getWhereLike($filter_params["type"]);
    } else {
        // No filtering by genre type detected.
        $genres = $genre_model->getAll();
    }    
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($genres, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetGenreById(Request $request, Response $response, array $args) {
    $genre_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $genre_model = new GenreModel();

    // Retreive the genre if from the request's URI.
    $genre_id = $args["genre_id"];
    if (isset($genre_id)) {
        // Fetch the info about the specified genre.
        $genre_info = $genre_model->getGenreById($genre_id);
        if (!$genre_info) {
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
        $response_data = json_encode($genre_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}