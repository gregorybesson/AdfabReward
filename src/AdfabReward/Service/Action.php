<?php

namespace AdfabReward\Service;

use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\ServiceManager\ServiceManager;
use ZfcBase\EventManager\EventProvider;
use AdfabReward\Options\ModuleOptions;

class Action extends EventProvider implements ServiceManagerAwareInterface
{

    /**
     * @var ActionMapperInterface
     */
    protected $actionMapper;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var ActionServiceOptionsInterface
     */
    protected $options;

    public function create(array $data)
    {

    }

    public function edit(array $data, $action)
    {
        $this->getActionMapper()->update($action);
        $this->getEventManager()->trigger(__FUNCTION__, $this, array('action' => $action, 'data' => $data));
        $this->getActionMapper()->insert($action);
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, array('action' => $action, 'data' => $data));

        return $action;
    }

    /**
     * getActionMapper
     *
     * @return ActionMapperInterface
     */
    public function getActionMapper()
    {
        if (null === $this->actionMapper) {
            $this->actionMapper = $this->getServiceManager()->get('adfabreward_action_mapper');
        }

        return $this->actionMapper;
    }

    /**
     * setActionMapper
     *
     * @param  ActionMapperInterface $actionMapper
     * @return Action
     */
    public function setActionMapper(ActionMapperInterface $actionMapper)
    {
        $this->actionMapper = $actionMapper;

        return $this;
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
     * @param  ServiceManager $locator
     * @return Action
     */
    public function setServiceManager(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }
}
