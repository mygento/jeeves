<?php

namespace Mygento\Jeeves\Model\Crud;

class Result
{
    private $events = [];

    private $di = [];

    private $acl = [];

    private $db = [];

    private $menu = [];

    private $path;

    public function getEvents(): array
    {
        return $this->events;
    }

    public function getDi(): array
    {
        return $this->di;
    }

    public function getAcl(): array
    {
        return $this->acl;
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

    public function updateAcl(array $acl)
    {
        $this->acl = array_merge($this->acl, $acl);
    }

    public function updateDbSchema(array $db)
    {
        $this->db = array_merge($this->db, $db);
    }

    public function updateMenu(array $menu)
    {
        $this->menu = array_merge($this->menu, $menu);
    }
}
