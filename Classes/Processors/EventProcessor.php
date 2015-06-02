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

namespace VMFDS\Cutter\Processors;

/**
 * Description of DownloadProcessor
 *
 * @author chris
 */
class EventProcessor extends AbstractProcessor
{
    protected $icon = 'calendar';
    protected $kool = array();

    public function __construct()
    {
        parent::__construct();
        $confManager = \VMFDS\Cutter\Core\ConfigurationManager::getInstance();
        $this->kool  = $confManager->getConfigurationSet('event', 'Processors');
    }

    public function getAdditionalFields()
    {
        return array(
            0 => array(
                'key' => 'event',
                'form' => $this->kOOLEventSelect(),
                'label' => 'Veranstaltung'),
        );
    }

    /**
     * Get a select field with all relevant kOOL events
     *
     * @param int eid Id of an event selected in previous steps
     * @return string Select field with all relevant kOOL events
     */
    private function kOOLEventSelect($eid = NULL)
    {
        global $config;

        // connect to db
        $dbConf = $this->kool['kOOL']['db'];
        $db     = mysql_connect($dbConf['host'], $dbConf['user'],
            $dbConf['pass']);
        mysql_select_db($dbConf['name'], $db);

        // build sql
        $eConf = $this->kool['kOOL']['event_select'];
        $where = array();
        $sql   = 'SELECT event.id, event.title,event.startdatum,event.kommentar FROM '.$dbConf['event_table'].' event LEFT JOIN '.$dbConf['group_table'].' grp ON (event.eventgruppen_id = grp.id) ';
        if ($this->getOption('allowed_calendars')) {
            $where[] = '(grp.calendar_id IN ('.join(',',
                    $localConfig['allowed_calendars']).'))';
        }
        if ($eConf['range']['start'])
                $where[] = '(event.startdatum>=\''.date('Y-m-d',
                    strtotime($eConf['range']['start'])).'\')';
        if ($eConf['range']['end'])
                $where[] = '(event.startdatum<=\''.date('Y-m-d',
                    strtotime($eConf['range']['end'])).'\')';
        if (is_array($eConf['allowed_categories']) && (!$this->getOption('ignore_categories'))) {
            $catWhere   = array();
            foreach ($eConf['allowed_categories'] as $cat)
                $catWhere[] = '(FIND_IN_SET(\''.$cat.'\', event.'.$eConf['category_field'].'))';
            $where[]    = '('.join(' OR ', $catWhere).')';
        }
        if ($this->getOption('skip_already_set'))
                $where[] = '(event.'.$localConfig['event_field'].' IS NULL)';
        $sql .= ' WHERE ('.join(' AND ', $where).')';
        if ($eConf['order_by'])
                $sql .= ' ORDER BY event.'.$eConf['order_by'].' ASC';
        $sql.=';';
        //die ($sql);
        // execute
        $res     = mysql_query($sql);

        // build select
        $select = '<select name="event" id="event"><option value="-1"></option>';
        while ($row    = mysql_fetch_assoc($res)) {
            $rowTitle = utf8_encode($row['title'] ? $row['title'] : $row['kommentar']);
            $select .= '<option value="'.$row['id'].' '.(($row['id'] == $eid) ? ' selected'
                        : '').'">'.strftime('%d.%m.%Y',
                    strtotime($row['startdatum'])).' '.$rowTitle.'</option>';
        }
        $select .= '</select>';

        return $select;
    }

    /**
     * Process an image file
     * @param \string $fileName Path to file
     * @param array $options Options
     * @return variant Return values
     */
    public function process($fileName, $options)
    {
        if ($this->checkRequiredArguments($options)) {
            die(print_r($options, 1));
            return true;
        }
    }

    /**
     * Get a list of required arguments
     * @return array List of required arguments
     */
    public function requiresArguments()
    {
        return array('event');
    }
}