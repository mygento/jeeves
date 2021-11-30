<?php

namespace Mygento\Jeeves\Model\Crud;

use Mygento\Jeeves\Generators\Crud\Configs\Acl;
use Mygento\Jeeves\Generators\Crud\Configs\AdminRoute;
use Mygento\Jeeves\Generators\Crud\Configs\DbSchema;
use Mygento\Jeeves\Generators\Crud\Configs\Menu;
use Mygento\Jeeves\IO\IOInterface;
use Mygento\Jeeves\Model\Generator;
use Mygento\Jeeves\Util\XmlManager;

class Configs extends Generator
{
    /**
     * @var XmlManager
     */
    private $xmlManager;

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
//        $this->genEvents($result->getEvents());
//        $this->genDI($result->getDi());
//        $this->genModuleXml();
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

    private function genDI()
    {
        $this->writeFile(
            $this->path . '/etc/di.xml',
            $this->getXmlManager()->generateDI(
                $this->guiList,
                $entities,
                $this->getNamespace(),
                $this->module
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

    private function genEvents()
    {
        if (count($events) > 0) {
            $this->writeFile(
                $this->path . '/etc/events.xml',
                $this->getXmlManager()->generateEvents($events)
            );
        }
    }

    private function genModuleXml()
    {
        $this->writeFile(
            $this->path . '/etc/module.xml',
            $this->getXmlManager()->generateModule($this->getFullname())
        );
    }

    private function getXmlManager()
    {
        if (null === $this->xmlManager) {
            $this->xmlManager = new XmlManager();
        }

        return $this->xmlManager;
    }
}
