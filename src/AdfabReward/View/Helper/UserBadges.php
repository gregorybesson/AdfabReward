<?php

namespace AdfabReward\View\Helper;

use Zend\View\Helper\AbstractHelper;

class UserBadges extends AbstractHelper
{
    /**
     * @var AchievementService
     */
    protected $achievementService;
    protected $rewardService;
    protected $authService;

    /**
     * __invoke
     *
     * @access public
     * @param  array  $options array of options
     * @return string
     */
    public function __invoke($userId=0, $detail=false)
    {
        if ($userId == 0 && $this->getAuthService()->hasIdentity()) {
            $userId = $this->getAuthService()->getIdentity()->getId();
        }

        $badgesConfig = \AdfabReward\Service\AchievementListener::getBadges();
        $badges = array();

        if ($userId != 0) {
            if (! $detail) {
                $badges = $this->getAchievementService()->getBadges($userId);
            } else {
                foreach ($badgesConfig as $key=>$badgeConfig) {
                    $eventsDone        = 1 * $this->getRewardService()->getTotal($userId, $badgeConfig['event'], 'count');
                    $eventsToNextBadge = 0;
                    $nextBadge = '';
                    $badge = $this->getAchievementService()->getTopBadge($userId, strtolower($key));
                    $badges[$key]['badge'] = $badge;
                    if ($badge) {
                        if (isset($badgeConfig['levels'][$badge->getLevel()+1])) {
                            $eventsToNextBadge = $badgeConfig['levels'][$badge->getLevel()+1]['conditions'] - $eventsDone;
                            if ($eventsToNextBadge>1) {
                                $nextBadge = $badgeConfig['levels'][$badge->getLevel()+1]['label'] . ' : ' . $eventsToNextBadge . ' ' . $badgeConfig['units'];
                            } else {
                                $nextBadge = $badgeConfig['levels'][$badge->getLevel()+1]['label'] . ' : ' . $eventsToNextBadge . ' ' . $badgeConfig['unit'];
                            }

                        } else {
                            $nextBadge = 'Niveau max!';
                        }
                    } else {
                        $eventsToNextBadge = $badgeConfig['levels'][1]['conditions'] - $eventsDone;
                        if ($eventsToNextBadge>1) {
                            $nextBadge = $badgeConfig['levels'][1]['label'] . ' : ' . $eventsToNextBadge . ' ' . $badgeConfig['units'];
                        } else {
                            $nextBadge = $badgeConfig['levels'][1]['label'] . ' : ' . $eventsToNextBadge . ' ' . $badgeConfig['unit'];
                        }
                    }
                    $badges[$key]['eventsDoneCount'] = $eventsDone;
                    if ($eventsDone>1) {
                        $badges[$key]['eventsDone']        = $eventsDone . ' ' . $badgeConfig['units'];
                    } else {
                        $badges[$key]['eventsDone']        = $eventsDone . ' ' . $badgeConfig['unit'];
                    }

                    $badges[$key]['eventsToNextBadge'] = $eventsToNextBadge;
                    $badges[$key]['nextBadge']         = $nextBadge;
                }
            }
        }

        return $badges;
    }

    /**
     * Get achievementService.
     *
     * @return AchievementService
     */
    public function getAchievementService()
    {
        return $this->achievementService;
    }

    /**
     * Set achievementService.
     *
     * @param AuthenticationService $achievementService
     * @return
     */
    public function setAchievementService(\AdfabReward\Service\Achievement $achievementService)
    {
        $this->achievementService = $achievementService;

        return $this;
    }

    /**
     * getAuthService
     *
     * @return AuthenticationService
     */
    public function getAuthService()
    {
        return $this->authService;
    }

    /**
     * setAuthenticationService
     *
     * @param  AuthenticationService $authService
     * @return User
     */
    public function setAuthService($authService)
    {
        $this->authService = $authService;

        return $this;
    }

    public function getRewardService()
    {
        return $this->rewardService;
    }

    public function setRewardService(\AdfabReward\Service\Event $rewardService)
    {
        $this->rewardService = $rewardService;

        return $this;
    }
}
