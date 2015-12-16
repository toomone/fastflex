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



/* End of file app.php */
