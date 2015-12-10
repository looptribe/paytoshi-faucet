<?php
/**
 * Created by PhpStorm.
 * User: Diego
 * Date: 10/12/2015
 * Time: 10.08
 */

namespace Looptribe\Paytoshi\Service;


class IpService
{

    /**
     * Enable checking of proxy headers (X-Forwarded-For to determined client IP.
     *
     * Defaults to false as only $_SERVER['REMOTE_ADDR'] is a trustworthy source
     * of IP address.
     *
     * @var bool
     */
    protected $checkProxyHeaders;
    /**
     * List of trusted proxy IP addresses
     *
     * If not empty, then one of these IP addresses must be in $_SERVER['REMOTE_ADDR']
     * in order for the proxy headers to be looked at.
     *
     * @var array
     */
    protected $trustedProxies;

    /**
     * List of proxy headers inspected for the client IP address
     *
     * @var array
     */
    protected $headersToInspect = array(
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR'
    );

    /**
     * Constructor
     *
     * @param bool $checkProxyHeaders Whether to use proxy headers to determine client IP
     * @param array $trustedProxies List of IP addresses of trusted proxies
     * @param array $headersToInspect List of headers to inspect
     */
    public function __construct(
        $checkProxyHeaders = false,
        array $trustedProxies = array(),
        array $headersToInspect = array()
    ) {
        $this->checkProxyHeaders = $checkProxyHeaders;
        $this->trustedProxies = $trustedProxies;
        if (!empty($headersToInspect)) {
            $this->headersToInspect = $headersToInspect;
        }
    }

    /**
     * Find out the client's IP address from the headers available to us
     *
     * @param array $serverParams
     * @return string
     */
    public function determineClientIpAddress($serverParams)
    {
        $ipAddress = null;
        if (isset($serverParams['REMOTE_ADDR']) && $this->isValidIpAddress($serverParams['REMOTE_ADDR'])) {
            $ipAddress = $serverParams['REMOTE_ADDR'];
        }
        $checkProxyHeaders = $this->checkProxyHeaders;
        if ($checkProxyHeaders && !empty($this->trustedProxies)) {
            if (!in_array($ipAddress, $this->trustedProxies)) {
                $checkProxyHeaders = false;
            }
        }
        if ($checkProxyHeaders) {
            foreach ($this->headersToInspect as $header) {
                if (array_key_exists($header, $serverParams)) {
                    $ip = trim(current(explode(',', $serverParams[$header])));
                    if ($this->isValidIpAddress($ip)) {
                        $ipAddress = $ip;
                        break;
                    }
                }
            }
        }

        return $ipAddress;
    }

    /**
     * Check that a given string is a valid IP address
     *
     * @param  string $ip
     * @return boolean
     */
    protected function isValidIpAddress($ip)
    {
        $flags = FILTER_FLAG_IPV4;# | FILTER_FLAG_IPV6;
        if (filter_var($ip, FILTER_VALIDATE_IP, $flags) === false) {
            return false;
        }

        return true;
    }
}