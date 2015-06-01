<?php

namespace VMFDS\Cutter\Controllers;

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

class UiController extends AbstractController
{

    function __construct()
    {
        parent::__construct();
        $this->setDefaultAction('index');
    }

    /**
     * Central UI action
     * @action index
     * @return void
     */
    function indexAction()
    {
        \VMFDS\Cutter\Core\Logger::getLogger()->addDebug('indexAction called');
        $session = \VMFDS\Cutter\Core\Session::getInstance();

        // redirect to upload, if we don't have a file yet
        if (!$session->hasArgument('workFile')) {
            \VMFDS\Cutter\Core\Logger::getLogger()->addDebug('No workFile in session, redirecting to upload');
            \VMFDS\Cutter\Core\Router::getInstance()->redirect(
                'acquisition', 'form');
        }

        $this->view->assign('image',
            CUTTER_baseUrl.'Temp/Uploads/'.$session->getArgument('workFile'));
        $this->view->assign('legal', $session->getArgument('legal'));
    }

    function debugAction()
    {
        \VMFDS\Cutter\Core\Logger::getLogger()->addDebug('debugAction called');
        die('<pre>'.print_r($_REQUEST, 1));
    }
}