<?php

namespace Annotator\Flysystem;

interface Gateway
{
    public function all($articleId): array;

    public function add($articleId, $annotation): array;

    public function get($articleId, $annotationId): array;

    public function replace($articleId, $annotationId, $annotation);

    public function delete($articleId, $annotationId);
}