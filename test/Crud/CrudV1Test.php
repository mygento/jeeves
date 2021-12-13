<?php

namespace Crud;

use Mygento\Jeeves\Console\Application as App;
use Mygento\Jeeves\Console\Command\ModelCrud;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CrudV1Test extends \PHPUnit\Framework\TestCase
{
    private const V = 'v1';

    private $commandTester;

    private $path;

    protected function setUp(): void
    {
        $application = new Application();
        $application->add(new ModelCrud());
        $command = $application->find('generate-model-crud');
        $this->commandTester = new CommandTester($command);
        $this->path = App::GEN . DIRECTORY_SEPARATOR . self::V;
    }

    public function testCrudV1()
    {
        $this->commandTester->execute([
            '--config_file' => '.jeeves.phpunit_' . self::V . '.yaml',
            '--path' => $this->path,
        ]);
        $this->checkInterfaces();
        $this->checkModels();
        $this->checkRepository();
        $this->checkSearchResults();
        $this->checkGui();
        $this->checkXml('etc/di.xml');
        $this->checkXml('etc/webapi.xml');
        $this->checkXml('etc/events.xml');
        $this->checkXml('etc/module.xml');
        $this->checkFile('registration.php');
    }

    private function checkInterfaces()
    {
        $this->checkFile('Api/ColumnsRepositoryInterface.php');
        $this->checkFile('Api/Data/ColumnsInterface.php');
        $this->checkFile('Api/Data/ColumnsSearchResultsInterface.php');
    }

    private function checkModels()
    {
        $this->checkFile('Model/Columns.php');
        $this->checkFile('Model/ResourceModel/Columns.php');
        $this->checkFile('Model/ResourceModel/Columns/Collection.php');
    }

    private function checkRepository()
    {
        $this->checkFile('Model/ColumnsRepository.php');
    }

    private function checkSearchResults()
    {
        $this->checkFile('Model/ColumnsSearchResults.php');
    }

    private function checkGui()
    {
        $this->checkControllers();
        $this->checkLayout();
        $this->checkUi();
        $this->checkXml('etc/acl.xml');
        $this->checkXml('etc/adminhtml/routes.xml');
        $this->checkXml('etc/adminhtml/menu.xml');
        $this->checkXml('etc/db_schema.xml');
    }

    private function checkControllers()
    {
        $this->checkFile('Controller/Adminhtml/CustomerAddress.php');
        $this->checkFile('Controller/Adminhtml/CustomerAddress/Delete.php');
        $this->checkFile('Controller/Adminhtml/CustomerAddress/Edit.php');
        $this->checkFile('Controller/Adminhtml/CustomerAddress/Index.php');
        $this->checkFile('Controller/Adminhtml/CustomerAddress/InlineEdit.php');
        $this->checkFile('Controller/Adminhtml/CustomerAddress/MassDelete.php');
        $this->checkFile('Controller/Adminhtml/CustomerAddress/NewAction.php');
        $this->checkFile('Controller/Adminhtml/CustomerAddress/Save.php');

        $this->checkFile('Controller/Adminhtml/Banner.php');
        $this->checkFile('Controller/Adminhtml/Banner/Index.php');
    }

    private function checkLayout()
    {
        $this->checkXml('view/adminhtml/layout/sample_module_customeraddress_edit.xml');
        $this->checkXml('view/adminhtml/layout/sample_module_customeraddress_index.xml');
        $this->checkXml('view/adminhtml/layout/sample_module_customeraddress_new.xml');

        $this->checkXml('view/adminhtml/layout/sample_module_banner_index.xml');
    }

    private function checkUi()
    {
        $this->checkFile('Ui/Component/Listing/CustomerAddressActions.php');
        $this->checkFile('Model/CustomerAddress/DataProvider.php');
        $this->checkXml('view/adminhtml/ui_component/sample_module_customeraddress_listing.xml');
        $this->checkXml('view/adminhtml/ui_component/sample_module_customeraddress_edit.xml');
        $this->checkFile('Model/ResourceModel/CustomerAddress/Grid/Collection.php');

        $this->checkFile('Model/Banner/DataProvider.php');
        $this->checkXml('view/adminhtml/ui_component/sample_module_banner_listing.xml');
        $this->checkFile('Model/ResourceModel/Banner/Grid/Collection.php');
    }

    private function checkFile($file)
    {
        $this->assertFileEquals(
            'test/Expectations/Crud/' . self::V . '/' . $file,
            $this->path . '/' . $file,
            '',
            false,
            false
        );
    }

    private function checkXml($file)
    {
        $this->assertXmlFileEqualsXmlFile(
            'test/Expectations/Crud/' . self::V . '/' . $file,
            $this->path . '/' . $file,
        );
        $this->assertFileEquals(
            'test/Expectations/Crud/' . self::V . '/' . $file,
            $this->path . '/' . $file,
            '',
            false,
            false
        );
    }
}
