<?php
namespace App\Core;

use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Twig view extension
 */
class ViewExtension extends Twig_Extension
{
    /**
     * @var string
     */
    protected $publicDirectory;
    /**
     * base url
     * @var string
     */
    protected $baseUrl;

    /**
     * Extention constructor
     * @param string $baseUrl
     */
    public function __construct($publicDirectory, $baseUrl)
    {
        $this->publicDirectory = $publicDirectory;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Callback for Twig
     * @ignore
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('cached_url', [$this, 'cachedUrl']),
        ];
    }

    /**
     * Return url to file with random cache hash in it
     * @param  string $fileNamePart 
     * @return string
     */
    public function cachedUrl($fileNamePart)
    {
        $fileNamePart = ltrim($fileNamePart, '/');
        $path = $this->publicDirectory.DIRECTORY_SEPARATOR.$fileNamePart.'*';
        $files = glob($path);

        if (isset($files[0])) {
            return str_replace($this->publicDirectory, $this->baseUrl, $files[0]);
        }

        return $this->baseUrl.'/'.$fileNamePart;
    }
}