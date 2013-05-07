<?php

namespace AdfabReward\View\Helper;

use Zend\View\Helper\AbstractHelper;

class RankWidget extends AbstractHelper
{
    /**
     * @var LeaderboardService
     */
    protected $leaderboardService;

    /**
     * __invoke
     *
     * @access public
     * @param  array  $options array of options
     * @return string
     */
    public function __invoke($userId = false, $timeScale='')
    {
        if ($userId) {
            return $this->getLeaderboardService()->getRank($userId, $timeScale);
        }

        return false;
    }

    /**
     * Get leaderboardService.
     *
     * @return LeaderboardService
     */
    public function getLeaderboardService()
    {
        return $this->leaderboardService;
    }

    /**
     * Set leaderboardService.
     *
     * @param AuthenticationService $leaderboardService
     * @return
     */
    public function setLeaderboardService(\AdfabReward\Service\Leaderboard $leaderboardService)
    {
        $this->leaderboardService = $leaderboardService;

        return $this;
    }
}
