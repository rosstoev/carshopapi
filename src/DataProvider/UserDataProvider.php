<?php
declare(strict_types=1);

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\DenormalizedIdentifiersAwareItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;

class UserDataProvider implements ContextAwareCollectionDataProviderInterface, DenormalizedIdentifiersAwareItemDataProviderInterface, RestrictedDataProviderInterface
{

    private CollectionDataProviderInterface $collectionDataProvider;
    private ItemDataProviderInterface $itemDataProvider;

    public function __construct(CollectionDataProviderInterface $collectionDataProvider, ItemDataProviderInterface $itemDataProvider)
    {
        $this->collectionDataProvider = $collectionDataProvider;
        $this->itemDataProvider = $itemDataProvider;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        /** @var User[] $users */
        $users = $this->collectionDataProvider->getCollection($resourceClass, $operationName);
        foreach ($users as $user) {
            $this->setEven($user);
        }
        return $users;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === User::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        /** @var User|null $user */
        $user = $this->itemDataProvider->getItem($resourceClass, $id, $operationName, $context);

        if (!is_null($user)) {
            $this->setEven($user);
        }

        return $user;
    }

    private function setEven(User $user)
    {
        if ($user->getId() % 2 == 0) {
            $user->isEven = true;
        } else {
            $user->isEven = false;
        }
    }
}