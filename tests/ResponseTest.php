<?php

use AdrianoRosa\HttpResponse\Response;

/**
 * @project: HttpResponseFormatter
 * @file   : ResponseTest.php
 * @author : Adriano Rosa <http://adrianorosa.com>
 * @date   : 2016-12-27 14:52
 */
class ResponseTest extends TestCase
{
    public function testResponseToJson()
    {
        $data = [
          'foo' => 'bar'
        ];

        $response = Response::create()->toJson($data);

        $this->assertInstanceOf('AdrianoRosa\HttpResponse\Response', $response);
        $this->assertEquals('{"code":200,"status":"success","data":{"foo":"bar"}}', $response->getContent());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('code', $content);
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('foo', $content['data']);
    }

    public function testResponseSafeJson()
    {
        $data = [
            'foo' => 'bar'
        ];

        $response = Response::create()->safeJson($data);

        $this->assertInstanceOf('AdrianoRosa\HttpResponse\Response', $response);

        $content = $this->fromSafeJson($response);

        $this->assertEquals('{"code":200,"status":"success","data":{"foo":"bar"}}', $this->fromSafeJson($response, true));

        $this->assertArrayHasKey('code', $content);
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('foo', $content['data']);
    }

    public function testResponseToJsonPayloadString()
    {
        $data = 'foo';

        $response = Response::create()->toJson($data);

        $this->assertInstanceOf('AdrianoRosa\HttpResponse\Response', $response);
        $this->assertEquals('{"code":200,"status":"success","data":["foo"]}', $response->getContent());

        $content = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('code', $content);
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('data', $content);
    }

    public function testResponseSafeJsonPayloadString()
    {
        $data = 'foo';

        $response = Response::create()->safeJson($data);

        $this->assertInstanceOf('AdrianoRosa\HttpResponse\Response', $response);
        $this->assertEquals('{"code":200,"status":"success","data":["foo"]}', $this->fromSafeJson($response, true));

        $content = $this->fromSafeJson($response);

        $this->assertArrayHasKey('code', $content);
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('data', $content);
    }

    public function testResponseToJsonSTATUS()
    {
        $data = [
            'foo' => 'bar'
        ];

        $response = Response::create()->toJson($data, 301);

        $this->assertInstanceOf('AdrianoRosa\HttpResponse\Response', $response);
        $this->assertEquals(301, $response->status());

        $response = Response::create()->toJson($data, 404);
        $this->assertInstanceOf('AdrianoRosa\HttpResponse\Response', $response);
        $this->assertEquals(404, $response->status());

        $response = Response::create()->toJson($data, 200, ['X-Foo' => 'Bar']);
        $this->assertInstanceOf('AdrianoRosa\HttpResponse\Response', $response);
        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('x-foo', $response->headers->all());
        $this->assertEquals('Bar', $response->headers->get('x-foo'));
    }

    public function testResponseSafeJsonSTATUS()
    {
        $data = [
            'foo' => 'bar'
        ];

        $response = Response::create()->safeJson($data, 301);

        $this->assertInstanceOf('AdrianoRosa\HttpResponse\Response', $response);
        $this->assertEquals(301, $response->status());

        $response = Response::create()->safeJson($data, 404);
        $this->assertInstanceOf('AdrianoRosa\HttpResponse\Response', $response);
        $this->assertEquals(404, $response->status());

        $response = Response::create()->safeJson($data, 200, ['X-Foo' => 'Bar']);
        $this->assertInstanceOf('AdrianoRosa\HttpResponse\Response', $response);
        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('x-foo', $response->headers->all());
        $this->assertEquals('Bar', $response->headers->get('x-foo'));
    }
}
