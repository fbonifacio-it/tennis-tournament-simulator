<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\TournamentController;
use App\Controller\AuthController;
use App\Repository\TournamentRepository;
use App\Repository\PlayerRepository;
use App\Middleware\AuthMiddleware;
use App\Security\Env;


Env::loadEnv();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($uri === '/swagger') {
    header('Content-Type: text/html');
    readfile(__DIR__ . '/swagger/index.html');
    exit;
}

if ($uri === '/swagger.json') {
    header('Content-Type: application/json');
    readfile(__DIR__ . '/swagger/swagger.json');
    exit;
}

$authController = new AuthController();
$tournamentController = new TournamentController(new PlayerRepository(), new TournamentRepository());
$authMiddleware = new AuthMiddleware();

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


if ($uri === '/register' && $method === 'POST') { // register user
    $authController->register();
} elseif ($uri === '/login' && $method === 'POST') { // login registered user
    $authController->login();
} elseif ($uri === '/logout' && $method === 'POST') { // logout
    $authMiddleware->authenticate();
    $authController->logout();
} elseif ($uri === '/tournament' && $method === 'POST') { // simulate a tournament and get the result
    $authMiddleware->authenticate();
    $requestBody = json_decode(file_get_contents("php://input"), true);
    echo json_encode($tournamentController->createTournament($requestBody['players'] ?? []));
} elseif (preg_match('#^/tournament/(\d+)$#', $uri, $matches) && $method === 'GET') { // get tournament by id
    $authMiddleware->authenticate();
    echo json_encode($tournamentController->getTournament((int)$matches[1]));
} elseif ($uri === '/tournament/history' && $method === 'GET') { // get all tournaments data
    $authMiddleware->authenticate();
    echo json_encode($tournamentController->getTournamentHistory());
} elseif ($uri === '/tournament/search' && $method === 'POST') { // search tournament by filters
    $authMiddleware->authenticate();
    $requestBody = json_decode(file_get_contents("php://input"), true);
    echo json_encode($tournamentController->search($requestBody));    
} else { // endpoint not found
    http_response_code(404);
    echo json_encode(["error" => "Route not found"]);
}
