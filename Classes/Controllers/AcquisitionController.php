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

class AcquisitionController extends AbstractController
{

    function __construct()
    {
        parent::__construct();
        $this->setDefaultAction('form');
    }

    /**
     * Display the upload form
     * @action form
     * @return void
     */
    function formAction()
    {
        \VMFDS\Cutter\Core\Logger::getLogger()->addDebug('uploadAction called');

        \VMFDS\Cutter\Core\Logger::getLogger()->addDebug('Clearing session content');
        \VMFDS\Cutter\Core\Session::getInstance()->clear();

        // get list of possible providers
        $providers = \VMFDS\Cutter\Factories\ProviderFactory::getProviderNames();
        $this->view->assign('providers', $providers);
    }

    /**
     * Import action
     * @action import
     * @return void
     */
    function importAction()
    {
        \VMFDS\Cutter\Core\Logger::getLogger()->addDebug('importAction called');
        $request = \VMFDS\Cutter\Core\Request::getInstance();
        if (!$request->hasArgument('url')) {
            \VMFDS\Cutter\Core\Logger::getLogger()->addDebug('No url specified, redirecting to upload');
            $this->redirectToAction('upload');
        }
        $url      = $request->getArgument('url');
        \VMFDS\Cutter\Core\Logger::getLogger()->addNotice('Starting cloud import from url '.$url);
        $provider = \VMFDS\Cutter\Factories\ProviderFactory::getHostHandler($url);
        \VMFDS\Cutter\Core\Logger::getLogger()->addDebug('Using provider class '.get_class($provider));

        // render the view prematurely (waiting ...)
        $this->renderView();

        $provider->retrieveImage($url);
        print_r($provider);

        // save data in session and redirect to index
        \VMFDS\Cutter\Core\Logger::getLogger()->addDebug('Import done, saving to session.');
        $session = \VMFDS\Cutter\Core\Session::getInstance();
        $session->setArgument('workFile', $provider->workFile);
        $session->setArgument('legal', $provider->legal);

        \VMFDS\Cutter\Core\Logger::getLogger()->addNotice('Cloud import processed with Provider "'.$provider->getName().'".');
        \VMFDS\Cutter\Core\Logger::getLogger()->addNotice('File received: '.CUTTER_uploadPath.$workFile);
        \VMFDS\Cutter\Core\Logger::getLogger()->addNotice('Legal text preset: '.$legal);
        $this->redirectToAction('index', self::REDIRECT_JAVASCRIPT, 3000);
    }

    /**
     * Receive action
     * Gets called to process an uploaded file
     * @action receive
     */
    function receiveAction()
    {
        \VMFDS\Cutter\Core\Logger::getLogger()->addDebug('receiveAction called.');
        $request = \VMFDS\Cutter\Core\Request::getInstance();
        if (!$request->hasFilesArray()) {
            $this->redirectToAction('upload');
        }
        $filesArray = $request->getFilesArray();
        $fileName   = $filesArray['file']['name'];
        $legal      = '';
        if ($request->hasArgument('legal')) {
            $legal = $request->getArgument('legal');
            $fileName .= '_'.str_replace(' / ', '_', $legal);
        }
        $fileName = strtr($fileName,
            array(' ' => '_', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue',
            'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ß' => 'ss'));
        $dest     = CUTTER_uploadPath.$fileName;
        move_uploaded_file($filesArray['file']['tmp_name'], $dest);

        // save info in session
        $session = \VMFDS\Cutter\Core\Session::getInstance();
        $session->setArgument('workFile', $fileName);
        $session->setArgument('legal', $legal);

        // this is an Ajax'y action with no return, so don't show a view
        $this->dontShowView();
    }
}