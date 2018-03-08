<?php

namespace AnnotatorTests\Flysystem;

use Annotator\Flysystem\Gateway;
use PHPUnit\Framework\TestCase;

abstract class GatewayTest extends TestCase
{
    abstract protected function gateway(): Gateway;

    public function test_adding_an_annotation_gives_it_an_id()
    {
        $annotation = [];

        $annotation = $this->gateway()->add($annotation);

        $this->assertTrue(isset($annotation['id']));
    }

    public function test_getting_an_annotation()
    {
        $annotation = [];

        $annotation = $this->gateway()->add($annotation);

        $actualAnnotation = $this->gateway()->get($annotation['id']);

        $this->assertEquals($annotation, $actualAnnotation);
    }

    public function test_replacing_an_annotation()
    {
        $annotation = [];
        $annotation = $this->gateway()->add($annotation);

        $annotation['key'] = "value";
        $this->gateway()->replace($annotation['id'], $annotation);

        $actualAnnotation = $this->gateway()->get($annotation['id']);

        $this->assertEquals($annotation, $actualAnnotation);
    }

    public function test_getting_all_annotations()
    {
        $annotationA = ['a'];
        $annotationB = ['b'];
        $annotationA = $this->gateway()->add($annotationA);
        $annotationB = $this->gateway()->add($annotationB);

        $actual = $this->gateway()->all();

        $this->assertEquals([$annotationA, $annotationB], $actual);
    }

    public function test_deleting_an_annotation()
    {
        $annotation = [];
        $annotation = $this->gateway()->add($annotation);
        $this->gateway()->delete($annotation['id']);

        $actual = $this->gateway()->all();

        $this->assertEmpty($actual);
    }
}