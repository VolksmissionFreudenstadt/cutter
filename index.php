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

require_once('vendor/autoload.php');

// define some constants
define('CUTTER_debug', true);
define('CUTTER_basePath', __DIR__.'/');
define('CUTTER_uploadPath', CUTTER_basePath.'Temp/Uploads/');
define('CUTTER_baseUrl',
    (($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http')
    .'://'.$_SERVER['SERVER_NAME'].dirname($_SERVER["REQUEST_URI"].'?').'/');

// error handling stuff:
if (CUTTER_debug) {
    ini_set('display_errors', 1);
}

// start session handling
VMFDS\Cutter\Core\Session::initialize();

// get a controller and run it
$controller = new VMFDS\Cutter\Core\Controller();
$controller->dispatch();

