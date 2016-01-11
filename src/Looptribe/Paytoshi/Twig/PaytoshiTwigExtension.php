<?php

namespace Looptribe\Paytoshi\Twig;

use Symfony\Component\HttpFoundation\Request;

class PaytoshiTwigExtension extends \Twig_Extension
{
    /** @var Request */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'paytoshi';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            'asset' => new \Twig_SimpleFunction('asset', array($this, 'assetFunction')),
        );
    }

    public function assetFunction($asset)
    {
        return $this->request->getBasePath() . '/' . ltrim($asset, '/');
    }
}
