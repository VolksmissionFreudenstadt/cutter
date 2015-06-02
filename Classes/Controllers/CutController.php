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

namespace VMFDS\Cutter\Controllers;

/**
 * Description of CutController
 *
 * @author chris
 */
class CutController extends AbstractController
{
    protected $data = array();

    /**
     * Override default renderView() function
     *
     * This controller will behave as a JSON controller, which means:
     * (1) default view output will NOT happen
     * (2) Content-Type will be set to application/json
     * (3) an internal array $data will be output as JSON
     */
    public function renderView()
    {
        $this->view->setContentType('application/json');
        $this->view->sendContentTypeHeader();
        echo json_encode($this->data);
    }

    /**
     * Cut the image
     * @action cut
     */
    function doAction()
    {
        $session = \VMFDS\Cutter\Core\Session::getInstance();
        $request = \VMFDS\Cutter\Core\Request::getInstance();
        // we just die, since this is a headless controller
        if (!$session->hasArgument('workFile')) die('workfile');
        if (!$request->hasArgument('x')) die('x');
        if (!$request->hasArgument('y')) die('y');
        if (!$request->hasArgument('w')) die('w');
        if (!$request->hasArgument('h')) die('h');
        if (!$request->hasArgument('template')) die('template');

        $template  = \VMFDS\Cutter\Factories\TemplateFactory::get($request->getArgument('template'));
        $processor = $template->getProcessorObject();
        $processor->setOptionsArray($template->getProcessorOptions());

        $destinationFile = CUTTER_basePath.'Temp/Processed/'.
            pathinfo($session->getArgument('workFile'), PATHINFO_FILENAME)
            .'_'.$template->getSuffix().'.jpg';

        // import image from a converter
        $imageFile = CUTTER_uploadPath.$session->getArgument('workFile');
        $converter = \VMFDS\Cutter\Factories\ConverterFactory::getFileHandler($imageFile);

        // process image
        $image = new \VMFDS\Cutter\Core\Image($converter->getImage($imageFile));
        $image->resize($request->getArgument('x'), $request->getArgument('y'),
            $template->getWidth(), $template->getHeight(),
            $request->getArgument('w'), $request->getArgument('h'));
        if ($request->hasArgument('legal')) {
            $legal = $request->getArgument('legal');
            \VMFDS\Cutter\Core\Logger::getLogger()->addDebug('Legal text is "'.$legal.'"');
            $image->setLegalText($legal, $template->getWidth(),
                $template->getHeight());
        }
        $image->toJpeg($destinationFile, 100);

        $this->data = $this->callProcessor($processor, $destinationFile);
    }

    /**
     * Call a processor on an image file
     * Recursively fall back to a possible fallback processor
     * @param \VMFDS\Cutter\Processors\AbstractProcessor $processor Processor object
     * @param \string $file Path to file
     * @return array Results array
     */
    private function callProcessor($processor, $file)
    {
        $request    = \VMFDS\Cutter\Core\Request::getInstance();
        $results    = $processor->process($file,
            $request->getArgumentsArray($processor->requiresArguments()));
        $this->data = $results;

        // Fallback to another processor?
        if ($results['result'] == $processor::RESULT_FALLBACK) {
            $fallbackProcessor = \VMFDS\Cutter\Factories\ProcessorFactory::get('Download');
            $results           = $this->callProcessor($fallbackProcessor, $file);
        }
        return $results;
    }
}