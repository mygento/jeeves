<?php

namespace Mygento\Jeeves\Model\Crud;

class Result
{
    private $events = [];

    private $di = [];

    private $aclEntity = [];

    private $aclConfig = [];

    private $db = [];

    private $menu = [];

    private $adminRoute = [];

    private $path;

    public function getEvents(): array
    {
        return $this->events;
    }

    public function getDi(): array
    {
        return $this->di;
    }

    public function getAclEntities(): array
    {
        return $this->aclEntity;
    }

    public function getAclConfigs(): array
    {
        return $this->aclConfig;
    }

    public function getDbSchema(): array
    {
        return $this->db;
    }

    public function getMenu(): array
    {
        return $this->menu;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getAdminRoute(): array
    {
        return $this->adminRoute;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function updateEvents(array $events)
    {
        $this->events = array_merge($this->events, $events);
    }

    public function updateDi(array $di)
    {
        $this->di = array_merge($this->di, $di);
    }

    public function updateAclConfigs(array $acl)
    {
        $this->aclConfig = array_merge($this->aclConfig, $acl);
    }

    public function updateAclEntities(array $acl)
    {
        $this->aclEntity = array_merge($this->aclEntity, $acl);
    }

    public function updateDbSchema(array $db)
    {
        $this->db = array_merge($this->db, $db);
    }

    public function updateMenu(array $menu)
    {
        $this->menu = array_merge($this->menu, $menu);
    }

    public function updateAdminRoute(array $route)
    {
        $this->adminRoute = array_merge($this->adminRoute, $route);
    }
}
