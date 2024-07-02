<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SavingAccountCategoryCreateRequest;
use App\Http\Requests\SavingAccountCategoryUpdateRequest;
use App\Models\SavingAccountCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SavingAccountCategoryController extends BaseController
{
    protected $model;

    public function __construct(SavingAccountCategory $user)
    {
        $this->model = $user;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $this->datatable($request, $this->model);

        return response()->json([
            'success' => true,
            'message' => 'Success retrieve data',
            'data' => $data
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SavingAccountCategoryCreateRequest $request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->safe()->toArray();
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $data = $this->model->find($id);

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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SavingAccountCategoryUpdateRequest $request, string $id)
    {
        try {

            $payload = $request->safe()->toArray();

            $agent = $this->model->find($id);

            $data = $agent->update($payload);

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $model = $this->model->find($id);
            $data = $model->delete();

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

    /**
     * Get all agents for list
     */
    public function list()
    {
        try {
            $data = $this->model->all(['name as label', 'id as value']);

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
}
