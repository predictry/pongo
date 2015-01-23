<?php

use App\Pongo\Repository\SiteRepository;

/**
 * Author       : Rifki Yandhi
 * Date Created : Sep 26, 2014 3:33:25 PM
 * File         : SiteEventHandler.php
 * Copyright    : rifkiyandhi@gmail.com
 * Function     : 
 */
class SiteEventHandler
{

    private $repository;

    public function __construct(SiteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function subscribe($events)
    {
        $events->listen('site.set_default_actions', 'SiteEventHandler@setDefaultActions');
        $events->listen('site.set_default_funnel_preferences', 'SiteEventHandler@setDefaultFunnelPreferences');
    }

    public function setDefaultActions($site)
    {
        $this->repository->createDefaultActions($site->id);
    }

    public function setDefaultFunnelPreferences($site)
    {
        $this->repository->createDefaultFunnelPreferences($site->id);
    }

}

/* End of file SiteEventHandler.php */