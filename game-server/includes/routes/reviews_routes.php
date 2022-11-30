<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client;

require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/ReviewModel.php';
require_once __DIR__ . './../models/GameModel.php';
require_once __DIR__ . './../models/AuthorModel.php';

// Callback for HTTP GET /reviews
//-- Supported filtering operation: by review rating.
function handleGetAllReviews(Request $request, Response $response, array $args)
{
    $reviews = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $review_model = new ReviewModel();

    // Retreive the query string parameter from the request's URI.
    $filter_params = $request->getQueryParams();
    if (isset($filter_params["rating"])) {
        // Fetch the list of reviews matching the provided rating.
        $reviews = $review_model->getWhereLike($filter_params["rating"]);
    } else {
        // No filtering by review rating detected.
        $reviews = $review_model->getAll();
    }
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //--
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($reviews, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleGetReviewById(Request $request, Response $response, array $args)
{
    $review_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $review_model = new ReviewModel();

    // Retreive the review if from the request's URI.
    $review_id = $args["review_id"];
    if (isset($review_id)) {
        // Fetch the info about the specified review.
        $review_info = $review_model->getReviewById($review_id);
        if (!$review_info) {
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
        $response_data = json_encode($review_info, JSON_INVALID_UTF8_SUBSTITUTE);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleCreateReviews(Request $request, Response $response, array $args)
{
    $response_code = HTTP_OK;
    $review_model = new ReviewModel();
    $game_model = new GameModel();
    $author_model = new AuthorModel();

    $data = $request->getParsedBody();

    // Fetch the info about the specified review.
    foreach ($data as $key => $single_review) {

        //check if there is game id and author id
        if (!empty($game_model->getGameById($single_review["game_id"])["game_id"])) {
            //something was found
            $gameId = $single_review["game_id"];
        } else {
            $response_data = makeCustomJSONError("NoSuchElementException", "GameId was not found.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }

        if (!empty($author_model->getAuthorById($single_review["author_id"])["author_id"])) {
            //something was found
            $author_id = $single_review["author_id"];
        } else {
            $response_data = makeCustomJSONError("NoSuchElementException", "AuhtorId was not found.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }


        if (isset($single_review["review_text"]) && isset($single_review["rating"])) {

            $reviewText = $single_review["review_text"];
            $rating = $single_review["rating"];

            $new_review_record = array(
                "review_text" => $reviewText,
                "rating" => $rating,
                "game_id" => $gameId,
                "author_id" => $author_id
            );
        } else {
            $response_data = makeCustomJSONError("UnsetParamaterException", "All paramaters must be set.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }


        $review_model->createReviews($new_review_record);
    }

    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
        $response_data = makeCustomJSONError("Success", "Review has been Created!", $response_data);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleUpdateReviews(Request $request, Response $response, array $args)
{

    $data = $request->getParsedBody();
    $response_code = HTTP_OK;
    $review_model = new ReviewModel();

    for ($index = 0; $index < count($data); $index++) {
        $single_review = $data[$index];
        $reviewId = $single_review["review_id"];
        $reviewText = $single_review["review_text"];
        $rating = $single_review["rating"];
        $gameId = $single_review["review_id"];
        $author_id = $single_review["author_id"];

        //-- We retrieve the key and its value
        //-- We perform an UPDATE/CREATE SQL statement

        $existing_review_record = array(
            "review_text" => $reviewText,
            "rating" => $rating,
            "game_id" => $gameId,
            "author_id" => $author_id,
        );

        $review_model->updateReviews($existing_review_record, array("review_id" => $reviewId));
    }

    $html = var_export($data, true);
    $response->getBody()->write($html);
    return $response->withStatus($response_code);
}

function handleDeleteReview(Request $request, Response $response, array $args) {
    $review_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $review_model = new ReviewModel();

    // Retreive the review from the request's URI.
    $review_id = $args["review_id"];
    if (isset($review_id)) {
        // Fetch the info about the specified review.
        $review_model->deleteReviews(array("review_id" => $review_id));
        $review_info = "Review has been DELETED";
        if (!$review_info) {
            // No matches found?
            $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified review.");
            $response->getBody()->write($response_data);
            return $response->withStatus(HTTP_NOT_FOUND);
        }
    }
    // Handle serve-side content negotiation and produce the requested representation.    
    $requested_format = $request->getHeader('Accept');
    //-- We verify the requested resource representation.    
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($review_info, JSON_INVALID_UTF8_SUBSTITUTE);
        $response_data = makeCustomJSONError("Success", "Review has been deleted", $response_data);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

function handleDeleteReviews(Request $request, Response $response, array $args) {
    $response_data = array();
    $response_code = HTTP_OK;
    $review_model = new ReviewModel();
    $data = $request->getParsedBody();
    $review_id = "";

    // Retreive the game from the request's URI.
    foreach($data as $key => $single_review){
        $review_id = $single_review["review_id"];
        if (isset($review_id)) {

            // Fetch the info about the specified game.
            $review_model->deleteReviews(array("review_id"=>$review_id));
            if (!$data) {
                // No matches found?
                $response_data = makeCustomJSONError("resourceNotFound", "No matching record was found for the specified review.");
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
        $response_data = makeCustomJSONError("Success", "Reviews has been deleted", $response_data);
    } else {
        $response_data = json_encode(getErrorUnsupportedFormat());
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}
