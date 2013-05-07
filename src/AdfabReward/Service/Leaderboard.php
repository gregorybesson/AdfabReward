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
        $dateLimit = '';

        if (strtolower($timeScale) == 'week') {
            $now = new \DateTime("now");
            $interval = 'P7D';
            $now->sub(new \DateInterval($interval));
            $dateLimit = " WHERE (re.created_at >= '" . $now->format('Y-m-d') . " 0:0:0'  OR isnull(re.created_at)) ";
        }

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping;
        $rsm->addScalarResult('points', 'points');
        $rsm->addScalarResult('rank', 'rank');

        // I use MySql user variable to determine the rank of the user in the leaderboard.
        $em->getConnection()->exec('SET @rank=0');

        $query = $em->createNativeQuery('
            SELECT @rank:=@rank+1 AS rank, IF(isnull(points),0, points) AS points
            FROM (SELECT
                sum(points) as points, user.user_id, re.created_at
                FROM
                user
                LEFT JOIN reward_event AS re ON re.user_id=user.user_id'
                . $dateLimit . '
                GROUP BY user.user_id
                ORDER BY points DESC
            ) t
            GROUP BY user_id
            HAVING user_id= ?
        ', $rsm);
        $query->setParameter(1, $userId);

        $result = $query->getResult();

        if (count($result) == 1) {
            $rank = $result[0];

            return $rank;
        } else {
            return null;
        }
    }

    /**
     * This function return count of events or total points by event category for one user
     * @param unknown_type $user
     * @param unknown_type $type
     * @param unknown_type $count
     */
    public function getLeaderboard( $type='', $timeScale='', $search='', $nbItems = 5)
    {
        $em = $this->getServiceManager()->get('adfabreward_doctrine_em');
        $filterSearch = '';
        $dateLimit = '';

        if (strtolower($timeScale) == 'week') {
            $now = new \DateTime("now");
            $interval = 'P7D';
            $now->sub(new \DateInterval($interval));
            $dateLimit = " AND e.createdAt >= '" . $now->format('Y-m-d') . " 0:0:0'";
        }

        if ($search != '') {
            $filterSearch = " AND (u.username like '%" . $search . "%' OR u.lastname like '%" . $search . "%' OR u.firstname like '%" . $search . "%')";
        }

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

        $query = $em->createQuery('
            SELECT SUM(e.points) as points, u.username, u.avatar, u.id, u.firstname, u.lastname, u.title, u.state
            FROM AdfabReward\Entity\Event e
            JOIN e.user u
            WHERE e.actionId in (?1)' .
            $dateLimit .
            $filterSearch .
            'AND u.state = 1
            GROUP BY e.user
            ORDER BY points DESC
        ');
        $query->setParameter(1, $filter);
        if ($nbItems>0) {
            $query->setMaxResults($nbItems);
        }
        $leaderboard = $query->getResult();

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
