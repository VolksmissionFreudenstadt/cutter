<?php
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

namespace VMFDS\Cutter\Factories;

/**
 * Abstract factory class
 *
 * @author Christoph Fischer <chris@toph.de>
 */
class AbstractFactory
{

    static public function getAllClasses($type) {
        $type = ucfirst($type);
        $typeAbstract = 'Abstract'.$type;
        $typeMulti = $type.'s';
        $typeNamespace = '\\VMFDS\\Cutter\\'.$typeMulti;
        $typePath = CUTTER_basePath.'Classes/'.$typeMulti.'/';

        $classes = array();
        // find all matching classes
        $handle = opendir($typePath);
        while ($file = readdir($handle)) {
            if (substr($file, -(strlen($type)+4)) == $type.'.php') {
                if ($file != $typeAbstract.'.php') {
                    $thisType = substr($file, 0, -(strlen($type)+4));
                    $thisClass = $typeNamespace.'\\'.$thisType.$type;
                    $classes[$thisType] = $thisClass;
                }
            }
        }
        closedir($handle);
        return $classes;
    }
    
}