<?php

namespace Controller;

use Arr;
use Popuper\EventsManager;
use Popuper\EventsRepository;
use Popuper\Variables\Repository as EventVariablesRepository;

/**
 * Trait PopupManagerInitTrait
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 */
trait EventsManagerInitTrait
{
    /**
     * @var \Popuper\EventsManager
     */
    protected $_eventsManager;

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Exception
     */
    public function before()
    {
        parent::before();

        $this->_eventsManager = new EventsManager(
            new EventsRepository($this->_leadId),
            new EventVariablesRepository()
        );
    }

    /**
     * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
     * @throws \Exception
     *
     * @param array $values
     *
     * @return array
     */
    protected function _buildEventData(array $values = [])
    {
        $eventData = $this->_eventsManager->getEventData();

        return Arr::array_diff_multidimensional(
            $eventData->getValues(), $values
        );
    }

}