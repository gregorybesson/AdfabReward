<?php

namespace AdfabReward\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use AdfabCore\ORM\Pagination\LargeTablePaginator as ORMPaginator;

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
        $search = $this->params()->fromQuery('name');
		
		$leaderboard = $this->getLeaderboardService()->getLeaderboardQuery($filter, ($period=='week')?'week':'', $search);
		
        $paginator = new Paginator(new DoctrineAdapter(new ORMPaginator($leaderboard)));
        $paginator->setItemCountPerPage(100);
        $paginator->setCurrentPageNumber($this->getEvent()->getRouteMatch()->getParam('p'));
		
        $viewModel = new ViewModel();

        return new ViewModel(
            array(
                'search' => $search,
                'period' => $period,
                'filter' => $filter,
                'leaderboard' => $paginator
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
