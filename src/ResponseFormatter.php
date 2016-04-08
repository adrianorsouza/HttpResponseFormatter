<?php

namespace AdrianoRosa\HttpResponse;

class ResponseFormatter
{

    /**
     * @type array
     */
    protected $response = [
        'code' => 404,
        'status' => 'Not Found',
        'data' => [],
    ];

    /**
     * @param array $data
     * @param int $code
     * @param string $status
     */
    public function __construct(array $data = [], $code = 200, $status = 'success')
    {
        $this->setFormatResponse($data, $code, $status);
    }

    /**
     * @param        $data
     * @param int    $code
     * @param string $status
     *
     * @return $this
     */
    public function setFormatResponse(array $data = [], $code = 200, $status = 'success')
    {
        $this->response['code']   = $code;
        $this->response['status'] = $status;
        $this->response['data']   = $data;

        return $this;
    }

    public function add($key, $value = null)
    {
        return $this->setAppendToResponse($key, $value);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @deprecated in favor of ResponseFormatter::add()
     * @return $this
     */
    public function setAppendToResponse($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->setAppendToResponse($k, $v);
            }
            return $this;
        }

        $this->response[$key] = $value;

        return $this;
    }

    /**
     * Get the response data formatted
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get the response data formatted
     *
     * @deprecated
     * @return array
     */
    public function getResponseData()
    {
        return $this->response['data'];
    }

    /**
     * @deprecated
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->response['code'];
    }

    /**
     * @deprecated
     * @return mixed
     */
    public function getStatus()
    {
        return $this->response['status'];
    }
}
