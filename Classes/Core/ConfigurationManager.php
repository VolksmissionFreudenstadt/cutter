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

class ConfigurationManager
{
    static private $instance = NULL;
    protected $conf          = array();

    protected function __construct()
    {

    }

    final private function __clone()
    {

    }

    /**
     * Get an instance of the configuration manager
     * @return \VMFDS\Cutter\Core\ConfigurationManager Instance of configuration manager
     */
    static public function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Return a specific configuration set
     * @param \string $setTitle Key for the configuration set
     * @param \strung $folderTitle Subfolder for configuration
     * @return array Configuration set
     */
    public function getConfigurationSet($setTitle, $folderTitle = '')
    {
        $folderTitle = $folderTitle ? ucfirst($folderTitle).'/' : '';
        if (!isset($this->conf[$setTitle])) {
            $this->conf[$setTitle] = yaml_parse_file(CUTTER_basePath.'/Configuration/'.$folderTitle.ucfirst($setTitle).'.yaml');
        }
        return $this->conf[$setTitle];
    }
}