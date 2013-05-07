<?php

namespace AdfabReward;

use Zend\Mvc\MvcEvent;
use Zend\Validator\AbstractValidator;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $application     = $e->getTarget();
        $serviceManager  = $application->getServiceManager();
        $eventManager    = $application->getEventManager();

        $translator = $serviceManager->get('translator');

        AbstractValidator::setDefaultTranslator($translator,'adfabcore');

        $eventManager->attach($serviceManager->get('adfabreward_event_listener'));
        $eventManager->attach($serviceManager->get('adfabreward_achievement_listener'));

        // I can post cron tasks to be scheduled by the core cron service
        $eventManager->getSharedManager()->attach('Zend\Mvc\Application','getCronjobs', array($this, 'addCronjob'));
    }

    /**
     * This method get the cron config for this module an add them to the listener
     * TODO : déporter la def des cron dans la config.
     *
     * @param  EventManager $e
     * @return array
     */
    public function addCronjob($e)
    {

        $cronjobs = $e->getParam('cronjobs');

        // This cron job is scheduled everyday @ 2AM en disable user in state 0 since 'period' (7 days here)
        $cronjobs['adfabreward_anniversary'] = array(
            'frequency' => '0 2 * * *',
            'callback'  => '\AdfabReward\Service\Cron::badgeAnniversary',
            'args'      => array()
        );

        return $cronjobs;
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/../../src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'aliases' => array(
                    'adfabreward_doctrine_em' => 'doctrine.entitymanager.orm_default',
            ),

            'invokables' => array(
                    'adfabreward_action_service'       => 'AdfabReward\Service\Action',
                    'adfabreward_event_service'        => 'AdfabReward\Service\Event',
                    'adfabreward_event_listener'       => 'AdfabReward\Service\EventListener',
                    'adfabreward_achievement_service'  => 'AdfabReward\Service\Achievement',
                    'adfabreward_achievement_listener' => 'AdfabReward\Service\AchievementListener',
                    'adfabreward_leaderboard_service'  => 'AdfabReward\Service\Leaderboard',
                    'adfabreward_cron_service'         => 'AdfabReward\Service\Cron',
               ),

            'factories' => array(
                'adfabreward_module_options' => function ($sm) {
                    $config = $sm->get('Configuration');

                    return new Options\ModuleOptions(isset($config['adfabreward']) ? $config['adfabreward'] : array());
                },
                'adfabreward_event_mapper' => function ($sm) {
                return new \AdfabReward\Mapper\Event(
                        $sm->get('adfabreward_doctrine_em'),
                        $sm->get('adfabreward_module_options')
                );
                },
                'adfabreward_action_mapper' => function ($sm) {
                    return new \AdfabReward\Mapper\Action(
                        $sm->get('adfabreward_doctrine_em'),
                        $sm->get('adfabreward_module_options')
                    );
                },
                'adfabreward_achievement_mapper' => function ($sm) {
                    return new \AdfabReward\Mapper\Achievement(
                        $sm->get('adfabreward_doctrine_em'),
                        $sm->get('adfabreward_module_options')
                    );
                },
                'adfabreward_editaction_form' => function($sm) {
                    $options = $sm->get('adfabreward_module_options');
                    $form = new Form\EditAction(null, $options, $sm);

                    return $form;
                },
            ),
        );
    }

    public function getViewHelperConfig()
    {
        return array(
            'factories' => array(
                'userScore' => function ($sm) {
                    $locator = $sm->getServiceLocator();
                    $viewHelper = new View\Helper\UserScore;
                    $viewHelper->setEventService($locator->get('adfabreward_event_service'));
                    $viewHelper->setAuthService($locator->get('zfcuser_auth_service'));

                    return $viewHelper;
                },
                'userBadges' => function ($sm) {
                    $locator = $sm->getServiceLocator();
                    $viewHelper = new View\Helper\UserBadges;
                    $viewHelper->setAchievementService($locator->get('adfabreward_achievement_service'));
                    $viewHelper->setAuthService($locator->get('zfcuser_auth_service'));
                    $viewHelper->setRewardService($locator->get('adfabreward_event_service'));
                    //$viewHelper->setAchievementListener($locator->get('adfabreward_achievement_listener'));
                    return $viewHelper;
                },
                'activityWidget' => function ($sm) {
                    $locator = $sm->getServiceLocator();
                    $viewHelper = new View\Helper\ActivityWidget;
                    $viewHelper->setAchievementService($locator->get('adfabreward_achievement_service'));

                    return $viewHelper;
                },
                'leaderboardWidget' => function ($sm) {
                    $locator = $sm->getServiceLocator();
                    $viewHelper = new View\Helper\LeaderboardWidget;
                    $viewHelper->setLeaderboardService($locator->get('adfabreward_leaderboard_service'));

                    return $viewHelper;
                },
                'rankWidget' => function ($sm) {
                    $locator = $sm->getServiceLocator();
                    $viewHelper = new View\Helper\RankWidget;
                    $viewHelper->setLeaderboardService($locator->get('adfabreward_leaderboard_service'));

                    return $viewHelper;
                },
            ),
        );
    }
}
