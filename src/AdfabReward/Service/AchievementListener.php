<?php
namespace AdfabReward\Service;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use ZfcBase\EventManager\EventProvider;
use Zend\EventManager\Event;

/**
 * This listener is used to calculate the points earned on user layer
 *
 * @author  Gregory Besson <gregory.besson@adfab.fr>
 */
class AchievementListener extends EventProvider implements ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
    protected $events = array();

    protected static $badges = array(
        'PLAYER'      => array(
            'event' => 'game',
            'unit' => 'jeu',
            'units' => 'jeux',
            'levels'=> array(
                1 => array(
                    'label' => 'Bronze',
                    'conditions' => 10
                ),
                2 => array(
                  'label' => 'Argent',
                    'conditions' => 50
                ),
                3 => array(
                  'label' => 'Or',
                  'conditions' => 100
                )
            ),
        ),
        'GOLDFATHER'      => array(
            'event' => 'sponsorship',
            'unit'  => 'filleul',
            'units' => 'filleuls',
            'levels'=> array(
                1 => array(
                    'label' => 'Bronze',
                    'conditions' => 1
                ),
                2 => array(
                    'label' => 'Argent',
                    'conditions' => 5
                ),
                3 => array(
                    'label' => 'Or',
                    'conditions' => 10
                )
            ),
        ),
        'BRAIN'      => array(
            'event' => 'quizAnswer',
            'unit'  => 'bonne réponse',
            'units' => 'bonnes réponses',
            'levels'=> array(
                1 => array(
                    'label' => 'Bronze',
                    'conditions' => 25
                ),
                2 => array(
                  'label' => 'Argent',
                    'conditions' => 150
                ),
                3 => array(
                  'label' => 'Or',
                  'conditions' => 250
                )
            ),
        ),
        'AMBASSADOR'      => array(
            'event' => 'social',
            'unit'  => 'partage',
            'units' => 'partages',
            'levels'=> array(
                1 => array(
                    'label' => 'Bronze',
                    'conditions' => 5
                ),
                2 => array(
                    'label' => 'Argent',
                    'conditions' => 50
                ),
                3 => array(
                    'label' => 'Or',
                    'conditions' => 100
                )
            ),
        ),
        'ANNIVERSARY'      => array(
            'event' => 'anniversary',
            'unit' => 'année',
            'units' => 'années',
            'levels'=> array(
                1 => array(
                    'label' => 'Bronze',
                    'conditions' => 1
                ),
                2 => array(
                  'label' => 'Argent',
                    'conditions' => 2
                ),
                3 => array(
                  'label' => 'Or',
                  'conditions' => 3
                )
            ),
        ),
    );

    public static function getBadges()
    {
        return self::$badges;
    }

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
/*
        // Badge Le parrain
        $this->listeners[] = $events->getSharedManager()->attach(
            'AdfabReward\Service\EventListener',
            'registerAfter.sponsor.post',
            array($this, 'badgeGodfather'),
            200
        );

        // Badge Le joueur
        $this->listeners[] = $events->getSharedManager()->attach(
            'AdfabReward\Service\EventListener',
            'subscribeAfter.post',
            array($this, 'badgePlayer'),
            200
        );

        // Badge L'ambassadeur
        $this->listeners[] = $events->getSharedManager()->attach(
            'AdfabReward\Service\EventListener',
            'sendShareMailAfter.post',
            array($this, 'badgeAmbassador'),
            200
        );
        */
/*
        // Badge Le cerveau
        $this->listeners[] = $events->getSharedManager()->attach(
                'AdfabReward\Service\EventListener',
                array('postFbWallAfter.post','sendShareMailAfter.post'),
                array($this, 'badgeBrain'),
                200
        );
*/
    }

    /**
     * {@inheritDoc}
     */
    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

/*
    public function badgeGodfather(Event $e)
    {
        $userId = $e->getParam('userId');

        $sm = $e->getTarget()->getServiceManager();
        $em = $e->getTarget()->getEventManager();
        $eventService = $sm->get('adfabreward_event_service');
        $actions = AdfabReward\Service\EventListener::$actions;
        $level = 0;

        $existingEvents = $eventService->getEventMapper()->findBy(array('actionId' => $actions['ACTION_SOCIAL_SPONSORSHIP']['id'],'userId' => $userId));

        if (count($existingEvents) == 1) {
            $level = 1;
            $levelLabel = 'BRONZE';
        }

        if (count($existingEvents) == 5) {
            $level = 2;
            $levelLabel = 'SILVER';
        }

        if (count($existingEvents) == 10) {
            $level = 3;
            $levelLabel = 'GOLD';
        }

        if ($level > 0) {
            $achievement = new \AdfabReward\Entity\Achievement();
            $achievement->setUserId($userId);
            $achievement->setType('badge');
            $achievement->setCategory('goldfather');
            $achievement->setLevel($level);
            $achievement->setLevelLabel($levelLabel);
            $achievement->setLabel('Badge Le parrain');
            $eventService->getEventMapper()->insert($achievement);

            $em->trigger(__FUNCTION__.'.'.$level.'.post', $this, array('userId' => $userId, 'achievement' => $achievement));
        }
    }

    public function badgePlayer(Event $e)
    {
        $userId = $e->getParam('userId');

        $sm = $e->getTarget()->getServiceManager();
        $em = $e->getTarget()->getEventManager();
        $eventService = $sm->get('adfabreward_event_service');
        $actions = AdfabReward\Service\EventListener::$actions;
        $level = 0;

        $existingEvents = $eventService->getEventMapper()->findBy(array('actionId' => $actions['ACTION_GAME_SUBSCRIBE']['id'],'userId' => $userId));

        if (count($existingEvents) == 1) {
            $level = 1;
            $levelLabel = 'BRONZE';
        }

        if (count($existingEvents) == 2) {
            $level = 2;
            $levelLabel = 'SILVER';
        }

        if (count($existingEvents) == 3) {
            $level = 3;
            $levelLabel = 'GOLD';
        }

        if ($level > 0) {
            $achievement = new \AdfabReward\Entity\Achievement();
            $achievement->setUserId($userId);
            $achievement->setType('badge');
            $achievement->setCategory('player');
            $achievement->setLevel($level);
            $achievement->setLevelLabel($levelLabel);
            $achievement->setLabel('Badge Le joueur');
            $eventService->getEventMapper()->insert($achievement);

            $em->trigger(__FUNCTION__.'.post', $this, array('userId' => $userId, 'achievement' => $achievement));
        }
    }

    public function badgeAmbassador(Event $e)
    {
        $userId = $e->getParam('userId');

        $sm = $e->getTarget()->getServiceManager();
        $em = $e->getTarget()->getEventManager();
        $eventService = $sm->get('adfabreward_event_service');
        $actions = AdfabReward\Service\EventListener::$actions;
        $level = 0;

        // trouver les events de type share (mail, fb ...)
        //$existingEvents = $eventService->getEventMapper()->findBy(array('actionId' => $actions['ACTION_GAME_SUBSCRIBE']['id'],'userId' => $userId));

        if (count($existingEvents) == 5) {
            $level = 1;
            $levelLabel = 'BRONZE';
        }

        if (count($existingEvents) == 50) {
            $level = 2;
            $levelLabel = 'SILVER';
        }

        if (count($existingEvents) == 100) {
            $level = 3;
            $levelLabel = 'GOLD';
        }

        if ($level > 0) {
            $achievement = new \AdfabReward\Entity\Achievement();
            $achievement->setUserId($userId);
            $achievement->setType('badge');
            $achievement->setCategory('ambassador');
            $achievement->setLevel($level);
            $achievement->setLevelLabel($levelLabel);
            $achievement->setLabel('Badge Ambassadeur');
            $eventService->getEventMapper()->insert($achievement);

            $em->trigger(__FUNCTION__.'.post', $this, array('userId' => $userId, 'achievement' => $achievement));
        }
    }*/


    public function grg(Event $e)
    {

        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, array());
    }
}
