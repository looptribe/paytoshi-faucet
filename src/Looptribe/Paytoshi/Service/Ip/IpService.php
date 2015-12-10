<?php

/**
 * Paytoshi Faucet Script
 *
 * Contact: info@paytoshi.org
 *
 * @author: Looptribe
 * @link: https://paytoshi.org
 * @package: Looptribe\Paytoshi
 */

namespace Looptribe\Paytoshi\Service\Ip;

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

    /** @var IpValidatorService  */
    protected $ipValidatorService;
    /** @var IpMatcherService  */
    protected $ipMatcherService;

    /**
     * Constructor
     *
     * @param IpValidatorService $ipValidatorService
     * @param IpMatcherService $ipMatcherService
     * @param bool $checkProxyHeaders Whether to use proxy headers to determine client IP
     * @param array $trustedProxies List of IP addresses of trusted proxies
     * @param array $headersToInspect List of headers to inspect
     */
    public function __construct(
        IpValidatorService $ipValidatorService,
        IpMatcherService $ipMatcherService,
        $checkProxyHeaders = false,
        array $trustedProxies = array(),
        array $headersToInspect = array()
    ) {
        $this->checkProxyHeaders = $checkProxyHeaders;
        $this->trustedProxies = $trustedProxies;
        if (!empty($headersToInspect)) {
            $this->headersToInspect = $headersToInspect;
        }
        $this->ipValidatorService = $ipValidatorService;
        $this->ipMatcherService = $ipMatcherService;
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
        if (isset($serverParams['REMOTE_ADDR']) && $this->ipValidatorService->validate($serverParams['REMOTE_ADDR'])) {
            $ipAddress = $serverParams['REMOTE_ADDR'];
        }
        $checkProxyHeaders = $this->checkProxyHeaders;
        if ($checkProxyHeaders && !empty($this->trustedProxies)) {
            $existsProxy = false;
            foreach($this->trustedProxies as $proxy) {
                if ($this->ipMatcherService->match($ipAddress, $proxy)) {
                    $existsProxy = true;
                    break;
                }
            }
            if (!$existsProxy) {
                $checkProxyHeaders = false;
            }
        }
        if ($checkProxyHeaders) {
            foreach ($this->headersToInspect as $header) {
                if (array_key_exists($header, $serverParams)) {
                    $ip = trim(current(explode(',', $serverParams[$header])));
                    if ($this->ipValidatorService->validate($ip)) {
                        $ipAddress = $ip;
                        break;
                    }
                }
            }
        }

        return $ipAddress;
    }

}