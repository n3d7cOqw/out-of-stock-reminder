<?php

namespace OutOfStockReminder\Grid\Query;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class RuleQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param int $contextLangId
     * @param int $contextShopId
     */
    public function __construct(Connection $connection, $dbPrefix, $contextLangId, $contextShopId)
    {
        parent::__construct($connection, $dbPrefix);
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getBaseQuery();

        $qb->select('id, title, threshold, status, email')
            ->orderBy(
                "id"
            )
            ->setFirstResult($searchCriteria->getOffset())
            ->setMaxResults($searchCriteria->getLimit());
//        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
//            if ('id' === $filterName) {
//                $qb->andWhere("id = :$filterName");
//                $qb->setParameter($filterName, $filterValue);
//                continue;
//            }
//
//            $qb->andWhere("$filterName LIKE :$filterName");
//            $qb->setParameter($filterName, '%'.$filterValue.'%');
//        }


        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getBaseQuery();
        $qb->select('COUNT(id)');

        return $qb;
    }

    private function getBaseQuery()
    {
        return $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix.'out_of_stock_rules');
    }
}