<?php

namespace Annotator\Flysystem;

use Mongo;
use MongoId;

class GatewayMongo implements Gateway
{
    public function all(): array
    {
        $out = [];

        $m = new Mongo();
        $posts = $m->annotator->annotations->find();

        foreach($posts as $post) {
            $post['id'] = (string) $post['_id'];
            unset($post['_id']);
            $out[] = $post;
        }

        return $out;
    }

    public function add($annotation): array
    {
        $m = new Mongo();
        $m->annotator->annotations->insert($annotation, array('safe' => true));

        $annotation['id'] = (string) $annotation['_id'];
        unset($annotation['_id']);

        return $annotation;
    }

    public function get($id): array
    {
        $m = new Mongo();
        $post = $m->annotator->annotations->findOne(array('_id' => new MongoId($id)));

        $post['id'] = (string) $post['_id'];
        unset($post['_id']);

        return $post;
    }

    public function replace($id, $annotation): void
    {
        unset($annotation['id']);

        $m = new Mongo();
        $m->annotator->annotations->update(
            array('_id' => new MongoId($id)),
            array('$set' => $annotation)
        );
    }

    public function delete($id): void
    {
        $m = new Mongo();
        $m->annotator->annotations->remove(
            array('_id' => new MongoId($id))
        );
    }
}