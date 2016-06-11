<?php
/**
 * @package        Arastta eCommerce
 * @copyright      Copyright (C) 2015-2016 Arastta Association. All rights reserved. (arastta.org)
 * @credits        See CREDITS.txt for credits and other copyright notices.
 * @license        GNU General Public License version 3; see LICENSE.txt
 */

final class Front {
    
    private $registry;
    private $pre_action = array();
    private $error;

    public function __construct($registry) {
        $this->registry = $registry;
    }

    public function addPreAction($pre_action) {
        $this->pre_action[] = $pre_action;
    }

    public function dispatch($action, $error) {
        $this->error = $error;

        foreach ($this->pre_action as $pre_action) {
            $result = $this->execute($pre_action);

            if ($result) {
                $action = $result;

                break;
            }
        }

        while ($action) {
            $action = $this->execute($action);
        }
    }

    private function execute($action) {
        $result = $action->execute($this->registry);

        if (is_object($result)) {
            $action = $result;
        } elseif ($result === false) {
            $action = $this->error;

            $this->error = '';
        } else {
            $action = false;
        }

        return $action;
    }
}
