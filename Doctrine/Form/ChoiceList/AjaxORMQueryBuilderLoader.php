<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Component\FormExtensions\Doctrine\Form\ChoiceList;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
class AjaxORMQueryBuilderLoader extends BaseAjaxORMQueryBuilderLoader
{
    /**
     * Contains the query builder that builds the query for fetching the
     * entities.
     *
     * This property should only be accessed through query builder.
     *
     * @var QueryBuilder
     */
    private $filterableQueryBuilder;

    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * Construct an ORM Query Builder Loader.
     *
     * @param QueryBuilder $queryBuilder The query builder for creating the query builder
     *
     * @throws UnexpectedTypeException
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        $this->filterableQueryBuilder = clone $this->getQueryBuilder();

        parent::reset();
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilterableQueryBuilder()
    {
        return $this->filterableQueryBuilder;
    }
}
