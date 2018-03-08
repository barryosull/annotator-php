<?php

namespace Annotator\Flysystem;

interface Gateway
{
    public function all(): array;

    public function add($annotation): array;

    public function get($id): array;

    public function replace($id, $annotation): void;

    public function delete($id): void;
}