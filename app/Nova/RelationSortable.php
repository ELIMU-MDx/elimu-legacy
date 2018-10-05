<?php

namespace App\Nova;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;

trait RelationSortable
{
    public static function sortBy(
        NovaRequest $request,
        $query,
        $relation,
        $orderColumn = 'name'
    ) {
        if ($request->get('orderBy') != $relation) {
            return $query;
        }
        $query->getQuery()->orders = null;
        $model = self::newModel();
        $relation = $model->{$relation}();
        $query->select($model->getTable().'.*', 'tmp.'.$orderColumn);

        $query->join(
            $relation->getRelated()->getTable(). ' as tmp',
            $model->getTable(). "." .$relation->getForeignKey(),
            "tmp.id"
        );
        $query->orderBy('tmp.'.$orderColumn, $request->get('orderByDirection'));
        return $query;
    }

    public static function sortByMultiple(NovaRequest $request, $query, array $data)
    {

        foreach ($data as $sortOptions) {
            $query = call_user_func_array([self::class, 'sortBy'], array_merge([$request, $query], $sortOptions));
        }
        return $query;
    }
}
