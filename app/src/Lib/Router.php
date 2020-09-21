<?php
declare(strict_types=1);

namespace Harpya\IP\Lib;

class Router extends \AltoRouter
{
    protected $request;
    protected $di;

    public function __construct($di = null)
    {
        $this->request = new \Phalcon\Http\Request();
        if ($di) {
            $this->di = $di;
        }
    }

    public function loadFromFolder($path)
    {
        $ls = glob($path . '/*.php');
        $router = $this;
        foreach ($ls as $filename) {
            if (!\is_dir($filename)) {
                include_once $filename;
            }
        }
    }

    protected function add($method, $rule, $closure)
    {
        $di = $this->di;
        $request = $this->request;
        $this->map($method, $rule, function (...$parms) use ($request,$di, $closure) {
            return $closure($request, $parms, $di);
        });
    }

    public function addGet($rule, $closure)
    {
        return $this->add('get', $rule, $closure);
    }

    public function addPost($rule, $closure)
    {
        return $this->add('post', $rule, $closure);
    }

    public function addPut($rule, $closure)
    {
        return $this->add('put', $rule, $closure);
    }

    public function addPatch($rule, $closure)
    {
        return $this->add('patch', $rule, $closure);
    }

    public function addDelete()
    {
        return $this->add('delete', $rule, $closure);
    }
}
