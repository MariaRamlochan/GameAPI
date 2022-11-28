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

function handleCreateAuthors(Request $request, Response $response, array $args) {
    $response_data = array();
    $response_code = HTTP_OK;
    $author_model = new AuthorModel();
    $data = $request->getParsedBody();


    foreach($data as $key => $single_author){
    // Fetch the info about the specified author.
        if(isset($single_author["name"]) && isset($single_author["num_reviews"])){

            $name = $single_author["name"];
            $numReviews = $single_author["num_reviews"];
            
            $new_author_record = array(
                "name"=>$name,
                "num_reviews"=>$numReviews,
            );

        }else{
            $response_data = makeCustomJSONError("UnsetParamaterException", "All paramaters must be set.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    $author_model->createAuthors($new_author_record);
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
        $response_data = makeCustomJSONError("Success", "Author has been Created!", $response_data);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleUpdateAuthors(Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $response_code = HTTP_OK;
    $author_model = new AuthorModel(); 

     //Create Empty array to insert what we would like to update    
     $existing_author = array();

    foreach($data as $key => $single_author){

        //-- Check data set and retrieve the key and its value
        if(isset($single_author["author_id"])){
            //Retreive the auhtor Id for the specific game we want to update
            $existing_authorId = $single_author["author_id"];
            if($author_model->getAuthorById($existing_authorId) == null){
                $response_data = makeCustomJSONError("resourceNotFound", "no authorID found");
                $response->getBody()->write($response_data);
                return $response->withStatus(HTTP_NOT_FOUND);
            }
        }

        //-- We retrieve the key and its value
        if(isset($single_author["name"])){
            $existing_author["name"] = $single_author["name"];
        }
        if(isset($single_author["num_reviews"])){
            $existing_author["num_reviews"] = $single_author["num_reviews"];
        }
        //-- We perform an UPDATE SQL statement
        $author_model->updateAuthors($existing_author, array("author_id" => $existing_authorId));
    }

    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
        $response_data = makeCustomJSONError("Success", "Author has been Updated", $response_data);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleDeleteAuthor(Request $request, Response $response, array $args) {
    $author_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $author_model = new AuthorModel();
    $data = $request->getParsedBody();

    // Retreive the artist from the request's URI.
    $author_id = $args["author_id"];
    if (isset($author_id)) {
        // Fetch the info about the specified author.
        $author_model->deleteAuthors(array("author_id"=>$author_id));
        $author_info = "Author has been DELETED";
        if (!$author_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified author.");
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

function handleDeleteAuthors(Request $request, Response $response, array $args) {
    $response_data = array();
    $response_code = HTTP_OK;
    $author_model = new AuthorModel();
    $data = $request->getParsedBody();
    $author_id = "";

    // Retreive the game from the request's URI.
    foreach($data as $key => $single_author){
        $author_id = $single_author["author_id"];
        if (isset($author_id)) {

            // Fetch the info about the specified game.
            $author_model->deleteAuthors(array("author_id"=>$author_id));
            if (!$data) {
                // No matches found?
                $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified author.");
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
