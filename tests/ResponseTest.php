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
        $content = json_decode($response->getContent(), true);

        $this->assertInstanceOf('AdrianoRosa\HttpResponse\Response', $response);
        $this->assertEquals('{"code":200,"status":"success","data":{"foo":"bar"}}', $response->getContent());

        $this->assertArrayHasKey('code', $content);
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('foo', $content['data']);

        // Test for append data
        $response = Response::create()->with('Music', ['Jazz', 'Pop', 'Rock'])->toJson($data);
        $content = json_decode($response->getContent(), true);

        $this->assertEquals('{"code":200,"status":"success","data":{"foo":"bar"},"Music":["Jazz","Pop","Rock"]}', $response->getContent());
        $this->assertArrayHasKey('code', $content);
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('foo', $content['data']);
        $this->assertArrayHasKey('Music', $content);
    }

    public function testResponseSafeJson()
    {
        $data = [
            'foo' => 'bar'
        ];

        $response = Response::create()->safeJson($data);
        $content = $this->fromSafeJson($response);

        $this->assertInstanceOf('AdrianoRosa\HttpResponse\Response', $response);

        $this->assertEquals(
            '{"code":200,"status":"success","data":{"foo":"bar"}}',
            $this->fromSafeJson($response, true)
        );
        $this->assertArrayHasKey('code', $content);
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('foo', $content['data']);

        // Test for append data
        $response = Response::create()->with('Music', ['Jazz', 'Pop', 'Rock'])->safeJson($data);
        $content = $this->fromSafeJson($response);

        $this->assertEquals(
            '{"code":200,"status":"success","data":{"foo":"bar"},"Music":["Jazz","Pop","Rock"]}',
            $this->fromSafeJson($response, true)
        );
        $this->assertArrayHasKey('code', $content);
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('foo', $content['data']);
        $this->assertArrayHasKey('Music', $content);
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

    public function testResponseJSONError()
    {
        $response = Response::create()->jsonError('No status code defined');
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('No status code defined', $data['data']['errorDescription']);

        $response = Response::create()->jsonError('Not Found', 404);
        $this->assertEquals(404, $response->getStatusCode());

        $response = Response::create()->jsonError('Generic error code defined', 4001)->setStatusCode(401);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals(
            '{"code":200,"status":"success","data":{"errorCode":4001,"errorDescription":"Generic error code defined"}}',
            $response->getContent()
        );
    }

    public function testResponseSafeJSONError()
    {
        $response = Response::create()->safeJsonError('No status code defined');
        $data = $this->fromSafeJson($response);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('No status code defined', $data['data']['errorDescription']);

        $response = Response::create()->safeJsonError('Not Found', 404);
        $this->assertEquals(404, $response->getStatusCode());

        $response = Response::create()->safeJsonError('Generic error code defined', 4001)->setStatusCode(401);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertAngularJsonResponse($response->getContent());
    }

    public function testStaticCalls()
    {
        $data = ['foo' => 'bar'];
        $response = Response::json($data);
        $content = json_decode($response->getContent(), true);

        $this->assertInstanceOf('AdrianoRosa\HttpResponse\Response', $response);
        $this->assertEquals('{"code":200,"status":"success","data":{"foo":"bar"}}', $response->getContent());

        $this->assertArrayHasKey('code', $content);
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('foo', $content['data']);

        // From safeJson
        $data = ['foo' => 'bar'];
        $response = Response::sjson($data);
        $content = $this->fromSafeJson($response);

        $this->assertInstanceOf('AdrianoRosa\HttpResponse\Response', $response);
        $this->assertAngularJsonResponse($response);

        $this->assertArrayHasKey('code', $content);
        $this->assertArrayHasKey('status', $content);
        $this->assertArrayHasKey('data', $content);
        $this->assertArrayHasKey('foo', $content['data']);

        // Error
        $response = Response::error('error description');
        $content = json_decode($response->getContent(), true);
        $this->assertEquals('error description', $content['data']['errorDescription']);
        $this->assertEquals(200, $response->getStatusCode());

        $response = Response::error('error description')->setStatusCode(405);
        $this->assertEquals(405, $response->getStatusCode());

        // safeError
        $response = Response::serror('error description');
        $content = $this->fromSafeJson($response);
        $this->assertEquals('error description', $content['data']['errorDescription']);
        $this->assertEquals(200, $response->getStatusCode());

        $response = Response::serror('error description')->setStatusCode(405);
        $this->assertEquals(405, $response->getStatusCode());
    }
}
