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
require_once './includes/routes/games_routes.php';
require_once './includes/routes/genres_routes.php';
require_once './includes/routes/platforms_routes.php';
require_once './includes/routes/publishers_routes.php';

//-- Step 6)
// TODO: And here we define app routes.

//---------------------- GAME ---------------------------------
$app->get("/games", "handleGetAllGames");
$app->get("/games/{game_id}", "handleGetGameById");
//---------------------- GENRE --------------------------------
$app->get("/genres", "handleGetAllGenres");
$app->get("/genres/{genre_id}", "handleGetGenreById");
//---------------------- PLATFORM -----------------------------
$app->get("/platforms", "handleGetAllPlatforms");
$app->get("/platforms/{platform_id}", "handleGetPlatformById");
//---------------------- PUBLISHER -----------------------------
$app->get("/publishers", "handleGetAllPublishers");
$app->get("/publishers/{publisher_id}", "handleGetPublisherById");

// Run the app.
$app->run();
