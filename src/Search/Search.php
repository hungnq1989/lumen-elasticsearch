<?php namespace Nord\Lumen\Elasticsearch\Search;

use Nord\Lumen\Elasticsearch\Search\Aggregation\Aggregation;
use Nord\Lumen\Elasticsearch\Search\Aggregation\AggregationCollection;
use Nord\Lumen\Elasticsearch\Search\Query\QueryDSL;

class Search
{
    /**
     * @var string
     */
    private $index;

    /**
     * @var string
     */
    private $type;

    /**
     * @var QueryDSL
     */
    private $query;

    /**
     * @var Sort
     */
    private $sort;

    /**
     * @var AggregationCollection
     */
    private $aggregations;

    /**
     * @var int
     */
    private $size = 100;

    /**
     * @var int
     */
    private $page = 1;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->aggregations = new AggregationCollection();
    }


    /**
     * @param $index
     * @return Search
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }


    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }


    /**
     * @param $type
     * @return Search
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }


    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * @param QueryDSL $query
     * @return Search
     */
    public function setQuery(QueryDSL $query)
    {
        $this->query = $query;
        return $this;
    }


    /**
     * @return QueryDSL
     */
    public function getQuery()
    {
        return $this->query;
    }


    /**
     * @param Sort $sort
     * @return Search
     */
    public function setSort(Sort $sort)
    {
        $this->sort = $sort;
        return $this;
    }


    /**
     * @return Sort
     */
    public function getSort()
    {
        return $this->sort;
    }


    /**
     * @return AggregationCollection
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }


    /**
     * @param Aggregation $aggregation
     * @return Search
     */
    public function addAggregation(Aggregation $aggregation)
    {
        $this->aggregations->add($aggregation);
        return $this;
    }


    /**
     * @param int $page
     * @return Search
     */
    public function setPage($page)
    {
        $this->page = (int)$page;
        return $this;
    }


    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }


    /**
     * @param int $size
     * @return Search
     */
    public function setSize($size)
    {
        $this->size = (int)$size;
        return $this;
    }


    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }


    /**
     * @return array
     */
    public function buildBody()
    {
        $body = [];

        if (($query = $this->getQuery())) {
            if (!empty($query)) {
                $body['query'] = $query->toArray();
            }
        }
        if (empty($body['query'])) {
            $body['query'] = ['match_all' => []];
        }

        if (($sort = $this->getSort())) {
            if (!empty($sort)) {
                $body['sort'] = $sort->toArray();
            }
        }

        $aggregations = $this->getAggregations();
        if ($aggregations->count() > 0) {
            $body['aggs'] = $aggregations->toArray();
        }

        // Set how many results to return.
        if ($this->getSize() > 0) {
            $body['size'] = $this->getSize();
        }

        // Set which "page" of results to return.
        if ($this->getPage() > 0) {
            $page = $this->getPage() - 1;
            $body['from'] = isset($body['size']) ? ($page * $body['size']) : 0;
        }

        return $body;
    }
}
