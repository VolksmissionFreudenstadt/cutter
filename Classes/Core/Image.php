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

namespace VMFDS\Cutter\Core;

/**
 * Description of Image
 *
 * @author chris
 */
class Image
{
    protected $image = null;

    public function __construct($image)
    {
        $this->image = $image;
    }

    public function resize($x, $y, $w1, $h1, $w2, $h2)
    {
        $dstImage    = ImageCreateTrueColor($w1, $h1);
        imagecopyresampled($dstImage, $this->image, 0, 0, $x, $y, $w1, $h1, $w2,
            $h2);
        $this->image = $dstImage;
    }

    public function toJpeg($destinationFile, $quality)
    {
        imagejpeg($this->image, $destinationFile, $quality);
    }

    public function setLegalText($legal, $w, $h)
    {
        //$legalText = ($localConfig['legal']['prefix'] ? $localConfig['legal']['prefix'].' '
        //            : '').$legal;
        $legalText     = $legal;
        $maxPercentage = 20;
        $minHeight     = 100;
        $font          = CUTTER_basePath.'Assets/Fonts/opensans.ttf';

        // calculate proper size
        $perc         = 0;
        $size         = 0;
        $height       = 0;
        $tgtMinHeight = $h * ($maxPercentage / 100);
        if ($tgtMinHeight < $minHeight) {
            $tgtMinHeight = $minHeight;
        }

        while ($height < $tgtMinHeight) {
            $size++;

            // calculate bounding box
            $box    = imagettfbbox($size, 0, $font, $legalText);
            $height = $box[2] - $box[0];
            //$perc = $height / ($localConfig['h'] / 100);
        }
        $size--;
        $box = imagettfbbox($size, 0, $font, $legalText);
        $x   = abs($box[5] - $box[1]);
        \VMFDS\Cutter\Core\Logger::getLogger()->addDebug(
            'Legal font size is '.$size);
        \VMFDS\Cutter\Core\Logger::getLogger()->addDebug(
            'Legal font file is '.$font);

        // insert source:
        $white = imagecolorallocate($this->image, 255, 255, 255);
        imagettftext($this->image, $size, 90, $x, $h - 5, $white, $font,
            $legalText);
    }
}