<?php

namespace Crud;

use Mygento\Jeeves\Console\Application as App;
use Mygento\Jeeves\Console\Command\ModelCrud;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CrudV1Test extends \PHPUnit\Framework\TestCase
{
    private const GEN_PATH = App::GEN . App::DS . 'crud' . App::DS;
    private const V = 'v1';
    private const VARIANTS = ['7.4', '8.1', '8.2'];

    private CommandTester $commandTester;
    private string $path;

    protected function setUp(): void
    {
        $application = new Application();
        $application->add(new ModelCrud());
        $command = $application->find('generate-model-crud');
        $this->commandTester = new CommandTester($command);
    }

    public function provider(): array
    {
        $variants = [];
        foreach (self::VARIANTS as $v) {
            list($v1, $v2) = explode('.', $v);
            if (version_compare(PHP_VERSION, $v, '>=') && version_compare(PHP_VERSION, $v1 . '.' . ($v2 + 1), '<')) {
                $variants[] = [str_replace('.', '', $v) . App::DS . self::V];
            }
        }

        return $variants;
    }

    /**
     * @dataProvider provider
     */
    public function testCrudV1(string $path)
    {
        $this->path = $path;
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
        $this->checkFile('Api/ColumnsRepositoryInterface.php');
        $this->checkFile('Api/Data/ColumnsInterface.php');
        $this->checkFile('Api/Data/ColumnsSearchResultsInterface.php');

        $this->checkFile('Api/CartItemRepositoryInterface.php');
        $this->checkFile('Api/Data/CartItemInterface.php');
        $this->checkFile('Api/Data/CartItemSearchResultsInterface.php');

        $this->checkFile('Api/PosterRepositoryInterface.php');
        $this->checkFile('Api/Data/PosterInterface.php');
        $this->checkFile('Api/Data/PosterSearchResultsInterface.php');

        $this->checkFile('Api/CardRepositoryInterface.php');
        $this->checkFile('Api/Data/CardInterface.php');
        $this->checkFile('Api/Data/CardSearchResultsInterface.php');

        $this->checkFile('Api/TicketRepositoryInterface.php');
        $this->checkFile('Api/Data/TicketInterface.php');
        $this->checkFile('Api/Data/TicketSearchResultsInterface.php');

        $this->checkFile('Api/ObsoleteRepositoryInterface.php');
        $this->checkFile('Api/Data/ObsoleteInterface.php');
        $this->checkFile('Api/Data/ObsoleteSearchResultsInterface.php');
    }

    private function checkModels()
    {
        $this->checkFile('Model/Columns.php');
        $this->checkFile('Model/ResourceModel/Columns.php');
        $this->checkFile('Model/ResourceModel/Columns/Collection.php');

        $this->checkFile('Model/CartItem.php');
        $this->checkFile('Model/ResourceModel/CartItem.php');
        $this->checkFile('Model/ResourceModel/CartItem/Collection.php');

        $this->checkFile('Model/Poster.php');
        $this->checkFile('Model/ResourceModel/Poster.php');
        $this->checkFile('Model/ResourceModel/Poster/Collection.php');

        $this->checkFile('Model/Card.php');
        $this->checkFile('Model/ResourceModel/Card.php');
        $this->checkFile('Model/ResourceModel/Card/Collection.php');

        $this->checkFile('Model/ResourceModel/Card/Relation/Store/ReadHandler.php');
        $this->checkFile('Model/ResourceModel/Card/Relation/Store/SaveHandler.php');

        $this->checkFile('Model/Ticket.php');
        $this->checkFile('Model/ResourceModel/Ticket.php');
        $this->checkFile('Model/ResourceModel/Ticket/Collection.php');

        $this->checkFile('Model/Obsolete.php');
        $this->checkFile('Model/ResourceModel/Obsolete.php');
        $this->checkFile('Model/ResourceModel/Obsolete/Collection.php');
    }

    private function checkRepository()
    {
        $this->checkFile('Model/ColumnsRepository.php');
        $this->checkFile('Model/CartItemRepository.php');
        $this->checkFile('Model/ObsoleteRepository.php');
        $this->checkFile('Model/PosterRepository.php');
        $this->checkFile('Model/TicketRepository.php');
        $this->checkFile('Model/CardRepository.php');

        $this->checkFile('Model/SearchCriteria/CardStoreFilter.php');
    }

    private function checkSearchResults()
    {
        $this->checkFile('Model/ColumnsSearchResults.php');
        $this->checkFile('Model/CartItemSearchResults.php');
        $this->checkFile('Model/ObsoleteSearchResults.php');
        $this->checkFile('Model/PosterSearchResults.php');
        $this->checkFile('Model/TicketSearchResults.php');
        $this->checkFile('Model/CardSearchResults.php');
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

        $this->checkFile('Controller/Adminhtml/Card.php');
        $this->checkFile('Controller/Adminhtml/Card/Index.php');
        $this->checkFile('Controller/Adminhtml/Card/Delete.php');
        $this->checkFile('Controller/Adminhtml/Card/Edit.php');
        $this->checkFile('Controller/Adminhtml/Card/InlineEdit.php');
        $this->checkFile('Controller/Adminhtml/Card/MassDelete.php');
        $this->checkFile('Controller/Adminhtml/Card/NewAction.php');
        $this->checkFile('Controller/Adminhtml/Card/Save.php');

        $this->checkFile('Controller/Adminhtml/Poster.php');
        $this->checkFile('Controller/Adminhtml/Poster/Index.php');
        $this->checkNoFile('Controller/Adminhtml/Poster/Delete.php');
        $this->checkNoFile('Controller/Adminhtml/Poster/Edit.php');
        $this->checkNoFile('Controller/Adminhtml/Poster/InlineEdit.php');
        $this->checkNoFile('Controller/Adminhtml/Poster/MassDelete.php');
        $this->checkNoFile('Controller/Adminhtml/Poster/NewAction.php');
        $this->checkNoFile('Controller/Adminhtml/Poster/Save.php');

        $this->checkNoFile('Controller/Adminhtml/Ticket.php');
        $this->checkNoFile('Controller/Adminhtml/Ticket/Index.php');
        $this->checkNoFile('Controller/Adminhtml/Ticket/Delete.php');
        $this->checkNoFile('Controller/Adminhtml/Ticket/Edit.php');
        $this->checkNoFile('Controller/Adminhtml/Ticket/InlineEdit.php');
        $this->checkNoFile('Controller/Adminhtml/Ticket/MassDelete.php');
        $this->checkNoFile('Controller/Adminhtml/Ticket/NewAction.php');
        $this->checkNoFile('Controller/Adminhtml/Ticket/Save.php');

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

        $this->checkXml('view/adminhtml/layout/sample_module_card_index.xml');
        $this->checkXml('view/adminhtml/layout/sample_module_card_new.xml');
        $this->checkXml('view/adminhtml/layout/sample_module_card_edit.xml');

        $this->checkXml('view/adminhtml/layout/sample_module_obsolete_index.xml');
        $this->checkXml('view/adminhtml/layout/sample_module_obsolete_new.xml');
        $this->checkXml('view/adminhtml/layout/sample_module_obsolete_edit.xml');

        $this->checkXml('view/adminhtml/layout/sample_module_poster_index.xml');
        $this->checkNoFile('view/adminhtml/layout/sample_module_poster_new.xml');
        $this->checkNoFile('view/adminhtml/layout/sample_module_poster_edit.xml');

        $this->checkNoFile('view/adminhtml/layout/sample_module_ticket_index.xml');
        $this->checkNoFile('view/adminhtml/layout/sample_module_ticket_new.xml');
        $this->checkNoFile('view/adminhtml/layout/sample_module_ticket_edit.xml');
    }

    private function checkUi()
    {
        $this->checkFile('Ui/Component/Listing/ColumnsActions.php');
        $this->checkFile('Model/Columns/DataProvider.php');
        $this->checkFile('Model/ResourceModel/Columns/Grid/Collection.php');

        $this->checkXml('view/adminhtml/ui_component/sample_module_columns_listing.xml');
        $this->checkXml('view/adminhtml/ui_component/sample_module_columns_edit.xml');

        $this->checkFile('Ui/Component/Listing/CartItemActions.php');
        $this->checkFile('Model/CartItem/DataProvider.php');
        $this->checkFile('Model/ResourceModel/CartItem/Grid/Collection.php');

        $this->checkXml('view/adminhtml/ui_component/sample_module_cartitem_listing.xml');
        $this->checkXml('view/adminhtml/ui_component/sample_module_cartitem_edit.xml');

        $this->checkFile('Ui/Component/Listing/CardActions.php');
        $this->checkFile('Model/Card/DataProvider.php');
        $this->checkFile('Model/ResourceModel/Card/Grid/Collection.php');

        $this->checkXml('view/adminhtml/ui_component/sample_module_card_listing.xml');
        $this->checkXml('view/adminhtml/ui_component/sample_module_card_edit.xml');

        $this->checkNoFile('Ui/Component/Listing/PosterActions.php');
        $this->checkFile('Model/Poster/DataProvider.php');
        $this->checkFile('Model/ResourceModel/Poster/Grid/Collection.php');

        $this->checkXml('view/adminhtml/ui_component/sample_module_poster_listing.xml');
        $this->checkNoFile('view/adminhtml/ui_component/sample_module_poster_edit.xml');

        $this->checkNoFile('Ui/Component/Listing/TicketActions.php');
        $this->checkNoFile('Model/Ticket/DataProvider.php');
        $this->checkNoFile('Model/ResourceModel/Ticket/Grid/Collection.php');

        $this->checkNoFile('view/adminhtml/ui_component/sample_module_ticket_listing.xml');
        $this->checkNoFile('view/adminhtml/ui_component/sample_module_ticket_edit.xml');

        $this->checkFile('Ui/Component/Listing/ObsoleteActions.php');
        $this->checkFile('Model/Obsolete/DataProvider.php');
        $this->checkFile('Model/ResourceModel/Obsolete/Grid/Collection.php');
        $this->checkXml('view/adminhtml/ui_component/sample_module_obsolete_listing.xml');
        $this->checkXml('view/adminhtml/ui_component/sample_module_obsolete_edit.xml');
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

    private function checkNoFile($file)
    {
        $this->assertFileDoesNotExist($this->path . '/' . $file);
    }
}
