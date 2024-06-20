<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function datatable(Request $request, $model, $relations = []) {
        $search = $request->query('search', '');
        $searchColumns = $request->query('search_columns', '');

        $searchColumns = explode(',', $searchColumns);
        $perPage = $request->query('limit', 10); // Number of results per page
        $page = $request->query('page', 1); // Current page

        // Start query builder
        if(count($relations) > 0) {
            $query = $model->with($relations);
        } else {
            $query = $model->query();
        }
        

        // Apply search filters
        if ($search && !empty($searchColumns)) {
            $query->where(function ($q) use ($search, $searchColumns) {
                foreach ($searchColumns as $column) {
                    $q->orWhere($column, 'LIKE', '%' . $search . '%');
                }
            });
        }

        // Paginate the results
        return $query->paginate($perPage, ['*'], 'page', $page);
    }
}
