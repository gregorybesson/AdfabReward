<?php

namespace AdfabReward\Options;

class ModuleOptions implements
    ActionListOptionsInterface
{
    /**
     * @var string
     */
    protected $actionEntityClass = 'AdfabReward\Entity\Action';

    /**
     * @var bool
     */
    protected $enableDefaultEntities = true;

    /**
     * Turn off strict options mode
     */
    protected $__strictMode__ = false;

    /**
     * @TODO: change "things" below
     * Array of "things" to show in the action list
     */
    protected $actionListElements = array('Id' => 'id', 'Name' => 'name', 'Subject' => 'subject', 'Verb' => 'verb', 'Target' => 'complement');

    protected $actionMapper = 'AdfabReward\Mapper\Action';

    public function setUserMapper($actionMapper)
    {
        $this->actionMapper = $actionMapper;
    }

    public function getUserMapper()
    {
        return $this->actionMapper;
    }

    public function setActionListElements(array $listElements)
    {
        $this->actionListElements = $listElements;
    }

    public function getActionListElements()
    {
        return $this->actionListElements;
    }

    /**
     * set user entity class name
     *
     * @param  string        $userEntityClass
     * @return ModuleOptions
     */
    public function setActionEntityClass($actionEntityClass)
    {
        $this->actionEntityClass = $actionEntityClass;

        return $this;
    }

    /**
     * get user entity class name
     *
     * @return string
     */
    public function getActionEntityClass()
    {
        return $this->actionEntityClass;
    }

    /**
     * @param boolean $enableDefaultEntities
     */
    public function setEnableDefaultEntities($enableDefaultEntities)
    {
        $this->enableDefaultEntities = $enableDefaultEntities;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getEnableDefaultEntities()
    {
        return $this->enableDefaultEntities;
    }
}
