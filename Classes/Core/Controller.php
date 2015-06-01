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

class Controller extends AbstractController
{

    function __construct()
    {
        parent::__construct();
        $this->setDefaultAction('index');
    }

    /**
     * Upload action
     * @action upload
     * @return void
     */
    function uploadAction()
    {
        // get list of possible providers
        $providers = \VMFDS\Cutter\Factories\ProviderFactory::getProviderNames();

        $this->view->assign('providers', $providers);
    }

    /**
     * Index action
     * @action index
     * @return void
     */
    function indexAction()
    {
        $session = \VMFDS\Cutter\Core\Session::getInstance();

        // redirect to upload, if we don't have a file yet
        if (!$session->hasArgument('workFile')) {
            $this->redirectToAction('upload');
        }
        echo '<pre>';
        print_r($_SESSION);
        die('This is the index action.');
    }

    function debugAction()
    {
        die('<pre>'.print_r($_REQUEST, 1));
    }

    /**
     * Import action
     * @action import
     * @return void
     */
    function importAction()
    {
        $request = \VMFDS\Cutter\Core\Request::getInstance();
        if (!$request->hasArgument('url')) {
            $this->redirectToAction('upload');
        }
        $url      = $request->getArgument('url');
        $provider = \VMFDS\Cutter\Factories\ProviderFactory::getHostHandler($url);

        // render the view prematurely (waiting ...)
        $this->renderView();

        $provider->retrieveImage($url);
        print_r($provider);

        // save data in session and redirect to index
        $session = \VMFDS\Cutter\Core\Session::getInstance();
        $session->setArgument('workFile', $provider->workFile);
        $session->setArgument('legal', $provider->legal);

        $this->redirectToAction('index', self::REDIRECT_JAVASCRIPT, 3000);
    }

    /**
     * Receive action
     * Gets called to process an uploaded file
     * @action receive
     */
    function receiveAction()
    {
        $request = \VMFDS\Cutter\Core\Request::getInstance();
        if (!$request->hasFilesArray()) {
            $this->redirectToAction('upload');
        }
        $filesArray = $request->getFilesArray();
        $fileName   = $filesArray['file']['name'];
        if ($request->hasArgument('legal'))
                $fileName .= '_'.str_replace(' / ', '_',
                    $request->getArgument('legal'));
        $fileName   = strtr($fileName,
            array(' ' => '_', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue',
            'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ß' => 'ss'));
        $dest       = CUTTER_uploadPath.$fileName;
        move_uploaded_file($filesArray['file']['tmp_name'], $dest);

        // save info in session
        $session = \VMFDS\Cutter\Core\Session::getInstance();
        $session->setArgument('workFile', $fileName);
        $session->setArgument('legal', $legal);

        // this is an Ajax'y action with no return, so don't show a view
        $this->dontShowView();
    }
}