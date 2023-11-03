<?php

namespace App\Services\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

trait ListBaseParams
{
    /**
     * Validate list params.
     *
     * @param array{limit: int, sort: string, order: string} $params
     *
     * @return array
     */
    protected function validateListParams(array $params): array
    {
        // Defaults
        $defaultLimit = $this->getDefaultLimit();
        $defaultSort = $this->getDefaultSort();
        $defaultOrder = $this->getDefaultOrder();
        $sortFields = $this->getSortFields();

        // Input params
        $limit = (int) ($params['limit'] ?? $defaultLimit);
        $sort = $params['sort'] ?? $defaultSort;
        $order = $params['order'] ?? $defaultOrder;

        // Set params
        $params['limit'] = $limit;
        $params['sort'] = $sortFields[$sort] ?? $sortFields[$defaultSort];
        $params['order'] = $this->getOrders()[$order] ?? $defaultOrder;

        return $params;
    }

    /**
     * Get query result.
     *
     * @param Builder|EloquentBuilder|Relation $query
     * @param int $limit
     *
     * @return Collection|LengthAwarePaginator
     */
    public function getResult(Builder|EloquentBuilder|Relation $query, int $limit): Collection|LengthAwarePaginator
    {
        return $limit <= 0 ? $query->get() : $query->paginate($limit);
    }

    /**
     * Default limit records on the page.
     *
     * @return int|null
     */
    protected function getDefaultLimit(): ?int
    {
        return 10;
    }

    /**
     * Default sort field.
     * Key in the sort fields array.
     *
     * @return string
     */
    protected function getDefaultSort(): string
    {
        return 'created_at';
    }

    /**
     * Default order direction.
     *
     * @return string
     */
    protected function getDefaultOrder(): string
    {
        return 'desc';
    }

    /**
     * Sortable fields.
     * Key is the name of input parameter.
     * Value is the field name in query.
     * Array[key => value] needs for raw order query,
     * for example: 'full_name' => 'CONCAT(first_name, ' ', last_name)'.
     *
     * @return array
     */
    protected function getSortFields(): array
    {
        return [
            'created_at' => 'created_at',
        ];
    }

    /**
     * Order directions helper array.
     *
     * @return string[]
     */
    private function getOrders(): array
    {
        return [
            'asc' => 'asc',
            'desc' => 'desc',
        ];
    }
}
