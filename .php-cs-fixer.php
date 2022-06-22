<?php
$finder = PhpCsFixer\Finder::create()->in('./src');
$config = new \Mygento\CS\Config\Module();
$config->setFinder($finder);
return $config;
