<?php

namespace Hichxm\LaravelSortable\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * Trait HasSortableColumn
 *
 * @package Hichxm\LaravelSortable\Traits
 * @mixin Model
 *
 * @method Builder|self orderedDesc()
 * @method Builder|self orderedAsc()
 * @method Builder|self ordered(string $direction = 'asc')
 *
 */
trait HasSortableColumn
{

    /**
     * @var string The column name to use for sorting
     */
    protected string $sortableColumn = 'order';

    /**
     * Boot the trait
     */
    public static function bootHasSortableColumn(): void
    {
        static::creating(function ($model) {
            /** @var HasSortableColumn $model */

            $orderColumnName = $model->determineOrderColumnName();

            if (is_null($model->$orderColumnName)) {
                $model->$orderColumnName = $model->getHighestOrderNumber() + 1;
            }
        });
    }

    /**
     * Get the column name to use for sorting
     *
     * @return string
     */
    protected function determineOrderColumnName(): string
    {
        return $this->sortableColumn;
    }

    /**
     * Set the order number
     *
     * @param int $order
     * @return bool
     */
    public function setOrder(int $order): bool
    {
        return $this->update([
            $this->determineOrderColumnName() => $order,
        ]);
    }

    /**
     * Set order with a new order number
     *
     * @param array $ids
     * @param int $startIndex
     * @param callable|null $customizeQuery
     * @return void
     */
    public static function setNewOrder(array $ids, int $startIndex = 1, callable $customizeQuery = null): void
    {
        $model = new static;

        foreach ($ids as $index => $id) {
            if($id instanceof Model) $id = $id->getKey();

            $model
                ->laravelSortableQuery()
                ->withoutGlobalScope(SoftDeletingScope::class)
                ->where($model->getKeyName(), '=', $id)
                ->when(is_callable($customizeQuery), function ($query) use ($customizeQuery) {
                    return $customizeQuery($query);
                })
                ->update([
                    $model->determineOrderColumnName() => $startIndex + $index,
                ]);
        }
    }

    /**
     * Swap the order of two models
     *
     * @param Model|HasSortableColumn $model1
     * @param Model|HasSortableColumn $model2
     */
    public static function swapOrder(Model $model1, Model $model2)
    {
        $orderColumnNameModel1 = $model1->determineOrderColumnName();
        $orderColumnNameModel2 = $model2->determineOrderColumnName();

        $order1 = $model1->$orderColumnNameModel1;
        $order2 = $model2->$orderColumnNameModel2;

        $model1->setOrder($order2);
        $model2->setOrder($order1);
    }

    /**
     * Get Highest order number
     *
     * @return int
     */
    public function getHighestOrderNumber(): int
    {
        return $this->laravelSortableQuery()->max($this->determineOrderColumnName()) ?? 0;
    }

    /**
     * Get the lowest order number
     *
     * @return int
     */
    public function getLowestOrderNumber(): int
    {
        return $this->laravelSortableQuery()->min($this->determineOrderColumnName());
    }

    /**
     * Scope to get ordered records in descending order
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOrderedDesc(Builder $query): Builder
    {
        return $this->scopeOrdered($query, 'desc');
    }

    /**
     * Scope to get ordered records in ascending order
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeOrderedAsc(Builder $query): Builder
    {
        return $this->scopeOrdered($query);
    }

    /**
     * Scope to get ordered records in ascending or descending order
     *
     * @param Builder $query
     * @param string $direction
     * @return Builder
     */
    public function scopeOrdered(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy($this->determineOrderColumnName(), $direction);
    }

    /**
     * Get the query laravelSortableQuery
     *
     * @return Builder
     */
    protected function laravelSortableQuery(): Builder
    {
        return $this->newQuery();
    }

}