<?php

namespace VMFDS\Cutter\Core;

/*
 * CUTTER
 * Versatile Image Cutter and Processor
 * http://github.com/VolksmissionFreudenstadt/cutter
 *
 * Copyright (c) 2015 Volksmission Freudenstadt, http://www.volksmission-freudenstadt.de
 * Author: Christoph Fischer, chris@toph.de
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class Request
{
    static $instance = NULL;
    protected $data  = array();

    /**
     * Get an instance of the request object
     * @return \VMFDS\Cutter\Core\Request Instance of session object
     */
    static public function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function __construct()
    {
        $this->data = $_REQUEST;
    }

    final private function __clone()
    {

    }

    /**
     * Return a request variable
     * @param string $varName Variable name
     * @return variant Value
     */
    static public function GPVar($varName)
    {
        return $_REQUEST[$varName];
    }

    /**
     * Checks if a specific argument is present in the request
     * @param \string $argument Argument name
     * @return \bool True if argument exists
     */
    public function hasArgument($argument)
    {
        return (isset($this->data[$argument]) && ($this->data[$argument] != ''));
    }

    /**
     * Get a specific argument from the request
     * @param \string $argument Argument name
     * @param variant Argument value or FALSE if argument not present
     */
    public function getArgument($argument)
    {
        return ($this->hasArgument($argument) ? $this->data[$argument] : false);
    }

    /**
     * Get all request arguments
     * @return array Arguments
     */
    public function getArguments()
    {
        return $this->data;
    }

    /**
     * Checks if $_FILES array is present
     * @return bool True if files array is present
     */
    public function hasFilesArray()
    {
        return is_array($_FILES);
    }

    /**
     * Returns files array with information about uploaded files
     * @return array File upload information
     */
    public function getFilesArray()
    {
        return $_FILES;
    }

    /**
     * Parse request data from nice URL
     */
    public function parseUri()
    {
        $pattern            = 'controller|action';
        $uri                = $_SERVER['REQUEST_URI'];
        $this->data['_ext'] = pathinfo($uri, PATHINFO_EXTENSION);
        $uri                = str_replace('.'.$this->data['_ext'], '', $uri);
        $uri                = str_replace(parse_url(CUTTER_baseUrl, PHP_URL_PATH),
            '', $uri);
        $uri                = parse_url($uri, PHP_URL_PATH);
        \VMFDS\Cutter\Core\Logger::getLogger()->addDebug('Parsing URI '.$uri);
        if ($uri != '') {
            $this->data['_raw'] = explode('/', $uri);
        } else {
            $this->data['_raw'] = array();
        }
        \VMFDS\Cutter\Core\Logger::getLogger()->addDebug('URI parsed',
            $this->data);
    }

    /**
     * Get named parameters from request according to a uri pattern
     * @param array $pattern Array with names of uri sections
     */
    public function applyUriPattern($pattern)
    {
        $uriItems = $this->data['_raw'];
        foreach ($pattern as $key) {
            if (isset($uriItems[0])) {
                $this->data[$key] = $uriItems[0];
                unset($uriItems[0]);
            } else {
                $this->data[$key] = '';
            }
            $uriItems = array_values($uriItems);
        }
        $this->data['_raw'] = $uriItems;
    }

    /**
     * Get multiple arguments at once
     * @param array $args Argument keys
     * @return array Argument values
     */
    public function getArgumentsArray($args)
    {
        $data = array();
        foreach ($args as $arg) {
            if ($this->hasArgument($arg)) {
                $data[$arg] = $this->getArgument($arg);
            }
        }
        return $data;
    }

    public function requireArguments($args)
    {
        foreach ($args as $arg) {
            if (!$this->hasArgument($arg)) {
                \VMFDS\Cutter\Core\Logger::getLogger()->addDebug('FATAL: Missing argument \''.$arg.'\'');
                die('FATAL: Missing argument \''.$arg.'\'');
            }
        }
    }
}