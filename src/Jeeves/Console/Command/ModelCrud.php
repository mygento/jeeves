<?php

namespace Mygento\Jeeves\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;


use Memio\Memio\Config\Build;
use Memio\Model\File;
use Memio\Model\Object;
use Memio\Model\Property;
use Memio\Model\Method;
use Memio\Model\Argument;

class ModelCrud extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('generate_model_crud')
            ->setDescription('Generate Model Crud')
            ->setDefinition(array(
                new InputOption('module', null, InputOption::VALUE_REQUIRED, 'Name of the module'),
                new InputOption('name', null, InputOption::VALUE_REQUIRED, 'Name of the model'),
                new InputOption('tablename', null, InputOption::VALUE_REQUIRED, 'Name of the table'),
              )
            )
            ->setHelp(<<<EOT
<info>php jeeves.phar generate_model_crud</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->genModel('Mygento\Payment', 'Keys');
        $this->genResourceModel('Mygento\Payment', 'Keys', 'mygento_keys');
        $this->genResourceCollection('Mygento\Payment', 'Keys');

        $service = new \Sabre\Xml\Service();
        $service->namespaceMap = ['http://www.w3.org/2001/XMLSchema-instance' => 'xsi'];
        $xml = $service->write('config', function($writer) {
          $writer->writeAttribute('xsi:noNamespaceSchemaLocation','urn:magento:framework:ObjectManager/etc/config.xsd');
          $writer->write([
              'name' => 'virtualType',
              'attributes' => [
                'name' => 'CloudPaymentsFacade',
                'type' => 'Magento\Payment\Model\Method\Adapter',
              ],
              'value' => [
                'arguments' => [
                  [
                    'name' => 'argument',
                    'attributes' => [
                      'name' => 'code',
                      'xsi:type' => 'string',
                    ],
                    'value' => 'cloudpayments',
                  ]
                ]
              ]
          ]);
        });
        echo $xml;
        $this->writeFile('generated/di.xml', $xml);
    }

    protected function genModel($module, $model)
    {
      $namespace = new \Nette\PhpGenerator\PhpNamespace($module.'\Model');
      $class = $namespace->addClass($model);
      $class->setExtends('\Magento\Framework\Model\AbstractModel');
      $method = $class->addMethod('_construct')
          ->addComment('Initialize '.$model.' model')
          ->setVisibility('protected')
          ->setBody('$this->_init(\\'.$module.'\\Model\ResourceModel'.'\\'.$model.'::class);');
      $this->writeFile('generated/Model/'.$model.'.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
      echo $namespace;
    }

    protected function genResourceModel($module, $model, $table, $key = 'id')
    {
      $namespace = new \Nette\PhpGenerator\PhpNamespace($module.'\Model\ResourceModel');
      $class = $namespace->addClass($model);
      $class->setExtends('\Magento\Framework\Model\ResourceModel\Db\AbstractDb');
      $method = $class->addMethod('_construct')
          ->addComment('Initialize '.$model.' resource model')
          ->setVisibility('protected')
          ->setBody('$this->_init(\''.$table.'\', \''.$key.'\');');
      $this->writeFile('generated/Model/ResourceModel/'.$model.'.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
      echo $namespace;
    }

    protected function genResourceCollection($module, $model)
    {
      $namespace = new \Nette\PhpGenerator\PhpNamespace($module.'\Model\ResourceModel\\'.$model);
      $class = $namespace->addClass('Collection');
      $class->setExtends('\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection');
      $method = $class->addMethod('_construct')
          ->addComment('Initialize '.$model.' resource collection')
          ->setVisibility('protected')
          ->setBody('$this->_init('.PHP_EOL.
            '   '.'\\'.$module.'\\Model'.'\\'.$model.'::class,'.PHP_EOL.
            '   '.'\\'.$module.'\\Model\ResourceModel'.'\\'.$model.'::class'.PHP_EOL.
            ');');
      $this->writeFile('generated/Model/ResourceModel/'.$model.'/Collection.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
      echo $namespace;
    }
}
