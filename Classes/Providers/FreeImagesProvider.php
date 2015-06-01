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

namespace VMFDS\Cutter\Providers;

/**
 * Description of FreeImagesProvider
 *
 * @author chris
 */
class FreeImagesProvider extends AbstractProvider
{
    static protected $handledHosts = array('www.freeimages.com',
        'freeimages.com',
        'sxc.hu',
        'www.sxc.hu'
    );

    /**
     * Checks if this provider can handle urls from a specific host
     * @param \string $host Host
     * @return bool True, if provider can handle urls from this host
     */
    static public function canHandleHost($host)
    {
        return in_array($host, self::$handledHosts);
    }

    /**
     * Get the name for this provider
     * @return string Provider name
     */
    static public function getName()
    {
        return 'freeimages';
    }

    public function __construct()
    {
        parent::__construct();
        $this->configuration['baseUrl']  = 'http://www.freeimages.com';
        $this->configuration['loginUrl'] = 'http://www.freeimages.com/index.phtml';
    }

    /**
     * Retrieve an image from a specific url
     * @param \string $imageUrl url
     */
    public function retrieveImage($imageUrl)
    {
        // Deal with urls like http://www.freeimages.com/photo/7870
        $path = parse_url($imageUrl, PHP_URL_PATH);
        if (substr($path, 0, 7) == '/photo/') {
            $imageUrl = 'http://www.freeimages.com/browse.phtml?f=download&id='
                .substr($path, 7);
        }

        $this->login();
        $doc = $this->getDOMDocument($this->getFile($imageUrl));
        $src = $this->getSourceUrl($doc);

        // extract info from document title
        $title   = $this->getDocTitle($doc);
        $markers = $this->regexMarkers('/(.*)\(Stock Photo By (.*)\) \[ID: (.*)\]/is',
            $title, array(1 => 'title', 2 => 'user', 3 => 'id'));

        $this->workFile = $this->replaceMarkers($this->configuration['fileNamePattern'],
            $markers);
        $this->legal    = $this->replaceMarkers($this->configuration['legalPattern'],
            $markers, FALSE);

        $this->workFile = preg_replace('/\W+/', '_', $this->workFile).'.'.pathinfo($src,
                PATHINFO_EXTENSION);
        $this->writeFile($src, CUTTER_uploadPath.$this->workFile);
    }

    /**
     * Log into freeimages.com
     *
     * @return string Answer
     */
    protected function login()
    {
        $this->post($this->configuration['loginUrl'],
            array(
            'where' => '',
            'f' => 'login',
            'submit' => 'Sign in',
            'login' => $this->configuration['login']['user'],
            'pass' => $this->configuration['login']['password'],
        ));
    }

    /**
     * Extract image source URL from a document
     *
     * @param \DOMDocument $doc DOMDocument object
     * @return string Image source url
     */
    protected function getSourceUrl($doc)
    {
        $img = $doc->getElementsByTagName('img')->item(1);
        $src = $img->getAttribute('src');
        if (strpos($src, '//') === false) {
            $sep = (substr($src, 0, 1) != '/') ? '/' : '';
            $src = $this->configuration['baseUrl'].$sep.$src;
        }
        return $src;
    }

    /**
     * Extract title and legal info from a document
     *
     * @param string $src Source url
     * @param \DOMDocument $doc Document object
     */
    protected function getTitleAndInfo($src, $doc)
    {
        if ($this->configuration['nameByTitle']) {
            $searchArray  = array();
            $replaceArray = array();
            foreach ($marker as $needle => $val) {
                $searchArray[]  = '###'.strtoupper($needle).'###';
                $replaceArray[] = str_replace(' ', '-',
                    str_replace('.', '', trim($val)));
            }
            $this->workFile = str_replace($searchArray, $replaceArray,
                $this->configuration['nameByTitle']['pattern']);

            $this->legal = str_replace($searchArray, $replaceArray,
                $this->configuration['nameByTitle']['legalPattern']);
        }
        $this->workFile = pathinfo($src, PATHINFO_FILENAME);
    }
}