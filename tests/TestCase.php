<?php

use PHPUnit_Framework_Assert as PHPUnit;

class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Validate AngularJS Security JSON response
     *
     * @param mixed $response
     *
     * @return void
     */
    public function assertAngularJsonResponse($response = null)
    {
        if ($response instanceof \Illuminate\Http\Response) {
            $response = $response->getContent();
        }

        return PHPUnit::assertRegExp("/^\)\]\}\'\,\n/", $response, 'Invalid Angular json secure response');
    }

    /**
     * Converts the safeJson like response into an array.
     *
     * @param \Illuminate\Http\Response $response
     * @param bool|false $as_string
     *
     * @return \Illuminate\Http\Response|mixed
     */
    protected function fromSafeJson($response = null, $as_string = false)
    {
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            return $response;
        }

        $json = str_replace(")]}',\n", '', $response->getContent());
        return $as_string ? $json : json_decode($json, true);
    }
}
