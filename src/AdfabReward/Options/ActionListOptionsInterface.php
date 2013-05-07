<?php

namespace AdfabReward\Options;

interface ActionListOptionsInterface
{
    public function getActionListElements();

    public function setActionListElements(array $elements);

    /**
     * set action entity class name
     *
     * @param  string        $actionEntityClass
     * @return ModuleOptions
     */
    public function setActionEntityClass($userEntityClass);

    /**
     * get action entity class name
     *
     * @return string
     */
    public function getActionEntityClass();
}
