<?php

namespace Mygento\Jeeves\Model\Crud;

use Mygento\Jeeves\Generators\Crud\Configs\Acl;
use Mygento\Jeeves\Generators\Crud\Configs\AdminRoute;
use Mygento\Jeeves\Generators\Crud\Configs\DbSchema;
use Mygento\Jeeves\Generators\Crud\Configs\Dependency;
use Mygento\Jeeves\Generators\Crud\Configs\Event;
use Mygento\Jeeves\Generators\Crud\Configs\Menu;
use Mygento\Jeeves\Generators\Crud\Configs\Module;
use Mygento\Jeeves\IO\IOInterface;
use Mygento\Jeeves\Model\Generator;

class Configs extends Generator
{
    public function __construct(IOInterface $io)
    {
        $this->setIO($io);
    }

    public function generate(Result $result)
    {
        $this->genAdminAcl($result);
        $this->genAdminRoute($result);
        $this->genAdminMenu($result);
        $this->genDBSchema($result);
        $this->genEvents($result);
        $this->genDI($result);
        $this->genModuleXml($result);
    }

    private function genAdminRoute(Result $result)
    {
        if (empty($result->getAdminRoute())) {
            return;
        }

        $generator = new AdminRoute();

        $this->writeFile(
            $result->getPath() . '/etc/adminhtml/routes.xml',
            $generator->generateAdminRoutes($result->getAdminRoute())
        );
    }

    private function genAdminAcl(Result $result)
    {
        if (empty($result->getAclEntities())) {
            return;
        }

        $generator = new Acl();

        $this->writeFile(
            $result->getPath() . '/etc/acl.xml',
            $generator->generateAdminAcls($result->getAclEntities(), $result->getAclConfigs())
        );
    }

    private function genAdminMenu(Result $result)
    {
        if (empty($result->getMenu())) {
            return;
        }

        $generator = new Menu();

        $this->writeFile(
            $result->getPath() . '/etc/adminhtml/menu.xml',
            $generator->generateAdminMenu($result->getMenu())
        );
    }

    private function genDI(Result $result)
    {
        $generator = new Dependency();
        $this->writeFile(
            $result->getPath() . '/etc/di.xml',
            $generator->generateDI(
                $result->getDi()
            )
        );
    }

    private function genDBSchema(Result $result)
    {
        $generator = new DbSchema();

        $this->writeFile(
            $result->getPath() . '/etc/db_schema.xml',
            $generator->generateSchema($result->getDbSchema())
        );
    }

    private function genEvents(Result $result)
    {
        if (empty($result->getEvents())) {
            return;
        }
        $generator = new Event();

        $this->writeFile(
            $result->getPath() . '/etc/events.xml',
            $generator->generateEvents($result->getEvents())
        );
    }

    private function genModuleXml(Result $result)
    {
        $generator = new Module();
        $this->writeFile(
            $result->getPath() . '/etc/module.xml',
            $generator->generateModule($result->getModule())
        );
    }
}
