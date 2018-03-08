<?php namespace Annotator\Flysystem;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use League\Flysystem\Adapter\Local;
use JWT;

class App
{
    private $baseUrl;
    private $gateway;

    public function __construct(string $baseUrl, string $storagePath)
    {
        $this->baseUrl = $baseUrl;
        $adapter = new Local($storagePath);
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
        $app->get($this->baseUrl, function () use ($app) {
            $out = array(
                'name'    => "Annotator Store API (PHP)",
                'version' => '1.0.0',
                'author'  => 'julien-c, barryosull'
            );
            return $app->json($out);
        });

        $app->get($this->baseUrl.'/{article_id}/annotations', function ($article_id) use ($app) {

            $annotations = $this->gateway->all($article_id);

            return $app->json($annotations);
        });

        $app->post($this->baseUrl.'/{article_id}/annotations', function ($article_id) use ($app) {

            $post = $this->gateway->add($article_id, $app['data']);

            return $app->json($post);
        });

        $app->get($this->baseUrl.'/{article_id}/annotations/{id}', function ($article_id, $id) use ($app) {

            $post = $this->gateway->get($article_id, $id);

            return $app->json($post);
        });

        $app->put($this->baseUrl.'/{article_id}/annotations/{id}', function (Request $request, $article_id, $id) use ($app) {

            $this->gateway->replace($article_id, $id, $app['data']);

            return new Response('', 303, array('Location' => $request->getUri()));
        });

        $app->delete($this->baseUrl.'/{article_id}/annotations/{id}', function ($article_id, $id) use ($app) {

            $this->gateway->delete($article_id, $id);

            return new Response('', 204);
        });

        /***
         * Run, App, Run!
         */
        $app['debug'] = true;
        $app->run();
    }
}