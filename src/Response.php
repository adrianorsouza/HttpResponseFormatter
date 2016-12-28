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
 *
 * @method static|Response json($data = [], $code = null, $headers = [])
 * @method static|Response sjson($data = [], $code = null, $headers = [])
 * @method static|Response error($errorDescription = '', $errorCode = null, $headers = [])
 * @method static|Response serror($errorDescription = '', $errorCode = null, $headers = [])
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
     * @param string $key
     * @param mixed $value
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

        $formatter = (new ResponseFormatter($data, $this->statusCode, $statusText));

        // append other data to response formatter
        if ( count($this->append) > 0 ) {
            $formatter->add($this->append);
        }

        $default = [
            'Content-Type' => 'application/json'
        ];

        $content = $this->morphToJson($formatter->getResponse());

        $this->setContent($content)->withHeaders(array_merge($default, $headers));

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
    public function safeJson($data = [], $code = null, $headers = [])
    {
        $this->toJson($data, $code, $headers);

        $safeJsonResponse = ")]}',\n" . $this->content;

        $this->setContent($safeJsonResponse);

        return $this;
    }

    /**
     * Return the json error formatted response.
     *
     * @param string $errorDescription
     * @param int $errorCode
     * @param array $headers
     *
     * @return \AdrianoRosa\HttpResponse\Response
     */
    public function jsonError($errorDescription = '', $errorCode = null, $headers = [])
    {
        $statusCode = null;

        if ( array_key_exists($errorCode, self::$statusTexts) ) {
            $statusCode = $errorCode;
        }

        return $this->toJson([
            'errorCode' => $errorCode,
            'errorDescription' => $errorDescription,
        ], $statusCode, $headers);
    }

    /**
     * Return the safe json error formatted response.
     *
     * @param string $errorDescription
     * @param int $errorCode
     * @param array $headers
     *
     * @return \AdrianoRosa\HttpResponse\Response
     */
    public function safeJsonError($errorDescription = '', $errorCode = null, $headers = [])
    {
        $this->jsonError($errorDescription, $errorCode, $headers);

        $this->setContent(")]}',\n" . $this->content);

        return $this;
    }

    /**
     * Magic method to allow easy chaining.
     *
     * @param $method
     * @param $args
     *
     * @return static[]
     */
    public static function __callStatic($method, $args)
    {
        switch ($method) {
            case 'json':
                $function = 'toJson';
                break;

            case 'sjson':
                $function = 'safeJson';
                break;

            case 'error':
                $function = 'jsonError';
                break;

            case 'serror':
                $function = 'safeJsonError';
                break;

            default:
                break;
        }

        if ( isset($function) ) {
            return call_user_func_array([static::create(), $function], $args);
        }

        throw new \BadMethodCallException('Call to undefined method ' . static::class . '::' . $method . '()');
    }
}
