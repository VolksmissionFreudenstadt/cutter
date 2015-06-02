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
        if (!isset($this->conf['_'.$folderTitle][$setTitle])) {
            $yamlFile = CUTTER_basePath.'/Configuration/'.$folderTitle.ucfirst($setTitle).'.yaml';
            if (file_exists($yamlFile)) {
                $this->conf['_'.$folderTitle][$setTitle] = yaml_parse_file($yamlFile);
            } else {
                $this->conf['_'.$folderTitle][$setTitle] = array();
            }
        }
        return $this->conf['_'.$folderTitle][$setTitle];
    }

    /**
     * Set default values from another array
     *
     * @param array lc Array to process
     * @param array c Array with default values
     * @return array New array with default values set
     */
    function setDefaults($lc, $c)
    {
        $lc = $this->arrayMergeRecursiveDistinct($c['defaults'], $lc);
        return $lc;
    }

    /**
     * Merge arrays
     *
     * arrayMergeRecursiveDistinct does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):
     *
     * arrayMergeRecursiveDistinct(array('key' => 'org value'), array('key' => 'new value'));
     * 	 => array('key' => array('org value', 'new value'));
     *
     * arrayMergeRecursiveDistinct does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * arrayMergeRecursiveDistinct(array('key' => 'org value'), array('key' => 'new value'));
     * 	 => array('key' => 'new value');
     *
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function.
     *
     * @param array $array1
     * @param mixed $array2
     * @author daniel@danielsmedegaardbuus.dk
     * @return array
     */
    protected function arrayMergeRecursiveDistinct($array1, $array2 = null)
    {
        $merged       = $array1;
        if (is_array($array2))
                foreach ($array2 as $key => $val)
                if (is_array($array2[$key]))
                        $merged[$key] = is_array($merged[$key]) ? $this->arrayMergeRecursiveDistinct($merged[$key],
                            $array2[$key]) : $array2[$key];
                else $merged[$key] = $val;
        return $merged;
    }
}