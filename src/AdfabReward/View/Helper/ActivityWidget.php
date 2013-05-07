<?php

namespace AdfabReward\View\Helper;

use Zend\View\Helper\AbstractHelper;

class ActivityWidget extends AbstractHelper
{
    /**
     * @var AchievementService
     */
    protected $achievementService;

    /**
     * __invoke
     *
     * @access public
     * @param  array  $options array of options
     * @return string
     */
    public function __invoke($options = array())
    {
        return $this->getAchievementService()->getLastBadgesActivity(5);
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
}
