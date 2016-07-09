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

namespace VMFDS\Cutter\Templates;

/**
 * Description of AbstractTemplate
 *
 * @author chris
 */
class AbstractTemplate
{
    protected $category         = '';
    protected $height           = 0;
    protected $width            = 0;
    protected $processor        = '';
    protected $suffix           = '';
    protected $title            = '';
    protected $processorOptions = array();

    public function getTemplateInfo()
    {
        $icon = \VMFDS\Cutter\Factories\ProcessorFactory::get($this->processor)->getIcon();
        return array(
            'title' => $this->title,
            'w' => $this->width,
            'h' => $this->height,
            'key' => $this->getKey(),
            'processor' => $this->processor,
            'category' => $this->category,
            'icon' => $icon,
        );
    }

    /**
     * Get this templates's key (class without namespace and 'Provider')
     * @return \string
     */
    public function getKey()
    {
        $class = get_class($this);
        return str_replace('Template', '',
            str_replace('VMFDS\\Cutter\\Templates\\', '', $class));
    }

    public function __construct()
    {
        $this->configuration['key'] = $this->getKey();
        $confMan = \VMFDS\Cutter\Core\ConfigurationManager::getInstance();
        $localConfig = $confMan->getConfigurationSet($this->getKey(), 'Templates');
        if (isset($localConfig['processor']))
            $this->processorOptions = $confMan->setDefaults($localConfig['processor'], ['defaults' => $this->processorOptions]);
    }

    public function getProcessorObject()
    {
        return \VMFDS\Cutter\Factories\ProcessorFactory::get($this->processor);
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getProcessor()
    {
        return $this->processor;
    }

    public function getSuffix()
    {
        return $this->suffix;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getProcessorOptions()
    {
        return $this->processorOptions;
    }
}