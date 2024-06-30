<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgentCreateRequest;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AgentController extends Controller
{
    protected $model;

    public function __construct(Agent $user)
    {
        $this->model = $user;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Submit failed, please check your data',
            'data' => []
        ], 422);

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
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
