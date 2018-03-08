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

    public function all(): array
    {
        $files = $this->filesystem->listContents();
        $annotations = [];
        foreach ($files as $file) {
            if ($file['type'] == 'file') {
                $annotations[] = json_decode($this->filesystem->read($file['path']), true);
            }
        }

        return $annotations;
    }

    public function add($annotation): array
    {
        $annotation['id'] = Uuid::uuid4()->toString();

        $this->filesystem->write("{$annotation['id']}.ant", json_encode($annotation));

        return $annotation;
    }

    public function get($id): array
    {
        return json_decode($this->filesystem->read("{$id}.ant"), true);
    }

    public function replace($id, $annotation): void
    {
        $this->filesystem->put("{$id}.ant", json_encode($annotation));
    }

    public function delete($id): void
    {
        $this->filesystem->delete("{$id}.ant");
    }
}