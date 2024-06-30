<?php

namespace App\Http\Controllers\Api;

use App\DataTables\UserDataTable;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Company;
use App\Models\Outlet;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends BaseController
{
    protected $model;

    public function __construct(Role $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        $data = $this->datatable($request, $this->model);

        return response()->json([
            'success' => true,
            'message' => 'Success retrieve data',
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {

            $validator = Validator::make($request->all() + ['guard_name' => 'web'], $this->validationRules());

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Submit failed, please check your data',
                    'data' => $validator->errors()
                ], 400);
            }

            $payload = $validator->safe()->toArray();

            $data = $this->model->create($payload);

            DB::commit();

            $response = [
                'success' => true,
                'message' => 'Success save data',
                'data' => $data
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            $response = [
                'success' => false,
                'message' => 'Server Error',
                'data' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }


    public function show($id)
    {
        try {
            $data = $this->model->with('permissions')->find($id)->toArray();

            $permissions = collect($data['permissions'])->mapWithKeys(function($row) {
                return [$row['name'] => true];
            });

            $allPermissions = Permission::whereNotIn('name', collect($data['permissions'])->pluck('name', 'id')->toArray())
                ->get()
                ->mapWithKeys(function($row) {
                    return [$row['name'] => false];
                });

            $data = [...$data, 'permissions' => [...$allPermissions, ...$permissions]];

            $response = [
                'success' => true,
                'message' => 'Success retrieve data',
                'data' => $data
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $response = [
                'success' => false,
                'message' => 'Server Error',
                'data' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {

            $validator = Validator::make($request->all(), $this->validationRules($id));

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Submit failed, please check your data',
                    'data' => $validator->errors()
                ], 400);
            }

            $payload = $validator->safe()->toArray();

            $model = $this->model->find($id);

            $selectedPermissions = [];
            foreach($request->permissions as $permissionName => $selected) {
                if($selected) {
                    $selectedPermissions[] = $permissionName;
                }
            }

            $model->syncPermissions($selectedPermissions);
            $data = $model->update($payload);

            $response = [
                'success' => true,
                'message' => 'Success save data',
                'data' => $data
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $response = [
                'success' => false,
                'message' => 'Server Error',
                'data' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }

    public function destroy($id)
    {
        try {
            $data = $this->model->whereId($id)->delete();

            $response = [
                'success' => true,
                'message' => 'Success delete data',
                'data' => $data
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $response = [
                'success' => false,
                'message' => 'Server Error',
                'data' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }

    public function getPermissions(Request $request) {
        try {
            if($request->query('specific')) {
                $data = Auth::user()->roles[0]->permissions->pluck('name');
            } else {
                $data = Permission::all();
            }

            $response = [
                'success' => true,
                'message' => 'Success retrieve data',
                'data' => $data
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            $response = [
                'success' => false,
                'message' => 'Server Error',
                'data' => $e->getMessage()
            ];
            return response()->json($response, 500);
        }
    }

    public function validationRules($id = null) {
        $validation = [
            'name' => 'required',
            'guard_name' => 'required'
        ];

        return $validation;
    }
}
