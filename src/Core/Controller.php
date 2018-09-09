<?php
namespace App\Core;

use Exception;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Http\Stream;

/**
 * Abstract controller class
 */
abstract class Controller
{
    /**
     * @var ContainerInterface
     */
    protected $ci;

    /**
     * Controller constructor
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;

        try {
            $this->init();
        }
        catch (Exception $ex) {
            // Dirty way to stop execution on any issues
            // especialy if dataset doesn't exist
            $this->forcedResponse(['error' => $ex->getMessage()], 422);
        }
    }

    /**
     * Init controller
     */
    protected function init()
    {
        // do nothing, you can override it
    }

    /**
     * Render template
     * @param  string $template
     * @param  array  $data
     * @return ResponseInterface
     */
    protected function render($template, $data = [])
    {
        return $this->ci['view']->render($this->ci['response'], $template, $data);
    }

    /**
     * Return json response
     * @param  array   $data
     * @param  integer $status
     * @return ResponseInterface
     */
    protected function json($data = [], $status = 200)
    {
        return $this->ci['response']->withJson($data, $status);
    }

    /**
     * Redirect to route
     * @param  string $route
     * @param  array $params
     * @return ResponseInterface
     */
    protected function redirect($route, $params = [])
    {
        return $this->ci['response']
            ->withRedirect($this->ci['router']->pathFor($route, $params));
    }

    /**
     * Redirect back
     * @return ResponseInterface
     */
    protected function redirectBack()
    {
        return $this->ci['response']
            ->withRedirect($this->ci['request']->getUri()->__toString());
    }
}