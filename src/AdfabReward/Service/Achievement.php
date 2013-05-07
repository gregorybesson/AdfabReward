<?php

namespace AdfabReward\Service;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\EventManager\EventProvider;
use AdfabReward\Options\ModuleOptions;

class Achievement extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var AchievementMapperInterface
     */
    protected $achievementMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var AchievementServiceOptionsInterface
     */
    protected $options;

    public function create(array $data)
    {

    }

    public function edit(array $data, $achievement)
    {
        $this->getAchievementMapper()->update($achievement);
        $this->getAchievementManager()->trigger(__FUNCTION__.'.post', $this, array('achievement' => $achievement, 'data' => $data));

        return $achievement;
    }

    /**
     * This function return the last $number events of type Badge
     * @param unknown_type $type
     * @param unknown_type $count
     */
    public function getLastBadgesActivity( $number=5 )
    {
        $em = $this->getServiceManager()->get('adfabreward_doctrine_em');

        $query = $em->createQuery('SELECT a, u FROM AdfabReward\Entity\Achievement a JOIN a.user u ORDER BY a.updatedAt DESC');
        $query->setMaxResults($number);
        $lastBadges = $query->getResult();

        return $lastBadges;
    }

    public function getTopBadge($user, $category='')
    {
        $em = $this->getServiceManager()->get('adfabreward_doctrine_em');

        $query = $em->createQuery("SELECT a FROM AdfabReward\Entity\Achievement a WHERE a.user = :user AND a.type = 'badge' AND a.category = '" . strtolower($category) . "' ORDER BY a.level DESC");
        $query->setParameter('user', $user);
        $query->setMaxResults(1);
        $result = $query->getResult();

        if (count($result) == 1) {
            return $result[0];
        } else {
            return null;
        }
    }

    public function getBadges($user)
    {
        $em = $this->getServiceManager()->get('adfabreward_doctrine_em');

        $query = $em->createQuery("SELECT MAX(a.level) as level, a.category, a.levelLabel, a.label FROM AdfabReward\Entity\Achievement a WHERE a.user = :user AND a.type = 'badge' GROUP BY a.category");
        $query->setParameter('user', $user);
        $result = $query->getResult();

        return $result;
    }

    public function findBy($array, $sort=array())
    {
        return $this->getAchievementMapper()->findBy($array, $sort);
    }

    /**
     * getAchievementMapper
     *
     * @return AchievementMapperInterface
     */
    public function getAchievementMapper()
    {
        if (null === $this->achievementMapper) {
            $this->achievementMapper = $this->getServiceManager()->get('adfabreward_achievement_mapper');
        }

        return $this->achievementMapper;
    }

    /**
     * setAchievementMapper
     *
     * @param  AchievementMapperInterface $achievementMapper
     * @return Achievement
     */
    public function setAchievementMapper(AchievementMapperInterface $achievementMapper)
    {
        $this->achievementMapper = $achievementMapper;

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
     * @return Achievement
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }
}
