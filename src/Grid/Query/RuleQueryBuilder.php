<?php

namespace OutOfStockReminder\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\DoctrineFilterApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\Filter\SqlFilters;
final class RuleQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var int
     */
    private $contextLanguageId;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @var bool
     */
    private $isStockSharingBetweenShopGroupEnabled;

    /**
     * @var int
     */
    private $contextShopGroupId;

    /**
     * @var DoctrineFilterApplicatorInterface
     */
    private $filterApplicator;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        int $contextLanguageId,
        int $contextShopId,
        int $contextShopGroupId,
        bool $isStockSharingBetweenShopGroupEnabled,
        DoctrineFilterApplicatorInterface $filterApplicator,
        Configuration $configuration
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextLanguageId = $contextLanguageId;
        $this->contextShopId = $contextShopId;
        $this->isStockSharingBetweenShopGroupEnabled = $isStockSharingBetweenShopGroupEnabled;
        $this->contextShopGroupId = $contextShopGroupId;
        $this->filterApplicator = $filterApplicator;
        $this->configuration = $configuration;
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('id, title, threshold, status, email')
            ->orderBy(
                $searchCriteria->getOrderBy(), $searchCriteria->getOrderWay()
            )
            ->setFirstResult(pSQL($searchCriteria->getOffset()))
            ->setMaxResults(pSQL($searchCriteria->getLimit()));



        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getBaseQuery();
        $qb->select('COUNT(id)');

        return $qb;
    }

    private function getQueryBuilder(array $filterValues): QueryBuilder
    {

        $qb = $this->getBaseQuery()
            ->select('id, title, threshold, status, email');
        $sqlFilters = new SqlFilters();
        $sqlFilters
            ->addFilter(
                'id',
                'id',
                SqlFilters::WHERE_STRICT
            );
        if (version_compare(_PS_VERSION_, '8.0', '>=')) {
            $sqlFilters
                ->addFilter(
                    'threshold',
                    'threshold',
                    SqlFilters::MIN_MAX
                );
        }

        $this->filterApplicator->apply($qb, $sqlFilters, $filterValues);


        $qb->setParameter('id_shop', $this->contextShopId);
        $qb->setParameter('id_lang', $this->contextLanguageId);

        foreach ($filterValues as $filterName => $filter) {
            if ('status' === $filterName) {
                $qb->andWhere('status = :status');
                $qb->setParameter(':status', pSQL($filter));

                continue;
            }

            if ('title' === $filterName) {
                $qb->andWhere('title LIKE :title');
                $qb->setParameter(':title', '%' .  pSQL($filter) . '%');

                continue;
            }


            if (version_compare(_PS_VERSION_, '8.0', '<')) {
                if ('threshold' === $filterName) {
                    if (isset($filter['min_field'])) {
                        $qb->andWhere('threshold >= :price_min');
                        $qb->setParameter(':price_min', (int)  pSQL($filter['min_field']));
                    }
                    if (isset($filter['max_field'])) {

                        $qb->andWhere('threshold <= :price_max');
                        $qb->setParameter(':price_max', (int)  pSQL($filter['max_field']));
                    }
                }
            }
        }


        return $qb;
    }

    private function getBaseQuery()
    {
        return $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix.'out_of_stock_rules');
    }
}