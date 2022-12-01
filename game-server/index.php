<?php
header("Access-Control-Allow-Origin: *");
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
require_once './includes/app_constants.php';
require_once './includes/helpers/helper_functions.php';

//--Step 1) Instantiate App.
$app = AppFactory::create();
//-- Step 2) Add routing middleware.
$app->addRoutingMiddleware();
//-- Adding Slim body parsing
$app->addBodyParsingMiddleware();
//-- Step 3) Add error handling middleware.
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
//-- Step 4)
// TODO: change the name of the sub directory here. You also need to change it in .htaccess
$app->setBasePath("/GameAPI/game-server");

//-- Step 5) Include the files containing the definitions of the callbacks.
require_once './includes/routes/apps_routes.php';
require_once './includes/routes/authors_routes.php';
require_once './includes/routes/games_routes.php';
require_once './includes/routes/reviews_routes.php';
require_once './includes/routes/streamers_routes.php';

//-- Step 6)
// TODO: And here we define app routes.

//--------------------------- APP -------------------------------------
$app->get("/apps", "handleGetAllApps");
$app->get("/apps/{app_id}", "handleGetAppById");

$app->post("/apps", "handleCreateApps");
$app->put("/apps", "handleUpdateApps");
$app->delete("/apps", "handleDeleteApps");
$app->delete("/apps/{app_id}", "handleDeleteApp");
//-------------------------- AUTHOR ------------------------------------
$app->get("/authors", "handleGetAllAuthors");
$app->get("/authors/{author_id}", "handleGetAuthorById");

$app->post("/authors", "handleCreateAuthors");
$app->put("/authors", "handleUpdateAuthors");
$app->delete("/authors", "handleDeleteAuthors");
$app->delete("/authors/{author_id}", "handleDeleteAuthor");
//-------------------------- GAME --------------------------------------
$app->get("/games", "handleGetAllGames");
$app->get("/games/{game_id}", "handleGetGameById");

$app->post("/games", "handleCreateGames");
$app->put("/games", "handleUpdateGames");
$app->delete("/games", "handleDeleteGames");
$app->delete("/games/{game_id}", "handleDeleteGameById");
//-------------------------- REVIEW ------------------------------------
$app->get("/reviews", "handleGetAllReviews");
$app->get("/reviews/{review_id}", "handleGetReviewById");

$app->post("/reviews", "handleCreateReviews");
$app->put("/reviews", "handleUpdateReviews");
$app->delete("/reviews", "handleDeleteReviews");
$app->delete("/reviews/{review_id}", "handleDeleteReview");
//------------------------- STREAMER -----------------------------------
$app->get("/streamers", "handleGetAllStreamers");
$app->get("/streamers/{streamer_id}", "handleGetStreamerById");

$app->post("/streamers", "handleCreateStreamers");
$app->put("/streamers", "handleUpdateStreamers");
$app->delete("/streamers", "handleDeleteStreamers");
$app->delete("/streamers/{streamer_id}", "handleDeleteStreamer");
//-------------------------- STREAM ------------------------------------
$app->get("/streams", "handleGetAllStreams");
$app->get("/streams/{stream_id}", "handleGetStreamById");

$app->post("/streams", "handleCreateStreams");
$app->put("/streams", "handleUpdateStreams");
$app->delete("/streams", "handleDeleteStreams");
$app->delete("/streams/{stream_id}", "handleDeleteStream");

// Run the app.
$app->run();
