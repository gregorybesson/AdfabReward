<?php
namespace AdfabReward\Service;

use Zend\Session\Container;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Zend\EventManager\Event;
use ZfcBase\EventManager\EventProvider;

/**
 * This listener is used to calculate the points earned on user layer
 *
 * @author  Gregory Besson <gregory.besson@adfab.fr>
 */
class EventListener extends EventProvider implements ListenerAggregateInterface
{
    // @constant
    protected static $actions = array(
        'ACTION_USER_REGISTER'      => array('id'=>1,  'points'=>200, 'category' => 'user',       'label'=>'création de compte'),
        'ACTION_USER_OPTIN'         => array('id'=>2,  'points'=>150, 'category' => 'newsletter', 'label'=>'newsletter'),
        'ACTION_USER_OPTINPARTNER'  => array('id'=>3,  'points'=>150, 'category' => 'newsletter', 'label'=>'newsletter partenaires'),
        'ACTION_USER_USERNAME'      => array('id'=>4,  'points'=>100, 'category' => 'user',       'label'=>'pseudo'),
        'ACTION_USER_AVATAR'        => array('id'=>5,  'points'=>150, 'category' => 'user',       'label'=>'avatar'),
        'ACTION_USER_ADDRESS'       => array('id'=>6,  'points'=>150, 'category' => 'user',       'label'=>'adresse'),
        'ACTION_USER_CITY'          => array('id'=>7,  'points'=>75,  'category' => 'user',       'label'=>'ville'),
        'ACTION_USER_TELEPHONE'     => array('id'=>8,  'points'=>150, 'category' => 'user',       'label'=>'téléphone'),
        'ACTION_USER_CHILDREN'      => array('id'=>9,  'points'=>75,  'category' => 'user',       'label'=>'nombre d\'enfants'),
        'ACTION_USER_FORM'          => array('id'=>10, 'points'=>100, 'category' => 'user',       'label'=>'validation de compte'),
        'ACTION_USER_PRIZECATEGORY' => array('id'=>11, 'points'=>100, 'category' => 'user',       'label'=>'centres d\'intérêts'),
        'ACTION_GAME_SUBSCRIBE'     => array('id'=>12, 'points'=>100, 'category' => 'game',       'label'=>'inscription à un jeu'),
        'ACTION_SOCIAL_SHAREMAIL'   => array('id'=>13, 'points'=>0,   'category' => 'social',     'label'=>'partage par mail'),
        'ACTION_SOCIAL_FBWALL'      => array('id'=>14, 'points'=>0,   'category' => 'social',     'label'=>'partage par FB Wall'),
        'ACTION_SOCIAL_FBFRIEND'    => array('id'=>15, 'points'=>0,   'category' => 'social',     'label'=>'partage par invitation FB'),
        'ACTION_SOCIAL_TWITTER'     => array('id'=>16, 'points'=>0,   'category' => 'social',     'label'=>'partage par twitter'),
        'ACTION_SOCIAL_GOOGLE'      => array('id'=>17, 'points'=>0,   'category' => 'social',     'label'=>'partage par google'),
        'ACTION_SOCIAL_SPONSORSHIP' => array('id'=>20, 'points'=>250, 'category' => 'social',     'label'=>'parrainage inscription'),
        'ACTION_USER_ANNIVERSARY'   => array('id'=>25, 'points'=>250, 'category' => 'user',       'label'=>'bonus anniversaire'),
        'ACTION_QUIZ_CORRECTANSWERS'=> array('id'=>30, 'points'=>0,   'category' => 'quiz',       'label'=>'Quiz : Bonnes réponses'),
        'ACTION_BADGE_BRONZE'       => array('id'=>100,'points'=>250, 'category' => 'badge',      'label'=>'badge bronze'),
        'ACTION_BADGE_SILVER'       => array('id'=>101,'points'=>500, 'category' => 'badge',      'label'=>'badge argent'),
        'ACTION_BADGE_GOLD'         => array('id'=>102,'points'=>750, 'category' => 'badge',      'label'=>'badge or'),
    );
    
    protected static $populated = false;

    public static function getActions(\AdfabReward\Service\Action $actionService)
    {
        if ( ! self::$populated ) {
            foreach( $actionService->getActionMapper()->findAll() as $action ) {
                foreach( self::$actions as $key => $a ) {
                    if ( $a['id'] == $action->getId() ) {
                        self::$actions[$key]['action'] = $action; 
                    }
                }
            }
        }
        return self::$actions;
    }

    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();
    protected $eventsArray = array();

    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        //REGISTER
        $this->listeners[] = $events->getSharedManager()->attach('AdfabUser\Service\User','register.post', array($this, 'registerAfter'), 200);

        //optin, optinPartner
        $this->listeners[] = $events->getSharedManager()->attach('AdfabUser\Service\User','updateNewsletter.pre', array($this, 'newsletterBefore'), 200);
        $this->listeners[] = $events->getSharedManager()->attach('AdfabUser\Service\User','updateNewsletter.post', array($this, 'newsletterAfter'), 200);

        //username, avatar, address, city, telephone, children
        $this->listeners[] = $events->getSharedManager()->attach('AdfabUser\Service\User',array('updateInfo.pre','updateAddress.pre'), array($this, 'infoBefore'), 200);
        $this->listeners[] = $events->getSharedManager()->attach('AdfabUser\Service\User',array('updateInfo.post','updateAddress.post'), array($this, 'infoAfter'), 200);

        // catégories de jeux préférées du client
        $this->listeners[] = $events->getSharedManager()->attach('AdfabGame\Service\PrizeCategoryUser','edit.post', array($this, 'prizeCategoryAfter'), 200);

        // inscription à un jeu
        $this->listeners[] = $events->getSharedManager()->attach(array(
                'AdfabGame\Service\Lottery',
                'AdfabGame\Service\PostVote',
                'AdfabGame\Service\Quiz',
                'AdfabGame\Service\InstantWin'
            ),'play.post', array($this, 'playAfter'), 200);

        // partage par mail
        $this->listeners[] = $events->getSharedManager()->attach(array(
                'AdfabGame\Service\Lottery',
                'AdfabGame\Service\PostVote',
                'AdfabGame\Service\Quiz',
                'AdfabGame\Service\InstantWin'
        ),'sendShareMail.post', array($this, 'sendShareMailAfter'), 200);

        // partage par wall FB
        $this->listeners[] = $events->getSharedManager()->attach(array(
                'AdfabGame\Service\Lottery',
                'AdfabGame\Service\PostVote',
                'AdfabGame\Service\Quiz',
                'AdfabGame\Service\InstantWin'
        ),'postFbWall.post', array($this, 'postFbWallAfter'), 200);

        // partage par wall FB
        $this->listeners[] = $events->getSharedManager()->attach(array(
                'AdfabGame\Service\Lottery',
                'AdfabGame\Service\PostVote',
                'AdfabGame\Service\Quiz',
                'AdfabGame\Service\InstantWin'
        ),'postFbRequest.post', array($this, 'postFbRequestAfter'), 200);

        // partage par twitter
        $this->listeners[] = $events->getSharedManager()->attach(array(
                'AdfabGame\Service\Lottery',
                'AdfabGame\Service\PostVote',
                'AdfabGame\Service\Quiz',
                'AdfabGame\Service\InstantWin'
        ),'postTwitter.post', array($this, 'postTwitterAfter'), 200);

        // partage par wall FB
        $this->listeners[] = $events->getSharedManager()->attach(array(
                'AdfabGame\Service\Lottery',
                'AdfabGame\Service\PostVote',
                'AdfabGame\Service\Quiz',
                'AdfabGame\Service\InstantWin'
        ),'postGoogle.post', array($this, 'postGoogleAfter'), 200);

        // reponses justes aux quiz
        $this->listeners[] = $events->getSharedManager()->attach(
            'AdfabGame\Service\Quiz',
            'createQuizReply.post',
            array($this, 'createQuizReplyAfter'),
            200
        );
/*
        // obtention d'un badge bronze
        $this->listeners[] = $events->getSharedManager()->attach(
            'AdfabReward\Service\Achievement',
            array('badgeGodfather.1.post', 'badgePlayer.post', 'badgeAmbassador.post'),
            array($this, 'badgeBronzeAfter'),
            200
        );

        // obtention d'un badge argent
        $this->listeners[] = $events->getSharedManager()->attach(
                'AdfabReward\Service\Achievement',
                array('badgeGodfather.2.post', 'badgePlayer.post', 'badgeAmbassador.post'),
                array($this, 'badgeSilverAfter'),
                200
        );

        // obtention d'un badge or
        $this->listeners[] = $events->getSharedManager()->attach(
                'AdfabReward\Service\Achievement',
                array('badgeGodfather.3.post', 'badgePlayer.post', 'badgeAmbassador.post'),
                array($this, 'badgeGoldAfter'),
                200
        );*/
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

    /**
     * event registration + badge parrain
     * @param Event $e
     */
    public function registerAfter(Event $e)
    {
        $user = $e->getParam('user');
        $sm = $e->getTarget()->getServiceManager();
        $actionService = $sm->get('adfabreward_action_service');
        $actions = self::getActions($actionService);

        $eventService = $sm->get('adfabreward_event_service');
        $event = new \AdfabReward\Entity\Event();
        $event->setAction($actions['ACTION_USER_REGISTER']['action']);
        $event->setUser($user);
        $event->setPoints($actions['ACTION_USER_REGISTER']['points']);
        $event->setLabel($actions['ACTION_USER_REGISTER']['label']);
        $eventService->getEventMapper()->insert($event);

        $sm->get('jhu.zdt_logger')->info('Event register créé');

        // Is there a sponsorship ?
        $session = new Container('sponsorship');
        // Is there a secretKey in session ?
        if ($session->offsetGet('key')) {
            $socialEvent =  $eventService->getEventMapper()->findOneBySecretKey($session->offsetGet('key'));
            //Is this key is valid, I record a sponsorship event for the original user.
            if ($socialEvent) {
                $event = new \AdfabReward\Entity\Event();
                $event->setAction($actions['ACTION_SOCIAL_SPONSORSHIP']['action']);
                $event->setUser($socialEvent->getUser());
                $event->setPoints($actions['ACTION_SOCIAL_SPONSORSHIP']['points']);
                $event->setLabel($user->getEmail());
                $eventService->getEventMapper()->insert($event);

                // badge player
                $level = 0;

                $existingEvents = $eventService->getEventMapper()->findBy(array('action' => $actions['ACTION_SOCIAL_SPONSORSHIP']['action'],'user' => $socialEvent->getUser()));

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
                    $achievement->setUser($socialEvent->getUser());
                    $achievement->setType('badge');
                    $achievement->setCategory('goldfather');
                    $achievement->setLevel($level);
                    $achievement->setLevelLabel($levelLabel);
                    $achievement->setLabel('Le parrain');
                    $eventService->getEventMapper()->insert($achievement);
                    if ($level == 1) {
                        $this->badgeBronzeAfter($sm, $socialEvent->getUser(), 'Le parrain');
                    }
                    if ($level == 2) {
                        $this->badgeSilverAfter($sm, $socialEvent->getUser(), 'Le parrain');
                    }
                    if ($level == 3) {
                        $this->badgeGoldAfter($sm, $socialEvent->getUser(), 'Le parrain');
                    }
                }
            }
        }
    }



    /**
     * Scan and log event
     *
     * @param  MvcEvent $e
     * @return void
     */
    public function newsletterBefore(Event $e)
    {
        $data = $e->getParam('data');
        $user = $e->getParam('user');

        $sm = $e->getTarget()->getServiceManager();

        /*
        $logText =  'optin avant : ' . $user->getOptin() . 'apres : ' . $data['optin'] . '<br/>';
        $logText .=  'optinPartner avant : ' . $user->getOptinPartner() . 'apres : ' . $data['optinPartner']. '<br/>';
        $sm->get('jhu.zdt_logger')->info($logText);
        */


        //si avant !=1 et apres=1 => true
        if ($user->getOptin() != 1 && $data['optin'] == 1) {
            $this->eventsArray['optin'] = 'ACTION_USER_OPTIN';
        }
        if ($user->getOptinPartner() != 1 && $data['optinPartner'] == 1) {
            $this->eventsArray['optinPartner'] = 'ACTION_USER_OPTINPARTNER';
        }
    }

    public function newsletterAfter(Event $e)
    {
        $user = $e->getParam('user');
        $sm = $e->getTarget()->getServiceManager();
        $actionService = $sm->get('adfabreward_action_service');
        $actions = self::getActions($actionService);

        if (count($this->eventsArray) > 0) {
            // On compte les events
            $eventService = $sm->get('adfabreward_event_service');

            foreach ($this->eventsArray as $attribute => $value) {
                $existingEvents = $eventService->getEventMapper()->findBy(array('action' => $actions[$value]['action'],'user' => $user));
                if (count($existingEvents) == 0) {
                    $event = new \AdfabReward\Entity\Event();
                    $event->setAction($actions[$value]['action']);
                    $event->setUser($user);
                    $event->setPoints($actions[$value]['points']);
                    $event->setLabel($actions[$value]['label']);
                    $eventService->getEventMapper()->insert($event);
                    //$sm->get('jhu.zdt_logger')->info('Event $attribute créé');;
                }
            }
        }
    }

    /**
     * Scan and log event
     *
     * @param  MvcEvent $e
     * @return void
     */
    public function infoBefore(Event $e)
    {
        $data = $e->getParam('data');
        $user = $e->getParam('user');

        /*$logText  =  'username avant : ' . $user->getUsername() . 'apres : ' . $data['username'] . '<br/>';
        $logText .=  'avatar avant : ' . $user->getAvatar() . 'apres : ' . $data['avatar']. '<br/>';
        $logText .=  'address avant : ' . $user->getAddress() . 'apres : ' . $data['address']. '<br/>';
        $logText .=  'city avant : ' . $user->getCity() . 'apres : ' . $data['city']. '<br/>';
        $logText .=  'telephone avant : ' . $user->getTelephone() . 'apres : ' . $data['telephone']. '<br/>';
        $logText .=  'children avant : ' . $user->getChildren() . 'apres : ' . $data['children']. '<br/>';

        $sm->get('jhu.zdt_logger')->info($logText);*/


        //si avant !=1 et apres=1 => true
        if (isset($data['username']) && $user->getUsername() != $data['username'] && $data['username'] != '') {
            $this->eventsArray['username'] = 'ACTION_USER_USERNAME';
        }
        if (isset($data['avatar']) && $user->getAvatar() != $data['avatar'] && $data['avatar'] != '') {
            $this->eventsArray['avatar'] = 'ACTION_USER_AVATAR';
        }
        if (isset($data['address']) && $user->getAddress() != $data['address'] && $data['address'] != '') {
            $this->eventsArray['address'] = 'ACTION_USER_ADDRESS';
        }
        if (isset($data['city']) && $user->getCity() != $data['city'] && $data['city'] != '') {
            $this->eventsArray['city'] = 'ACTION_USER_CITY';
        }
        if (isset($data['telephone']) && $user->getTelephone() != $data['telephone'] && $data['telephone'] != '') {
            $this->eventsArray['telephone'] = 'ACTION_USER_TELEPHONE';
        }
        if (isset($data['children']) && $user->getChildren() != $data['children'] && $data['children'] != '') {
            $this->eventsArray['children'] = 'ACTION_USER_CHILDREN';
			
        }
        // juste valider le form
        $this->eventsArray['form'] = 'ACTION_USER_FORM';
		
    }

    public function infoAfter(Event $e)
    {
        $user = $e->getParam('user');
        $sm = $e->getTarget()->getServiceManager();
        $actionService = $sm->get('adfabreward_action_service');
        $actions = self::getActions($actionService);

        if (count($this->eventsArray) > 0) {
            // On compte les events
            $eventService = $sm->get('adfabreward_event_service');

          //  print_r($this->events);
          //  die();

        foreach ($this->eventsArray as $attribute => $value) {
                $existingEvents = $eventService->getEventMapper()->findBy(array('action' => $actions[$value]['action'],'user' => $user));
                if (count($existingEvents) == 0) {
                    $event = new \AdfabReward\Entity\Event();
                    $event->setAction($actions[$value]['action']);
                    $event->setUser($user);
                    $event->setPoints($actions[$value]['points']);
                    $event->setLabel($actions[$value]['label']);
                    $eventService->getEventMapper()->insert($event);
                    //$sm->get('jhu.zdt_logger')->info('Event $attribute créé');;
                }
            }
        }
    }

    public function prizeCategoryAfter(Event $e)
    {
        $user = $e->getParam('user');
        $sm = $e->getTarget()->getServiceManager();
        $eventService = $sm->get('adfabreward_event_service');
        $actionService = $sm->get('adfabreward_action_service');
        $actions = self::getActions($actionService);

        $existingEvents = $eventService->getEventMapper()->findBy(array('action' => $actions['ACTION_USER_PRIZECATEGORY']['action'],'user' => $user));
        if (count($existingEvents) == 0) {
            $event = new \AdfabReward\Entity\Event();
            $event->setAction($actions['ACTION_USER_PRIZECATEGORY']['action']);
            $event->setUser($user);
            $event->setPoints($actions['ACTION_USER_PRIZECATEGORY']['points']);
            $event->setLabel($actions['ACTION_USER_PRIZECATEGORY']['label']);
            $eventService->getEventMapper()->insert($event);
            $sm->get('jhu.zdt_logger')->info('Event prizecategory créé');
        }
    }

    /**
     * event inscription jeu + badge joueur
     * @param Event $e
     */
    public function playAfter(Event $e)
    {
        $game = $e->getParam('game');
        $user = $e->getParam('user');
        $sm = $e->getTarget()->getServiceManager();
        $eventService = $sm->get('adfabreward_event_service');
        $achievementService = $sm->get('adfabreward_achievement_service');
        $actionService = $sm->get('adfabreward_action_service');
        $actions = self::getActions($actionService);

        $event = new \AdfabReward\Entity\Event();
        $event->setAction($actions['ACTION_GAME_SUBSCRIBE']['action']);
        $event->setUser($user);
        $event->setPoints($actions['ACTION_GAME_SUBSCRIBE']['points']);
        $event->setLabel($game->getTitle());
        $eventService->getEventMapper()->insert($event);

        // $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, array('user' => $user, 'game' => $game));

        // badge player
        $level = 0;

        $existingEvents = $eventService->getEventMapper()->findBy(array('action' => $actions['ACTION_GAME_SUBSCRIBE']['action'],'user' => $user));

        if (count($existingEvents) == 10) {
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
            $achievement->setUser($user);
            $achievement->setType('badge');
            $achievement->setCategory('player');
            $achievement->setLevel($level);
            $achievement->setLevelLabel($levelLabel);
            $achievement->setLabel('Le joueur');
            $achievementService->getAchievementMapper()->insert($achievement);

            if ($level == 1) {
                $this->badgeBronzeAfter($sm, $user, 'Le joueur');
            }
            if ($level == 2) {
                $this->badgeSilverAfter($sm, $user, 'Le joueur');
            }
            if ($level == 3) {
                $this->badgeGoldAfter($sm, $user, 'Le joueur');
            }
        }

    }

    public function sendShareMailAfter(Event $e)
    {
        $topic = $e->getParam('topic');
        $user = $e->getParam('user');
        $secretKey = $e->getParam('secretKey');

        $sm = $e->getTarget()->getServiceManager();
        $eventService = $sm->get('adfabreward_event_service');
        $actionService = $sm->get('adfabreward_action_service');
        $actions = self::getActions($actionService);

        $event = new \AdfabReward\Entity\Event();
        $event->setAction($actions['ACTION_SOCIAL_SHAREMAIL']['action']);
        $event->setUser($user);
        $event->setPoints($actions['ACTION_SOCIAL_SHAREMAIL']['points']);
        $event->setLabel($topic);
        $event->setSecretKey($secretKey);
        $eventService->getEventMapper()->insert($event);

        // badge ambassador
        $this->badgeAmbassador($sm, $user);
    }

    public function postFbWallAfter(Event $e)
    {
        $topic = $e->getParam('topic');
        $user = $e->getParam('user');
        $secretKey = $e->getParam('secretKey');

        $sm = $e->getTarget()->getServiceManager();
        $eventService = $sm->get('adfabreward_event_service');
        $actions = self::$actions;

        $event = new \AdfabReward\Entity\Event();
        $event->setAction($actions['ACTION_SOCIAL_FBWALL']['action']);
        $event->setUser($user);
        $event->setPoints($actions['ACTION_SOCIAL_FBWALL']['points']);
        $event->setLabel($topic);
        $event->setSecretKey($secretKey);
        $eventService->getEventMapper()->insert($event);

        // badge ambassador
        $this->badgeAmbassador($sm, $user);

    }

    public function postTwitterAfter(Event $e)
    {
        $topic = $e->getParam('topic');
        $user = $e->getParam('user');
        $secretKey = $e->getParam('secretKey');

        $sm = $e->getTarget()->getServiceManager();
        $eventService = $sm->get('adfabreward_event_service');
        $actionService = $sm->get('adfabreward_action_service');
        $actions = self::getActions($actionService);

        $event = new \AdfabReward\Entity\Event();
        $event->setAction($actions['ACTION_SOCIAL_TWITTER']['action']);
        $event->setUser($user);
        $event->setPoints($actions['ACTION_SOCIAL_TWITTER']['points']);
        $event->setLabel($topic);
        $event->setSecretKey($secretKey);
        $eventService->getEventMapper()->insert($event);

        // badge ambassador
        $this->badgeAmbassador($sm, $user);
    }

    public function postGoogleAfter(Event $e)
    {
        $topic = $e->getParam('topic');
        $user = $e->getParam('user');
        $secretKey = $e->getParam('secretKey');

        $sm = $e->getTarget()->getServiceManager();
        $eventService = $sm->get('adfabreward_event_service');
        $actionService = $sm->get('adfabreward_action_service');
        $actions = self::getActions($actionService);

        $event = new \AdfabReward\Entity\Event();
        $event->setAction($actions['ACTION_SOCIAL_GOOGLE']['action']);
        $event->setUser($user);
        $event->setPoints($actions['ACTION_SOCIAL_GOOGLE']['points']);
        $event->setLabel($topic);
        $event->setSecretKey($secretKey);
        $eventService->getEventMapper()->insert($event);

        // badge ambassador
        $this->badgeAmbassador($sm, $user);

    }

    public function postFbRequestAfter(Event $e)
    {
        $game = $e->getParam('game');
        $user = $e->getParam('user');
        $secretKey = $e->getParam('secretKey');

        $sm = $e->getTarget()->getServiceManager();
        $eventService = $sm->get('adfabreward_event_service');
        $actionService = $sm->get('adfabreward_action_service');
        $actions = self::getActions($actionService);

        $event = new \AdfabReward\Entity\Event();
        $event->setAction($actions['ACTION_SOCIAL_FBFRIEND']['action']);
        $event->setUser($user);
        $event->setPoints($actions['ACTION_SOCIAL_FBFRIEND']['points']);
        $event->setLabel($game->getTitle());
        $event->setSecretKey($secretKey);
        $eventService->getEventMapper()->insert($event);

        // badge ambassador
        $this->badgeAmbassador($sm, $user);

    }

    public function createQuizReplyAfter(Event $e)
    {
        $data = $e->getParam('data');
        $game = $e->getParam('game');
        $user = $e->getParam('user');

        $sm = $e->getTarget()->getServiceManager();
        $eventService = $sm->get('adfabreward_event_service');
        $achievementService = $sm->get('adfabreward_achievement_service');
        $actionService = $sm->get('adfabreward_action_service');
        $actions = self::getActions($actionService);

        // badge brain
        $level = 0;

        $existingEvents = $eventService->getEventMapper()->findBy(array('action' => $actions['ACTION_QUIZ_CORRECTANSWERS']['action'],'user' => $user));

        // je crée autant d'events que de bonnes réponses
        for ($i=1;$i<=$data;$i++) {
            $event = new \AdfabReward\Entity\Event();
            $event->setAction($actions['ACTION_QUIZ_CORRECTANSWERS']['action']);
            $event->setUser($user);
            $event->setPoints($actions['ACTION_QUIZ_CORRECTANSWERS']['points']);
            $event->setLabel($game->getTitle());
            $eventService->getEventMapper()->insert($event);

            if (count($existingEvents)+$i == 25) {
                $level = 1;
                $levelLabel = 'BRONZE';
            }
            if (count($existingEvents)+$i == 150) {
                $level = 2;
                $levelLabel = 'SILVER';
            }
            if (count($existingEvents)+$i == 250) {
                $level = 3;
                $levelLabel = 'GOLD';
            }
        }

        if ($level > 0) {
            $achievement = new \AdfabReward\Entity\Achievement();
            $achievement->setUser($user);
            $achievement->setType('badge');
            $achievement->setCategory('brain');
            $achievement->setLevel($level);
            $achievement->setLevelLabel($levelLabel);
            $achievement->setLabel('Le cerveau');
            $achievementService->getAchievementMapper()->insert($achievement);

            if ($level == 1) {
                $this->badgeBronzeAfter($sm, $user, 'Le cerveau');
            }
            if ($level == 2) {
                $this->badgeSilverAfter($sm, $user, 'Le cerveau');
            }
            if ($level == 3) {
                $this->badgeGoldAfter($sm, $user, 'Le cerveau');
            }
        }
    }

    public function badgeBronzeAfter($sm, $user, $label)
    {
        $eventService = $sm->get('adfabreward_event_service');
        $actionService = $sm->get('adfabreward_action_service');
        $actions = self::getActions($actionService);

        $event = new \AdfabReward\Entity\Event();
        $event->setAction($actions['ACTION_BADGE_BRONZE']['action']);
        $event->setUser($user);
        $event->setPoints($actions['ACTION_BADGE_BRONZE']['points']);
        $event->setLabel($label);
        $eventService->getEventMapper()->insert($event);

        $this->sendBadgeMail($sm, $user, $label, 'bronze');
    }

    public function badgeSilverAfter($sm, $user, $label)
    {
        $eventService = $sm->get('adfabreward_event_service');
        $actionService = $sm->get('adfabreward_action_service');
        $actions = self::getActions($actionService);

        $event = new \AdfabReward\Entity\Event();
        $event->setAction($actions['ACTION_BADGE_SILVER']['action']);
        $event->setUser($user);
        $event->setPoints($actions['ACTION_BADGE_SILVER']['points']);
        $event->setLabel($label);
        $eventService->getEventMapper()->insert($event);

        $this->sendBadgeMail($sm, $user, $label, 'silver');
    }

    public function badgeGoldAfter($sm, $user, $label)
    {
        $eventService = $sm->get('adfabreward_event_service');
        $actionService = $sm->get('adfabreward_action_service');
        $actions = self::getActions($actionService);

        $event = new \AdfabReward\Entity\Event();
        $event->setAction($actions['ACTION_BADGE_GOLD']['action']);
        $event->setUser($user);
        $event->setPoints($actions['ACTION_BADGE_GOLD']['points']);
        $event->setLabel($label);
        $eventService->getEventMapper()->insert($event);

        $this->sendBadgeMail($sm, $user, $label, 'gold');
    }

    public function badgeAmbassador($sm, $user)
    {
        $eventService = $sm->get('adfabreward_event_service');
        $achievementService = $sm->get('adfabreward_achievement_service');
        $label = "L\'ambassadeur";

        // badge player
        $level = 0;

        $existingEvents = $eventService->getTotal($user,'social','count');

        if ($existingEvents == 5) {
            $level = 1;
            $levelLabel = 'BRONZE';
        }

        if ($existingEvents == 50) {
            $level = 2;
            $levelLabel = 'SILVER';
        }

        if ($existingEvents == 100) {
            $level = 3;
            $levelLabel = 'GOLD';
        }

        if ($level > 0) {
            $achievement = new \AdfabReward\Entity\Achievement();
            $achievement->setUser($user);
            $achievement->setType('badge');
            $achievement->setCategory('ambassador');
            $achievement->setLevel($level);
            $achievement->setLevelLabel($levelLabel);
            $achievement->setLabel($label);
            $achievementService->getAchievementMapper()->insert($achievement);

            if ($level == 1) {
                $this->badgeBronzeAfter($sm, $user, $label);
            }
            if ($level == 2) {
                $this->badgeSilverAfter($sm, $user, $label);
            }
            if ($level == 3) {
                $this->badgeGoldAfter($sm, $user, $label);
            }
        }
    }

    public function sendBadgeMail($sm, $user, $label, $level)
    {
        $renderer 	 = $sm->get('Zend\View\Renderer\RendererInterface');
        $skinUrl 	 = $renderer->url('home', array(), array('force_canonical' => true));
        $config 	 = $sm->get('config');
        $badge 		 = '';
        $userScore   = $sm->get('viewhelpermanager')->get('UserScore');

        $mailService = $sm->get('adfabuser_message');
        $to 		 = $user->getEmail();
        $subject 	 = "Obtention d'un badge";

        if (isset($config['contact']['email'])) {
            $from = $config['contact']['email'];
        }

        switch ($label) {
            case 'Le cerveau':
                $badge = 'brain';
                break;
            case 'Le joueur':
                $badge = 'player';
                break;
            case 'Le parrain':
                $badge = 'brain';
                break;
            case "L\'ambassadeur":
                $badge = 'ambassador';
                break;
        }

        $message = $mailService->createHtmlMessage($from, $to, $subject, 'adfab-reward/frontend/email/win_badge', array('score' => $userScore, 'badge' => $badge, 'level' => $level, 'skinUrl' => $skinUrl, 'label' => $label, 'user' => $user));
        $mailService->send($message);
    }
}
