<?php namespace Annotator\Flysystem;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use League\Flysystem\Adapter\Local;
use JWT;

require __DIR__.'/config.php';

class App
{
    private $gateway;

    public function __construct(string $storage_path)
    {
        $adapter = new Local($storage_path);
        $this->gateway = new GatewayFlysystem($adapter);
    }

    public function boot()
    {
        $app = new \Silex\Application();

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

            $annotations = $this->gateway->all();

            return $app->json($annotations);
        });

        $app->post('/annotations', function () use ($app) {

            $post = (new GatewayMongo())->add($app['data']);

            return $app->json($post);
        });

        $app->get('/annotations/{id}', function ($id) use ($app) {

            $post = $this->gateway->get($id);

            return $app->json($post);
        });

        $app->put('/annotations/{id}', function (Request $request, $id) use ($app) {

            $this->gateway->replace($id, $app['data']);

            return new Response('', 303, array('Location' => $request->getUri()));
        });

        $app->delete('/annotations/{id}', function (Request $request, $id) use ($app) {

            $this->gateway->delete($id);

            return new Response('', 204);
        });

        /***
         * Auth Endpoint.
         * @see https://github.com/okfn/annotator/wiki/Authentication
         */
        $app->get('/auth/token', function () use ($app) {
            $jwt = JWT::encode(
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
         * Run, App, Run!
         */
        $app['debug'] = true;
        $app->run();
    }
}