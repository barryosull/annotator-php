<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Annotator\Flysystem\GatewayMongo;

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/config.php';

$app = new Silex\Application();

/***
 * Accepting JSON in request body.
 * @note: the method described in http://silex.sensiolabs.org/doc/cookbook/json_request_body.html doesn't allow us to get the whole parameter array.
 */
$app->before(function (Request $request) use ($app) {
	if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
		$app['data'] = json_decode($request->getContent(), true);
	}
});

/***
 * Endpoints.
 * @see https://github.com/okfn/annotator/wiki/Storage
 */
$app->get('/', function () use ($app) {
	$out = array(
		'name'    => "Annotator Store API (PHP)",
		'version' => '1.0.0',
		'author'  => 'julien-c, barryosull'
	);
	return $app->json($out);
});

$app->get('/annotations', function () use ($app) {

	$annotations = (new GatewayMongo())->all();

	return $app->json($annotations);
});

$app->post('/annotations', function () use ($app) {

    $post = (new GatewayMongo())->add($app['data']);
	
	return $app->json($post);
});

$app->get('/annotations/{id}', function ($id) use ($app) {

    $post = (new GatewayMongo())->get($id);

	return $app->json($post);
});

$app->put('/annotations/{id}', function (Request $request, $id) use ($app) {

    (new GatewayMongo())->replace($id, $app['data']);
	
	return new Response('', 303, array('Location' => $request->getUri()));
});

$app->delete('/annotations/{id}', function (Request $request, $id) use ($app) {

    (new GatewayMongo())->delete($id);
	
	return new Response('', 204);
});


/***
 * Auth Endpoint.
 * @see https://github.com/okfn/annotator/wiki/Authentication
 */
$app->get('/auth/token', function () use ($app) {
	$jwt = jwt::encode(
		[
			'consumerKey' => CONSUMER_KEY,
			'userId'      => USER_ID,
			'issuedAt'    => time(),
			'ttl'         => CONSUMER_TTL
		],
		CONSUMER_SECRET
	);
	
	return new Response($jwt);
});


/***
 *
 * Run, App, Run!
 *
 */

$app['debug'] = true;
$app->run();