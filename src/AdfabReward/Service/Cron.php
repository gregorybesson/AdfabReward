<?php

namespace AdfabReward\Service;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\EventManager\EventProvider;
use AdfabGame\Options\ModuleOptions;

class Cron extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var AchievementServiceOptionsInterface
     */
    protected $options;

    /**
     * @var AchievementMapperInterface
     */
    protected $achievementMapper;

    /**
     * @var EventMapperInterface
     */
    protected $eventMapper;

    public static function badgeAnniversary()
    {

        $configuration = array(
            'modules' => array(
                'Application',
                'DoctrineModule',
                'DoctrineORMModule',
                'ZfcBase',
                'ZfcUser',
                'BjyAuthorize',
                'ZfcAdmin',
                'AdfabCore',
                'AdfabUser',
                'AdfabReward'
            ),
            'module_listener_options' => array(
                'config_glob_paths'    => array(
                    'config/autoload/{,*.}{global,local}.php',
                ),
                'module_paths' => array(
                    './module',
                    './vendor',
                ),
            ),
        );
        $smConfig = isset($configuration['service_manager']) ? $configuration['service_manager'] : array();
        $sm = new \Zend\ServiceManager\ServiceManager(new \Zend\Mvc\Service\ServiceManagerConfig($smConfig));
        $sm->setService('ApplicationConfig', $configuration);
        $sm->get('ModuleManager')->loadModules();
        $sm->get('Application')->bootstrap();

        $rewardService = $sm->get('adfabreward_cron_service');
        $options = $sm->get('adfabreward_module_options');

        $rewardService->anniversary();
    }

    /**
     * TODO : Il faudra un import spécifique pour ce badge durant la reprise
     */
    public function anniversary()
    {
        $em = $this->getServiceManager()->get('adfabreward_doctrine_em');

        $now = new \DateTime('now');
        $month = $now->format('m');
        $day = $now->format('d');

        $actions = \AdfabReward\Service\EventListener::getActions();

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping;
        $rsm->addEntityResult('\AdfabUser\Entity\User', 'u');
        $rsm->addFieldResult('u', 'user_id', 'id');
        $rsm->addFieldResult('u', 'created_at', 'created_at');

        $query = $em->createNativeQuery('SELECT user_id, created_at FROM user WHERE MONTH(created_at) = ? AND DAY(created_at) = ?', $rsm);
        $query->setParameter(1, $month);
        $query->setParameter(2, $day);

        $usersToReward = $query->getResult();

        foreach ($usersToReward as $user) {
            $number = $now->format('Y') - $user->getCreatedAt()->format('Y');

            // Je vérifie que tous les badges anniversary ont bien été créés pour le user
            for ($i=1;$i<=$number;$i++) {
                $existingAchievements = $this->getAchievementMapper()->findOneBy(array('type' => 'badge', 'category' => 'anniversary', 'level' => $i, 'user' => $user));

                if (count($existingAchievements) == 0) {
                    if ($i == 1) {
                        $level = 1;
                        $levelLabel = 'BRONZE';
                    }

                    if ($i == 2) {
                        $level = 2;
                        $levelLabel = 'SILVER';
                    }

                    if ($i >= 3) {
                        $level = $i;
                        $levelLabel = 'GOLD';
                    }

                    $achievement = new \AdfabReward\Entity\Achievement();
                    $achievement->setUser($user);
                    $achievement->setType('badge');
                    $achievement->setCategory('anniversary');
                    $achievement->setLevel($i);
                    $achievement->setLevelLabel($levelLabel);
                    $achievement->setLabel('Badge Anniversaire');
                    $this->getAchievementMapper()->insert($achievement);

                    $event = new \AdfabReward\Entity\Event();
                    $event->setUser($user);
                    $event->setLabel('Badge Anniversaire');
                    if ($i == 1) {
                        $event->setActionId($actions['ACTION_BADGE_BRONZE']['id']);
                        $event->setPoints($actions['ACTION_BADGE_BRONZE']['points']);
                    } elseif ($i == 2) {
                        $event->setActionId($actions['ACTION_BADGE_SILVER']['id']);
                        $event->setPoints($actions['ACTION_BADGE_SILVER']['points']);
                    } elseif ($i >= 3) {
                        $event->setActionId($actions['ACTION_BADGE_GOLD']['id']);
                        $event->setPoints($actions['ACTION_BADGE_GOLD']['points']);
                    }
                    $this->getEventMapper()->insert($event);

                    // event supplémentaire de 250 points d'anniversaire. cf. maquettes de points et badges...
                    $event = new \AdfabReward\Entity\Event();
                    $event->setUser($user);
                    $event->setLabel('Bonus Anniversaire');
                    $event->setActionId($actions['ACTION_USER_ANNIVERSARY']['id']);
                    $event->setPoints($actions['ACTION_USER_ANNIVERSARY']['points']);
                    $this->getEventMapper()->insert($event);
                }
            }
        }

        return true;
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
     * @param  ServiceManager $serviceManager
     * @return Achievement
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }
}
