<?php

namespace Mygento\Jeeves\Generators\Crud\Configs;

use Mygento\Jeeves\Generators\Crud\Common;
use Mygento\Jeeves\Model;

class AdminRoute extends Common
{
    public function generateAdminRoutes(array $routes): string
    {
        $service = $this->getService();

        $routeList = array_map(
            function (Model\AdminRoute $route) {
                return $this->getAdminRoute($route);
            },
            array_values($routes)
        );

        return $service->write('config', function ($writer) use ($routeList) {
            $writer->setIndentString(self::TAB);
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:App/etc/routes.xsd');
            $writer->write([
                self::N => 'router',
                self::A => [
                    'id' => 'admin',
                ],
                self::V => $routeList,
            ]);
        });
    }

    private function getAdminRoute(Model\AdminRoute $route): array
    {
        return [
            self::N => 'route',
            self::A => [
                'id' => $route->getId(),
                'frontName' => $route->getPath(),
            ],
            self::V => [
                self::N => 'module',
                self::A => [
                    self::N => $route->getName(),
                    'before' => 'Magento_Backend',
                ],
            ],
        ];
    }
}
