<?php

namespace AdfabReward\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    /**
     *
     */
    protected $options;

    /**
     * @var actionMapper
     */
    protected $actionMapper;

    /**
     * @var adminActionService
     */
    protected $adminActionService;

    /**
     * @var leaderboardService
     */
    protected $leaderboardService;

    public function leaderboardAction()
    {
        $filter = $this->getEvent()->getRouteMatch()->getParam('filter');
        $period = $this->getEvent()->getRouteMatch()->getParam('period');
        $search = '';
        if ($period=='week') {
            $leaderboard = $this->getLeaderboardService()->getLeaderboard($filter, 'week', $search, 49);
        } else {
            $leaderboard = $this->getLeaderboardService()->getLeaderboard($filter, '', $search, 49);
        }

        $viewModel = new ViewModel();

        return new ViewModel(
            array(
                'search' => $search,
                'period' => $period,
                'filter' => $filter,
                'leaderboard' => $leaderboard
            )
        );
    }

    public function getLeaderboardService()
    {
        if (!$this->leaderboardService) {
            $this->leaderboardService = $this->getServiceLocator()->get('adfabreward_leaderboard_service');
        }

        return $this->leaderboardService;
    }

    public function setLeaderboardService($leaderboardService)
    {
        $this->leaderboardService = $leaderboardService;

        return $this;
    }
}
