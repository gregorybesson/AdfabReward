<?php

namespace AdfabReward\Service;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\EventManager\EventProvider;
use AdfabReward\Options\ModuleOptions;

class Event extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var EventMapperInterface
     */
    protected $eventMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var EventServiceOptionsInterface
     */
    protected $options;

    public function edit(array $data, $event)
    {
        $this->getEventMapper()->update($event);
        //$this->getEventManager()->trigger(__FUNCTION__, $this, array('event' => $event, 'data' => $data));
        //$this->getEventMapper()->insert($event);
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, array('event' => $event, 'data' => $data));

        return $event;
    }

    /**
     * This function return count of events or total points by event category for one user
     * @param unknown_type $user
     * @param unknown_type $type
     * @param unknown_type $count
     */
    public function getTotal($user, $type='', $count='points')
    {
        $em = $this->getServiceManager()->get('adfabreward_doctrine_em');

        if ($count == 'points') {
            $aggregate = 'SUM(e.points)';
        } elseif ($count == 'count') {
            $aggregate = 'COUNT(e.id)';
        }

        // TODO : automatiser avec l'entité LeaderboardType directement en base
        switch ($type) {
            case 'game':
                $filter = array(12);
                break;
            case 'user':
                $filter = array(1,4,5,6,7,8,9,10,11);
                break;
            case 'newsletter':
                $filter = array(2,3);
                break;
            case 'sponsorship':
                $filter = array(20);
                break;
            case 'social':
                $filter = array(13,14,15,16,17);
                break;
            case 'quizAnswer':
                $filter = array(30);
                break;
            case 'badgesBronze':
                $filter = array(100);
                break;
            case 'badgesSilver':
                $filter = array(101);
                break;
            case 'badgesGold':
                $filter = array(102);
                break;
            case 'anniversary':
                $filter = array(25);
                break;
            default:
                $filter = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,25,100,101,102,103);
        }

        $query = $em->createQuery('SELECT ' . $aggregate . ' FROM AdfabReward\Entity\Event e WHERE e.user = :user AND e.action IN (?1)');
        $query->setParameter('user', $user);
        $query->setParameter(1, $filter);
        $total = $query->getSingleScalarResult();

        return $total;
    }

    public function findBy($array, $sort=array())
    {
        return $this->getEventMapper()->findBy($array, $sort=array());
    }

    /**
     * getEventMapper
     *
     * @return EventMapperInterface
     */
    public function getEventMapper()
    {
        if (null === $this->eventMapper) {
            $this->eventMapper = $this->getServiceManager()->get('adfabreward_event_mapper');
        }

        return $this->eventMapper;
    }

    /**
     * setEventMapper
     *
     * @param  EventMapperInterface $eventMapper
     * @return Event
     */
    public function setEventMapper(EventMapperInterface $eventMapper)
    {
        $this->eventMapper = $eventMapper;

        return $this;
    }

    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions()
    {
        if (!$this->options instanceof ModuleOptions) {
            $this->setOptions($this->getServiceManager()->get('adfabreward_module_options'));
        }

        return $this->options;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set service manager instance
     *
     * @param  ServiceManager $locator
     * @return Event
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }
}
