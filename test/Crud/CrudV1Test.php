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
        if (\PHP_VERSION_ID >= 70400) {
            $this->checkFile('Api/ColumnsRepositoryInterface.php');
            $this->checkFile('Api/Data/ColumnsInterface.php');
            $this->checkFile('Api/Data/ColumnsSearchResultsInterface.php');

            $this->checkFile('Api/CartItemRepositoryInterface.php');
            $this->checkFile('Api/Data/CartItemInterface.php');
            $this->checkFile('Api/Data/CartItemSearchResultsInterface.php');

            $this->checkFile('Api/PosterRepositoryInterface.php');
            $this->checkFile('Api/Data/PosterInterface.php');
            $this->checkFile('Api/Data/PosterSearchResultsInterface.php');
        }

        $this->checkFile('Api/ObsoleteRepositoryInterface.php');
        $this->checkFile('Api/Data/ObsoleteInterface.php');
        $this->checkFile('Api/Data/ObsoleteSearchResultsInterface.php');
    }

    private function checkModels()
    {
        if (\PHP_VERSION_ID >= 70400) {
            $this->checkFile('Model/Columns.php');
            $this->checkFile('Model/ResourceModel/Columns.php');
            $this->checkFile('Model/ResourceModel/Columns/Collection.php');

            $this->checkFile('Model/CartItem.php');
            $this->checkFile('Model/ResourceModel/CartItem.php');
            $this->checkFile('Model/ResourceModel/CartItem/Collection.php');

            $this->checkFile('Model/Poster.php');
            $this->checkFile('Model/ResourceModel/Poster.php');
            $this->checkFile('Model/ResourceModel/Poster/Collection.php');
        }

        $this->checkFile('Model/Obsolete.php');
        $this->checkFile('Model/ResourceModel/Obsolete.php');
        $this->checkFile('Model/ResourceModel/Obsolete/Collection.php');
    }

    private function checkRepository()
    {
        if (\PHP_VERSION_ID >= 70400) {
            $this->checkFile('Model/ColumnsRepository.php');
            $this->checkFile('Model/CartItemRepository.php');
            $this->checkFile('Model/ObsoleteRepository.php');
            $this->checkFile('Model/PosterRepository.php');
        }
    }

    private function checkSearchResults()
    {
        if (\PHP_VERSION_ID >= 70400) {
            $this->checkFile('Model/ColumnsSearchResults.php');
            $this->checkFile('Model/CartItemSearchResults.php');
            $this->checkFile('Model/ObsoleteSearchResults.php');
            $this->checkFile('Model/PosterSearchResults.php');
        }
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
        if (\PHP_VERSION_ID >= 70400) {
            $this->checkFile('Controller/Adminhtml/Columns.php');
            $this->checkFile('Controller/Adminhtml/Columns/Index.php');
            $this->checkFile('Controller/Adminhtml/Columns/Delete.php');
            $this->checkFile('Controller/Adminhtml/Columns/Edit.php');
            $this->checkFile('Controller/Adminhtml/Columns/InlineEdit.php');
            $this->checkFile('Controller/Adminhtml/Columns/MassDelete.php');
            $this->checkFile('Controller/Adminhtml/Columns/NewAction.php');
            $this->checkFile('Controller/Adminhtml/Columns/Save.php');

            $this->checkFile('Controller/Adminhtml/CartItem.php');
            $this->checkFile('Controller/Adminhtml/CartItem/Index.php');
            $this->checkFile('Controller/Adminhtml/CartItem/Delete.php');
            $this->checkFile('Controller/Adminhtml/CartItem/Edit.php');
            $this->checkFile('Controller/Adminhtml/CartItem/InlineEdit.php');
            $this->checkFile('Controller/Adminhtml/CartItem/MassDelete.php');
            $this->checkFile('Controller/Adminhtml/CartItem/NewAction.php');
            $this->checkFile('Controller/Adminhtml/CartItem/Save.php');

            $this->checkFile('Controller/Adminhtml/Poster.php');
            $this->checkFile('Controller/Adminhtml/Poster/Index.php');
            $this->checkNoFile('Controller/Adminhtml/Poster/Delete.php');
            $this->checkNoFile('Controller/Adminhtml/Poster/Edit.php');
            $this->checkNoFile('Controller/Adminhtml/Poster/InlineEdit.php');
            $this->checkNoFile('Controller/Adminhtml/Poster/MassDelete.php');
            $this->checkNoFile('Controller/Adminhtml/Poster/NewAction.php');
            $this->checkNoFile('Controller/Adminhtml/Poster/Save.php');
        }

        $this->checkFile('Controller/Adminhtml/Obsolete.php');
        $this->checkFile('Controller/Adminhtml/Obsolete/Index.php');
        $this->checkFile('Controller/Adminhtml/Obsolete/Delete.php');
        $this->checkFile('Controller/Adminhtml/Obsolete/Edit.php');
        $this->checkFile('Controller/Adminhtml/Obsolete/InlineEdit.php');
        $this->checkFile('Controller/Adminhtml/Obsolete/MassDelete.php');
        $this->checkFile('Controller/Adminhtml/Obsolete/NewAction.php');
        $this->checkFile('Controller/Adminhtml/Obsolete/Save.php');
    }

    private function checkLayout()
    {
        $this->checkXml('view/adminhtml/layout/sample_module_columns_index.xml');
        $this->checkXml('view/adminhtml/layout/sample_module_columns_new.xml');
        $this->checkXml('view/adminhtml/layout/sample_module_columns_edit.xml');

        $this->checkXml('view/adminhtml/layout/sample_module_cartitem_index.xml');
        $this->checkXml('view/adminhtml/layout/sample_module_cartitem_new.xml');
        $this->checkXml('view/adminhtml/layout/sample_module_cartitem_edit.xml');

        $this->checkXml('view/adminhtml/layout/sample_module_obsolete_index.xml');
        $this->checkXml('view/adminhtml/layout/sample_module_obsolete_new.xml');
        $this->checkXml('view/adminhtml/layout/sample_module_obsolete_edit.xml');

        $this->checkXml('view/adminhtml/layout/sample_module_poster_index.xml');
        $this->checkNoFile('view/adminhtml/layout/sample_module_poster_new.xml');
        $this->checkNoFile('view/adminhtml/layout/sample_module_poster_edit.xml');
    }

    private function checkUi()
    {
        if (\PHP_VERSION_ID >= 70400) {
            $this->checkFile('Ui/Component/Listing/ColumnsActions.php');
            $this->checkFile('Model/Columns/DataProvider.php');
            $this->checkFile('Model/ResourceModel/Columns/Grid/Collection.php');
        }
        $this->checkXml('view/adminhtml/ui_component/sample_module_columns_listing.xml');
        $this->checkXml('view/adminhtml/ui_component/sample_module_columns_edit.xml');

        if (\PHP_VERSION_ID >= 70400) {
            $this->checkFile('Ui/Component/Listing/CartItemActions.php');
            $this->checkFile('Model/CartItem/DataProvider.php');
            $this->checkFile('Model/ResourceModel/CartItem/Grid/Collection.php');
        }
        $this->checkXml('view/adminhtml/ui_component/sample_module_cartitem_listing.xml');
        $this->checkXml('view/adminhtml/ui_component/sample_module_cartitem_edit.xml');

        if (\PHP_VERSION_ID >= 70400) {
            $this->checkNoFile('Ui/Component/Listing/PosterActions.php');
            $this->checkFile('Model/Poster/DataProvider.php');
            $this->checkFile('Model/ResourceModel/Poster/Grid/Collection.php');
        }
        $this->checkXml('view/adminhtml/ui_component/sample_module_poster_listing.xml');
        $this->checkNoFile('view/adminhtml/ui_component/sample_module_poster_edit.xml');

        $this->checkFile('Ui/Component/Listing/ObsoleteActions.php');
        $this->checkFile('Model/Obsolete/DataProvider.php');
        $this->checkFile('Model/ResourceModel/Obsolete/Grid/Collection.php');
        $this->checkXml('view/adminhtml/ui_component/sample_module_obsolete_listing.xml');
        $this->checkXml('view/adminhtml/ui_component/sample_module_obsolete_edit.xml');
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

    private function checkNoFile($file)
    {
        $this->assertFileDoesNotExist($this->path . '/' . $file);
    }
}
