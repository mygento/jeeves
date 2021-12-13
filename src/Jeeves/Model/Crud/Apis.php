<?php

namespace Mygento\Jeeves\Model\Crud;

use Mygento\Jeeves\Model\Api;
use Mygento\Jeeves\Model\Generator;

class Apis extends Generator
{
    public function generateApi(Entity $entity): array
    {
        if (!$entity->hasApi()) {
            return [];
        }

        $repo = $entity->getNamespace() . '\Api\\' . $entity->getEntityName() . 'RepositoryInterface';

        $api = [
            new Api(
                $entity->getEntityApiName() . '/:entityId',
                'GET',
                $repo,
                'getById',
                $entity->getEntityAcl()
            ),
            new Api(
                $entity->getEntityApiName() . '/search',
                'GET',
                $repo,
                'getList',
                $entity->getEntityAcl()
            ),
        ];
        if (!$entity->isReadOnly()) {
            $api[] = new Api(
                $entity->getEntityApiName(),
                'POST',
                $repo,
                'save',
                $entity->getEntityAcl()
            );
            $api[] = new Api(
                $entity->getEntityApiName() . '/:id',
                'PUT',
                $repo,
                'save',
                $entity->getEntityAcl()
            );
            $api[] = new Api(
                $entity->getEntityApiName() . '/:entityId',
                'DELETE',
                $repo,
                'deleteById',
                $entity->getEntityAcl()
            );
        }

        return $api;
    }
}
