<?php

namespace Annotator\Flysystem;

use League\Flysystem;
use League\Flysystem\Memory\MemoryAdapter;
use League\Flysystem\AdapterInterface;
use Ramsey\Uuid\Uuid;

class GatewayFlysystem implements Gateway
{
    private $filesystem;

    public function __construct(AdapterInterface $adapter = null)
    {
        if (!$adapter) {
            $adapter  = new MemoryAdapter();
        }
        $this->filesystem = new Flysystem\Filesystem($adapter);
    }

    public function all($articleId): array
    {
        $files = $this->filesystem->listContents("$articleId/");
        $annotations = [];
        foreach ($files as $file) {
            if ($file['type'] == 'file') {
                $annotations[] = json_decode($this->filesystem->read($file['path']), true);
            }
        }

        return $annotations;
    }

    public function add($articleId, $annotation): array
    {
        $annotation['id'] = Uuid::uuid4()->toString();

        $this->filesystem->write("$articleId/{$annotation['id']}.ant", json_encode($annotation));

        return $annotation;
    }

    public function get($articleId, $annotationId): array
    {
        return json_decode($this->filesystem->read("$articleId/{$annotationId}.ant"), true);
    }

    public function replace($articleId, $annotationId, $annotation)
    {
        $this->filesystem->put("$articleId/{$annotationId}.ant", json_encode($annotation));
    }

    public function delete($articleId, $annotationId)
    {
        $this->filesystem->delete("$articleId/{$annotationId}.ant");
    }
}