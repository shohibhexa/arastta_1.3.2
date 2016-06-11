<?php
/**
 * @package        Arastta eCommerce
 * @copyright      Copyright (C) 2015-2016 Arastta Association. All rights reserved. (arastta.org)
 * @credits        See CREDITS.txt for credits and other copyright notices.
 * @license        GNU General Public License version 3; see LICENSE.txt
 */

final class Action {
    
    private $file;
    private $class;
    private $method;
    private $args = array();

    public function __construct($route, $args = array()) {
        $path = '';

        // Break apart the route
        $parts = explode('/', str_replace('../', '', (string)$route));

        foreach ($parts as $part) {
            $path .= $part;

            if (is_dir(Client::getDir() . 'controller/' . $path)) {
                $path .= '/';

                array_shift($parts);

                continue;
            }

            $file = Client::getDir() . 'controller/' . str_replace(array('../', '..\\', '..'), '', $path) . '.php';

            if (is_file($file)) {
                $this->file = $file;

                $this->class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $path);

                array_shift($parts);

                break;
            }
        }

        if ($args) {
            $this->args = $args;
        }

        $method = array_shift($parts);

        if ($method) {
            $this->method = $method;
        } else {
            $this->method = 'index';
        }
    }

    public function execute($registry) {
        // Stop any magical methods being called
        if (substr($this->method, 0, 2) == '__') {
            return false;
        }

        if (is_file($this->file)) {
            include_once(modification($this->file));

            $class = $this->class;

            $controller = new $class($registry);

            if (is_callable(array($controller, $this->method))) {
                return call_user_func(array($controller, $this->method), $this->args);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
