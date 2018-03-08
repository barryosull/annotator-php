<?php

namespace AnnotatorTests\Flysystem;

use Annotator\Flysystem\Gateway;
use PHPUnit\Framework\TestCase;

abstract class GatewayTest extends TestCase
{
    const ARTICLE_ID = "article-id";

    abstract protected function gateway(): Gateway;

    public function test_adding_an_annotation_gives_it_an_id()
    {
        $annotation = [];

        $annotation = $this->gateway()->add(self::ARTICLE_ID, $annotation);

        $this->assertTrue(isset($annotation['id']));
    }

    public function test_getting_an_annotation()
    {
        $annotation = [];

        $annotation = $this->gateway()->add(self::ARTICLE_ID, $annotation);

        $actualAnnotation = $this->gateway()->get(self::ARTICLE_ID, $annotation['id']);

        $this->assertEquals($annotation, $actualAnnotation);
    }

    public function test_replacing_an_annotation()
    {
        $annotation = [];
        $annotation = $this->gateway()->add(self::ARTICLE_ID, $annotation);

        $annotation['key'] = "value";
        $this->gateway()->replace(self::ARTICLE_ID, $annotation['id'], $annotation);

        $actualAnnotation = $this->gateway()->get(self::ARTICLE_ID, $annotation['id']);

        $this->assertEquals($annotation, $actualAnnotation);
    }

    public function test_getting_all_annotations()
    {
        $annotationA = ['a'];
        $annotationB = ['b'];
        $annotationA = $this->gateway()->add(self::ARTICLE_ID, $annotationA);
        $annotationB = $this->gateway()->add(self::ARTICLE_ID, $annotationB);

        $actual = $this->gateway()->all(self::ARTICLE_ID);

        $this->assertEquals([$annotationA, $annotationB], $actual);
    }

    public function test_deleting_an_annotation()
    {
        $annotation = [];
        $annotation = $this->gateway()->add(self::ARTICLE_ID, $annotation);
        $this->gateway()->delete(self::ARTICLE_ID, $annotation['id']);

        $actual = $this->gateway()->all(self::ARTICLE_ID);

        $this->assertEmpty($actual);
    }
}