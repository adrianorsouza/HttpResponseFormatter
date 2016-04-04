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

        $status = ($this->statusCode >= 100 && $this->statusCode <= 308) ? 'success' : 'error';

        if ( is_scalar($data) ) {
            $data = (array) $data;
        }

        $content = $this->morphToJson(
            (new ResponseFormatter($data, $this->statusCode, $status))
                ->getResponse()
        );

        $safeResponse = ")]}',\n" . $content;

        $default = [
            'Content-Type' => 'application/json'
        ];

        return $this->setContent($safeResponse)->withHeaders(array_merge($default, $headers));
    }

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
}
