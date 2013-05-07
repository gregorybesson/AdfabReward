<?php

namespace AdfabReward\View\Helper;

use AdfabReward\Service\Event;

use Zend\View\Helper\AbstractHelper;

class UserScore extends AbstractHelper
{
    /**
     * @var EventService
     */
    protected $eventService;

    /**
     * __invoke
     *
     * @access public
     * @param  array  $options array of options
     * @return string
     */
    public function __invoke($options = array())
    {
        return $this->getEventService()->getTotal($this->getAuthService()->getIdentity());
    }

    /**
     * Get eventService.
     *
     * @return EventService
     */
    public function getEventService()
    {
        return $this->eventService;
    }

    /**
     * Set eventService.
     *
     * @param AuthenticationService $eventService
     * @return
     */
    public function setEventService(\AdfabReward\Service\Event $eventService)
    {
        $this->eventService = $eventService;

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
}
