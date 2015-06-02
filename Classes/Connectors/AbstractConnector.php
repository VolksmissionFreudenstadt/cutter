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

namespace VMFDS\Cutter\Connectors;

/**
 * Description of AbstractConnector
 *
 * @author chris
 */
class AbstractConnector
{
    protected $configuration = array();
    protected $db            = null;

    public function __construct()
    {
        $confMan             = \VMFDS\Cutter\Core\ConfigurationManager::getInstance();
        $this->configuration = $confMan->getConfigurationSet(
            $this->getKey(), 'Connectors');
        $this->db            = new \mysqli(
            $this->configuration['host'], $this->configuration['user'],
            $this->configuration['pass'], $this->configuration['name']
        );
    }

    /**
     * Get this connector's key (class without namespace and 'Provider')
     * @return \string
     */
    public function getKey()
    {
        $class = get_class($this);
        return str_replace('Connector', '',
            str_replace('VMFDS\\Cutter\\Connectors\\', '', $class));
    }

    public function query($sql)
    {
        return $this->db->query($sql);
    }

    public function getAll($sql)
    {
        $res  = $this->query($sql);
        $rows = array();
        while ($row  = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function getOne($sql)
    {
        $res  = $this->query($sql);
        $rows = array();
        if (!$row  = $res->fetch_assoc()) {
            $row = false;
        }
        return $row;
    }
}