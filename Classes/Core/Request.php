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
     * @return \VMFDS\Cutter\Utility\Request Instance of session object
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
        return isset($this->data[$argument]);
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
}