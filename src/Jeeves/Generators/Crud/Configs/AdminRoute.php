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
            $routes
        );

        return $service->write('config', function ($writer) use ($routeList) {
            $writer->setIndentString(self::TAB);
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:App/etc/routes.xsd');
            $writer->write([
                'name' => 'router',
                'attributes' => [
                    'id' => 'admin',
                ],
                'value' => $routeList,
            ]);
        });
    }

    private function getAdminRoute(Model\AdminRoute $route): array
    {
        return [
            'name' => 'route',
            'attributes' => [
                'id' => $route->getId(),
                'frontName' => $route->getPath(),
            ],
            'value' => [
                'name' => 'module',
                'attributes' => [
                    'name' => $route->getName(),
                    'before' => 'Magento_Backend',
                ],
            ],
        ];
    }
}
