<?php

namespace Crud;

use Mygento\Jeeves\Console\Application as App;
use Mygento\Jeeves\Console\Command\ModelCrud;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CrudV0Test extends \PHPUnit\Framework\TestCase
{
    private const GEN_PATH = App::GEN . App::DS . 'crud' . App::DS;
    private const V = 'v0';
    private const VARIANTS = ['7.4'];

    private CommandTester $commandTester;
    private string $path;

    protected function setUp(): void
    {
        $application = new Application();
        $application->add(new ModelCrud());
        $command = $application->find('generate-model-crud');
        $this->commandTester = new CommandTester($command);
    }

    public function testCrudV0()
    {
        foreach (self::VARIANTS as $v) {
            if (version_compare(PHP_VERSION, $v, '>=')) {
                $this->path = str_replace('.', '', $v) . App::DS . self::V;
                $this->checkEveryThing();
            }
        }
    }

    private function checkEveryThing()
    {
        $this->commandTester->execute([
            '--config_file' => '.jeeves.phpunit_' . self::V . '.yaml',
            '--path' => self::GEN_PATH . $this->path,
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

    private function checkSearchResults()
    {
        $this->checkFile('Model/BannerSearchResults.php');
        $this->checkFile('Model/CustomerAddressSearchResults.php');
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
            'test/Expectations/Crud/' . $this->path . '/' . $file,
            self::GEN_PATH . $this->path . '/' . $file,
            '',
            false,
            false
        );
    }

    private function checkXml($file)
    {
        $this->assertXmlFileEqualsXmlFile(
            'test/Expectations/Crud/' . $this->path . '/' . $file,
            self::GEN_PATH . $this->path . '/' . $file,
        );
        $this->assertFileEquals(
            'test/Expectations/Crud/' . $this->path . '/' . $file,
            self::GEN_PATH . $this->path . '/' . $file,
            '',
            false,
            false
        );
    }
}
