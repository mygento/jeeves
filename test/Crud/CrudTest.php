<?php

namespace Crud;

use Mygento\Jeeves\Console\Command\ModelCrud;
use Symfony\Component\Console\Tester\CommandTester;

class CrudTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $application = new \Symfony\Component\Console\Application();
        $application->add(new ModelCrud());
        $command = $application->find('generate-model-crud');
        $this->commandTester = new CommandTester($command);
    }

    public function testCrudBasic()
    {
        $this->commandTester->execute([]);
        $this->checkInterfaces();
        $this->checkModels();
        $this->checkRepository();
        $this->checkGui();
        $this->checkXml('etc/di.xml');
        $this->checkXml('etc/webapi.xml');
        $this->checkXml('etc/module.xml');
        $this->checkFile('registration.php');
    }

    private function checkInterfaces()
    {
        $this->checkFile('Api/CustomerAddressRepositoryInterface.php');
        $this->checkFile('Api/Data/CustomerAddressInterface.php');
        $this->checkFile('Api/Data/CustomerAddressSearchResultsInterface.php');

        $this->checkFile('Api/BannerRepositoryInterface.php');
        $this->checkFile('Api/Data/BannerInterface.php');
        $this->checkFile('Api/Data/BannerSearchResultsInterface.php');
    }

    private function checkModels()
    {
        $this->checkFile('Model/CustomerAddress.php');
        $this->checkFile('Model/ResourceModel/CustomerAddress.php');
        $this->checkFile('Model/ResourceModel/CustomerAddress/Collection.php');

        $this->checkFile('Model/Banner.php');
        $this->checkFile('Model/ResourceModel/Banner.php');
        $this->checkFile('Model/ResourceModel/Banner/Collection.php');

        $this->checkFile('Model/ResourceModel/Banner/Relation/Store/ReadHandler.php');
        $this->checkFile('Model/ResourceModel/Banner/Relation/Store/SaveHandler.php');
    }

    private function checkRepository()
    {
        $this->checkFile('Model/CustomerAddressRepository.php');
        $this->checkFile('Model/BannerRepository.php');
        $this->checkFile('Model/SearchCriteria/BannerStoreFilter.php');
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
            \Mygento\Jeeves\Console\Application::GEN . '/' . $file,
            'test/Expectations/Crud/' . $file,
            '',
            false,
            false
        );
    }

    private function checkXml($file)
    {
        $this->assertXmlFileEqualsXmlFile(
            \Mygento\Jeeves\Console\Application::GEN . '/' . $file,
            'test/Expectations/Crud/' . $file
        );
        $this->assertFileEquals(
            \Mygento\Jeeves\Console\Application::GEN . '/' . $file,
            'test/Expectations/Crud/' . $file,
            '',
            false,
            false
        );
    }
}
