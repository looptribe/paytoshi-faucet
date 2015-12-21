<?php

namespace Looptribe\Paytoshi\Api;

use Buzz\Message\Response;

abstract class ApiResponse
{
    /** @var Response */
    private $response;

    /** @var array */
    private $content;

    private $error = null;

    private $errorCode = null;

    public function __construct(Response $response)
    {
        $this->response = $response;
        $this->content = json_decode($response->getContent(), true);
        $this->parseContent($response, $this->content);
    }

    protected function parseContent(Response $response, $content)
    {
        if (!$response->isSuccessful()) {
            if (isset($content['code'])) {
                $this->errorCode = $content['code'];
                switch ($content['code']) {
                    case 'NOT_ENOUGH_FUNDS':
                        $this->error = 'Insufficient funds.';
                        break;
                    case 'INVALID_ADDRESS':
                        $this->error = 'Invalid address.';
                        break;
                    case 'FAUCET_DISABLED':
                        $this->error = 'This faucet has been disabled by the owner or the Paytoshi staff.';
                        break;
                    case 'ACCESS_DENIED':
                        $this->error = 'Access denied, please check your apikey.';
                        break;
                    case 'INTERNAL_ERROR':
                        $this->error = 'An internal server error has occurred, try again later.';
                        break;
                    case 'BAD_REQUEST':
                        $this->error = 'Bad request.';
                        break;
                    default:
                        $this->error =  sprintf("Unknown error code: %s.", $content['code']);
                        break;
                }
            }
            else {
                $this->error = 'Unknown error.';
                $this->errorCode = 'UNKNOWN';
            }
        }
    }

    /**
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->response->isSuccessful();
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string|null
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }
}
