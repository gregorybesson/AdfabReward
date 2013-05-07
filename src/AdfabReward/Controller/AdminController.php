<?php

namespace AdfabReward\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use AdfabReward\Options\ModuleOptions;

class AdminController extends AbstractActionController
{
    protected $options, $actionMapper, $adminActionService;

    public function listAction()
    {
        $actionMapper = $this->getActionMapper();
        $actions = $actionMapper->findAll();
        if (is_array($actions)) {
            $paginator = new \Zend\Paginator\Paginator(new \Zend\Paginator\Adapter\ArrayAdapter($actions));
        } else {
            $paginator = $actions;
        }

        $paginator->setItemCountPerPage(100);
        $paginator->setCurrentPageNumber($this->getEvent()->getRouteMatch()->getParam('p'));

        return array(
            'actions' => $paginator,
            'actionlistElements' => $this->getOptions()->getActionListElements()
        );
    }

    public function createAction()
    {
        /*$form = $this->getServiceLocator()->get('adfabreward_createaction_form');
        $request = $this->getRequest();

        $action = false;
        if ($request->isPost()) {
            $action = $this->getAdminActionService()->create((array) $request->getPost());
        }

        if (!$action) {
            return array(
                'createActionForm' => $form
            );
        }

        $this->flashMessenger()->setNamespace('adfabreward')->addMessage('The action was created');

        return $this->redirect()->toRoute('zfcadmin/adfabrewardadmin/list');*/
    }

    public function editAction()
    {
        $actionId = $this->getEvent()->getRouteMatch()->getParam('actionId');
        $action = $this->getActionMapper()->findById($actionId);
        $form = $this->getServiceLocator()->get('adfabreward_editaction_form');
        $form->setAction($action);
        $request = $this->getRequest();

        if (!$request->isPost()) {
            $form->populateFromAction($action);

            return array(
                'editActionForm' => $form,
                'actionId' => $actionId
            );
        }

        $this->getAdminActionService()->edit(get_object_vars($request->getPost()), $action);

        $this->flashMessenger()->setNamespace('adfabreward')->addMessage('The action was edited');

        return $this->redirect()->toRoute('zfcadmin/adfabrewardadmin/list');
    }

    public function removeAction()
    {
        $actionId = $this->getEvent()->getRouteMatch()->getParam('actionId');
        $action = $this->getActionMapper()->findById($actionId);
        if ($action) {
            $this->getActionMapper()->remove($action);
            $this->flashMessenger()->setNamespace('adfabreward')->addMessage('The action was deleted');
        }

        return $this->redirect()->toRoute('zfcadmin/adfabrewardadmin/list');
    }

    public function setOptions(ModuleOptions $options)
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions()
    {
        if (!$this->options instanceof ModuleOptions) {
            $this->setOptions($this->getServiceLocator()->get('adfabreward_module_options'));
        }

        return $this->options;
    }

    public function getActionMapper()
    {
        if (null === $this->actionMapper) {
            $this->actionMapper = $this->getServiceLocator()->get('adfabreward_action_mapper');
        }

        return $this->actionMapper;
    }

    public function setActionMapper(ActionMapperInterface $actionMapper)
    {
        $this->actionMapper = $actionMapper;

        return $this;
    }

    public function getAdminActionService()
    {
        if (null === $this->adminActionService) {
            $this->adminActionService = $this->getServiceLocator()->get('adfabreward_action_service');
        }

        return $this->adminActionService;
    }

    public function setAdminActionService($service)
    {
        $this->adminActionService = $service;

        return $this;
    }
}
