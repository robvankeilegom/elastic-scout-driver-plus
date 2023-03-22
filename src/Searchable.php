<?php
declare(strict_types=1);

namespace Elastic\ScoutDriverPlus;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Elastic\ScoutDriverPlus\Builders\QueryBuilderInterface;
use Elastic\ScoutDriverPlus\Builders\SearchParametersBuilder;
use Laravel\Scout\Searchable as BaseSearchable;

trait Searchable
{
    use BaseSearchable {
        searchableUsing as baseSearchableUsing;
    }

    /**
     * @param Closure|QueryBuilderInterface|array|null $query
     */
    public static function searchQuery($query = null): SearchParametersBuilder
    {
        $builder = new SearchParametersBuilder(new static());

        if (isset($query)) {
            $builder->query($query);
        }

        return $builder;
    }

    /**
     * @return string|int|null
     */
    public function searchableRouting()
    {
        return null;
    }

    /**
     * @return array|string|null
     */
    public function searchableWith()
    {
        return null;
    }

    public function searchableConnection(): ?string
    {
        return null;
    }

    /**
     * @return Engine
     */
    public function searchableUsing()
    {
        /** @var Engine $engine */
        $engine = $this->baseSearchableUsing();
        $connection = $this->searchableConnection();

        return isset($connection) ? $engine->connection($connection) : $engine;
    }

    public static function openPointInTime(?string $keepAlive = null): string
    {
        $self = new static();
        $engine = $self->searchableUsing();
        $indexName = $self->searchableAs();

        return $engine->openPointInTime($indexName, $keepAlive);
    }

    public static function closePointInTime(string $pointInTimeId): void
    {
        $self = new static();
        $engine = $self->searchableUsing();

        $engine->closePointInTime($pointInTimeId);
    }

    public function queryElasticModelsByIds(Builder $query, array $ids)
    {
        $query->whereIn($this->getScoutKeyName(), $ids);

        return $query;
    }
}
