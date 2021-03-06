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

        $imgInfo = getimagesize(CUTTER_baseUrl.'Temp/Uploads/'.$session->getArgument('workFile'));
        $this->view->assign('width', $imgInfo[0]);
        $this->view->assign('height', $imgInfo[1]);

        $this->view->assign('legal', $session->getArgument('legal'));

        $info = \VMFDS\Cutter\Factories\TemplateFactory::getTemplateInfo();
        $this->view->assign('templates', $info);

        $templateKeys  = array_keys($info);
        $firstGroup    = array_keys($info[$templateKeys[0]]);
        $firstTemplate = $firstGroup[0];
        $this->view->assign('firstTemplate', $firstTemplate);
    }

    function debugAction()
    {
        \VMFDS\Cutter\Core\Logger::getLogger()->addDebug('debugAction called');
        die('<pre>'.print_r($_REQUEST, 1));
    }

    /**
     * Download script
     */
    public function downloadAction()
    {
        $this->dontShowView();
        $request = \VMFDS\Cutter\Core\Request::getInstance();
        if ($request->hasArgument('url')) {
            $url = $request->getArgument('url');
            $raw = CUTTER_basePath.'Temp/Processed/'.basename(parse_url($url,
                        PHP_URL_PATH));
            Header('Content-Description: File Transfer');
            Header('Content-Disposition: attachment; filename='.sprintf('"%s"',
                    addcslashes(basename($raw), '"')));
            header('Content-Transfer-Encoding: binary');
            header('Content-Type: application/octet-stream');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: '.filesize($raw));
            readfile($raw);
            die();
        }
    }
}