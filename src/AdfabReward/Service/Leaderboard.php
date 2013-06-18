<?php

namespace AdfabReward\Service;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\EventManager\EventProvider;
use AdfabReward\Options\ModuleOptions;

class Leaderboard extends EventProvider implements ServiceManagerAwareInterface
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

    public function getRank($userId, $timeScale='')
    {
        $em = $this->getServiceManager()->get('adfabreward_doctrine_em');
        
        $prefix = $timeScale == 'week' ? 'week' : 'total'; 
        
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping;
        $rsm->addScalarResult('points', 'points');
        $rsm->addScalarResult('rank', 'rank');
        
        $query = $em->createNativeQuery('
            SELECT
                COUNT(*) + 1 AS rank,
                rl2.'.$prefix.'_points AS points
            FROM reward_leaderboard AS rl, reward_leaderboard AS rl2
            WHERE
                rl.leaderboardtype_id = 1 AND
                rl2.leaderboardtype_id = 1 AND
                rl2.user_id = ? AND
                rl.'.$prefix.'_points > rl2.'.$prefix.'_points
        ', $rsm);
        
        $query->setParameter(1, $userId);
        
        $result = $query->getResult();
        
        if (count($result) == 1) {
            $rank = $result[0];
            return $rank;
        } else {
            return array('rank'=>0,'result'=>0);
        }
        
    }

    /**
     * This function return count of events or total points by event category for one user
     * @param unknown_type $user
     * @param unknown_type $type
     * @param unknown_type $count
     */
    public function getLeaderboardQuery( $type='', $timeScale='', $search='')
    {
        $em = $this->getServiceManager()->get('adfabreward_doctrine_em');
        $filterSearch = '';
        $dateLimit = '';

        $prefix = $timeScale == 'week' ? 'week' : 'total'; 

        if ($search != '') {
            $filterSearch = ' AND (u.username LIKE :queryString1 OR u.lastname LIKE :queryString2 OR u.firstname LIKE :queryString3)';
        }
        
        // TODO : automatiser avec l'entité LeaderboardType directement en base
        switch ($type) {
            case 'game':
                $leaderboardTypeId = 2;
                break;
            case 'sponsorship':
                $leaderboardTypeId = 3;
                break;
            case 'social':
                $leaderboardTypeId = 4;
                break;
            default:
                $leaderboardTypeId = 1;
        }

        $query = $em->createQuery('
            SELECT e.'.$prefix.'Points as points, u.username, u.avatar, u.id, u.firstname, u.lastname, u.title, u.state
            FROM AdfabReward\Entity\Leaderboard e
            JOIN e.user u
            WHERE u.state = 1 AND e.leaderboardType = :leaderboardTypeId '.$filterSearch.'
            ORDER BY e.'.$prefix.'Points DESC
        ');
        $query->setParameter('leaderboardTypeId', $leaderboardTypeId);
        if ($search != '') {
            $query->setParameter('queryString1', '%'.$search.'%');
            $query->setParameter('queryString2', '%'.$search.'%');
            $query->setParameter('queryString3', '%'.$search.'%');
        }
        return $query;
    }

	/**
     * This function return count of events or total points by event category for one user
     * @param unknown_type $user
     * @param unknown_type $type
     * @param unknown_type $count
     */
    public function getLeaderboard( $type='', $timeScale='', $search='', $nbItems = 5)
    {
    	$query = $this->getLeaderboardQuery($type='', $timeScale='', $search);
		if ($nbItems>0) {
            $query->setMaxResults($nbItems);
        }
        try {
            $leaderboard = $query->getResult();
        }
        catch( \Doctrine\ORM\Query\QueryException $e ) {
            echo $e->getMessage();
            echo $e->getTraceAsString();
            exit();
        }

        return $leaderboard;
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
