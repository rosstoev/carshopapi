<?php
declare(strict_types=1);

namespace App\Filter;


use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;

final class BrandFilter extends AbstractContextAwareFilter
{

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {

        // otherwise filter is applied to order and page as well
        if (
            !$this->isPropertyEnabled($property, $resourceClass) ||
            !$this->isPropertyMapped($property, $resourceClass)
        ) {
            return;
        }

        $parameterName = $queryNameGenerator->generateParameterName($property); // Generate a unique parameter name to avoid collisions with other filters
        $alias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->andWhere($alias.'.brand = :'. $parameterName)
            ->setParameter($parameterName, $value);
    }

    public function getDescription(string $resourceClass): array
    {

        if (!$this->properties) {
            return [];
        }

        foreach ($this->properties as $property => $strategy) {
            $description[$property] = [
                'property' => $property,
                'type' => 'string',
                'required' => false,
                'swagger' => [
                    'description' => 'Brand name filter.',
                    'name' => 'Filter by brand name',
                    'type' => 'Brand name filter',
                ],
            ];
        }

        return $description;
    }
}