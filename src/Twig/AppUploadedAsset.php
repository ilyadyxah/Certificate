<?php

namespace App\Twig;

use Symfony\Component\Asset\Packages;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\RuntimeExtensionInterface;

class AppUploadedAsset implements RuntimeExtensionInterface
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var Packages
     */
    private $packages;

    public function __construct(ParameterBagInterface $parameterBag, Packages $packages)
    {
        $this->parameterBag = $parameterBag;
        $this->packages = $packages;
    }

    public function asset(string $config, string $path)
    {
        return $this->packages->getUrl($this->parameterBag->get($config) . '/' . $path);
    }
}