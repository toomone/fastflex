<?php

// Error Handler for any uncaught exception
// -----------------------------------------------------------------------------
// This can be silenced by turning on Slim Debugging. All exceptions thrown by
// our application will be collected here.
$app->error(function (\Exception $e) use ($app) {
    $app->render('error.html', array(
        'message' => $e->getMessage()
    ), 500);
});


// Welcome Page
// -----------------------------------------------------------------------------
// A simple about the project page
$app->get('/', function () use ($app) {
    $app->render('about.html');
});


// Version Endpoint
// -----------------------------------------------------------------------------
// Heartbeat endpoint, should always return 200 and the application version.
$app->get('/version', function () use ($app) {
    $app->response->write($app->config('version'));
});


// Zen Statement From GitHub
// -----------------------------------------------------------------------------
// Can be used to verify that the application has external connectivity.
$app->get('/zen', function () use ($app) {
    $response = $app->curl->get('https://api.github.com/zen');
    if ($response->headers['Status-Code'] != 200) {
        $app->halt(502, 'GitHub has failed with :' + $response->headers['Status-Code']);
    }
    $app->response->write($response->body);
});


// Fetch a file from the file store.
// -----------------------------------------------------------------------------
// Authenticated request for a file from the file store
$app->get('/files/:filename', $authenticate($app), function ($filename) use ($app) {
    $supported_types = (object) array(
        'json'    => 'application/json',
        'xml'     => 'application/xml',
        'csv'     => 'text/csv',
        'unknown' => 'application/octet-stream'
    );

    $filename  = pathinfo($filename, PATHINFO_BASENAME);
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $path      = realpath(__DIR__ . '/../file_store/' . $filename);

    $content_type
        = property_exists($supported_types, $extension)
        ? $supported_types->$extension
        : $supported_types->unknown;

    if (!is_readable($path)) {
        $app->notFound();
    }

    $app->response->headers->set('Content-Type', $content_type);
    readfile($path);
});


// Say hello to a user
// -----------------------------------------------------------------------------
// Used to test parameters from [issue 4](https://github.com/there4/slim-unit-testing-example/issues/4).
$app->get('/say-hello/:name', function ($name) use ($app) {
    $response = $name ? 'Hello ' . $name : 'Missing parameter for name';
    $app->response->write($response);
});


$app->map('/say-hello', function () use ($app) {
    $name = $app->request->params('name');
    $response = $name ? 'Hello ' . $name : 'Missing parameter for name';
    $app->response->write($response);
})->via('POST', 'PUT');


$app->post('/issue3', function () use ($app) {
    $name = $app->request->post('name');
    $response = $name ? 'Hello ' . $name : 'Missing parameter for name';
    $app->response->write($response);
});

$app->get('/issue12', function () use ($app) {
    $referer = $app->request->headers('referer');
    $response = $referer ? $referer : 'Missing referer header';
    $app->response->write($response);
});


/* End of file app.php */
