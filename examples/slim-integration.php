<?php

/**
 * Slim Framework 4 Integration Example
 */

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Williamug\Versioning\UniversalVersioning;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Configure versioning as middleware or in bootstrap
UniversalVersioning::setRepositoryPath(__DIR__ . '/..');
UniversalVersioning::setFallbackVersion(getenv('APP_VERSION') ?: 'dev');

// If you have PSR-16 cache configured:
// UniversalVersioning::setCacheAdapter($container->get('cache'));

// Add versioning to dependency container
$container = $app->getContainer();
$container->set('version', function () {
  return [
    'tag' => UniversalVersioning::tag(),
    'full' => UniversalVersioning::full(),
    'commit' => UniversalVersioning::commit(),
  ];
});

// Routes
$app->get('/', function (Request $request, Response $response) {
  $version = UniversalVersioning::tag();
  $commit = UniversalVersioning::commit();

  $html = <<<HTML
    <!DOCTYPE html>
    <html>
    <head><title>My Slim App</title></head>
    <body>
        <h1>Welcome</h1>
        <footer>
            <p>Version: {$version}</p>
            <p>Build: {$commit}</p>
        </footer>
    </body>
    </html>
    HTML;

  $response->getBody()->write($html);
  return $response;
});

$app->get('/api/version', function (Request $request, Response $response) {
  $data = [
    'version' => UniversalVersioning::tag(),
    'full' => UniversalVersioning::full(),
    'commit' => UniversalVersioning::commit(),
  ];

  $response->getBody()->write(json_encode($data));
  return $response->withHeader('Content-Type', 'application/json');
});

// Middleware to add version to all responses
$app->add(function (Request $request, $handler) {
  $response = $handler->handle($request);
  return $response->withHeader('X-App-Version', UniversalVersioning::tag());
});

// Using Twig template engine
/*
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

$twig = Twig::create(__DIR__ . '/../templates', ['cache' => false]);

// Add version to all templates
$twig->getEnvironment()->addGlobal('app_version', UniversalVersioning::tag());
$twig->getEnvironment()->addGlobal('app_commit', UniversalVersioning::commit());

$app->add(TwigMiddleware::create($app, $twig));

$app->get('/', function (Request $request, Response $response) use ($twig) {
    return $twig->render($response, 'home.html.twig', [
        'title' => 'Home'
    ]);
});
*/

$app->run();
