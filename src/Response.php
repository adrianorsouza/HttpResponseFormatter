<?php

namespace AdrianoRosa\HttpResponse;

use \Illuminate\Http\Response as BaseResponse;

/**
 * Class Response
 *
 * @author  Adriano Rosa <http://adrianorosa.com>
 * @date    02/04/16 14:51
 *
 * @package Http
 */
class Response extends BaseResponse
{
    /**
     * @var array
     */
    protected $append = [];

    /**
     * Factory method for chainability.
     *
     * Example:
     *
     *     return Response::create($body, 200)
     *         ->setSharedMaxAge(300);
     *
     * @param mixed $content The response content, see setContent()
     * @param int   $status  The response status code
     * @param array $headers An array of response headers
     *
     * @return Response
     */
    public static function create($content = '', $status = 200, $headers = array())
    {
        return new static($content, $status, $headers);
    }

    /**
     * Appends data to the response formatter.
     *
     * @param $key
     * @param $value
     *
     * @return $this
     */
    public function with($key, $value)
    {
        $this->append[$key] = $value;

        return $this;
    }

    /**
     * Format a safe JSON Response.
     *
     * @param array $data
     * @param int $code
     * @param array $headers
     *
     * @return $this
     */
    public function toJson($data = [], $code = null, $headers = [])
    {
        if ( ! is_null($code) ) {
            $this->setStatusCode($code);
        }

        if ( count($data) === 0 ) {
            $data = $this->content;
        }

        if ( $data instanceof \Illuminate\Support\Collection
            || $data instanceof \Illuminate\Database\Eloquent\Model
        ) {
            $data = $data->toArray();
        }

        if ( is_scalar($data) ) {
            $data = empty($data) ? [] : (array) $data;
        }

        $statusText = ($this->statusCode >= 100 && $this->statusCode <= 308) ? 'success' : $this->statusText;

        $content = (new ResponseFormatter($data, $this->statusCode, $statusText));

        $default = [
            'Content-Type' => 'application/json'
        ];

        return $this->setContent($content->getResponse())->withHeaders(array_merge($default, $headers));;
    }

    /**
     * Format a safe JSON Response.
     *
     * @param array $data
     * @param int $code
     * @param array $headers
     *
     * @return $this
     */
    public function safeJson($data = [], $code = null, $headers = [])
    {
        if ( ! is_null($code) ) {
            $this->setStatusCode($code);
        }

        if ( count($data) === 0 ) {
            $data = $this->content;
        }

        if ( $data instanceof \Illuminate\Support\Collection
            || $data instanceof \Illuminate\Database\Eloquent\Model
        ) {
            $data = $data->toArray();
        }

        $status = ($this->statusCode >= 100 && $this->statusCode <= 308) ? 'success' : $this->statusText;

        if ( is_scalar($data) ) {
            $data = empty($data) ? [] : (array) $data;
        }

        $formatter = (new ResponseFormatter($data, $this->statusCode, $status));

        // append other data to response formatter
        if ( count($this->append) > 0 ) {
            $formatter->add($this->append);
        }

        $content = $this->morphToJson($formatter->getResponse());

        $safeResponse = ")]}',\n" . $content;

        $default = [
            'Content-Type' => 'application/json'
        ];

        return $this->setContent($safeResponse)->withHeaders(array_merge($default, $headers));
    }

    /**
     * @param $statusCode
     * @param int $errorCode
     * @param string $errorDescription
     *
     * @param array $headers
     *
     * @return \AdrianoRosa\HttpResponse\Response
     */
    public function safeJsonError($errorCode = 0, $errorDescription = '', $statusCode = null, $headers = [])
    {
        $statusCode = $statusCode ?: $errorCode;

        return $this->safeJson([
            'errorCode' => $errorCode,
            'errorDescription' => $errorDescription,
        ], $statusCode, $headers);
    }

    {
        return new static($content, $status, $headers);
    }
}
