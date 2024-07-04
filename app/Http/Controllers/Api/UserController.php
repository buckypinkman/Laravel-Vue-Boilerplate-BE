<?php

namespace App\Http\Controllers\Api;

use App\DataTables\UserDataTable;
use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Company;
use App\Models\Member;
use App\Models\ModelHasRole;
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
use Spatie\Permission\Models\Role;

class UserController extends BaseController
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
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

            $validator = Validator::make($request->all(), $this->validationRules());

            if($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Submit failed, please check your data',
                    'data' => $validator->errors()
                ], 400);
            }

            $payload = $validator->safe()->toArray();

            $payload['password'] = Hash::make($payload['password']);

            $data = $this->model->create($payload);
            $data->assignRole($payload['role']);

            $data->roles()->update([
                'agent_id' => $payload['agent_id'],
                'branch_id' => $payload['branch_id'],
            ]);

            $member = Member::create([
                'mobile_no' => $request['mobile_no'],
                'is_member' => $request['is_member'],
                'branch_id' => $request['branch_id']
            ]);

            $this->model->whereId($data->id)->update(['member_id' => $member->id]);

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
            $data = $this->model->with('userRoles', 'member')->find($id);

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

            $user = $this->model->find($id);

            if (!empty($payload['password'])) $payload['password'] = Hash::make($payload['password']);
            else unset($payload['password']);

            $data = $user->update($payload);
            DB::table('model_has_roles')->where('model_type', 'App\Models\User')->where('model_id',$user->id)->delete();
            $user->assignRole($payload['role']);

            $user->roles()->update([
                'agent_id' => $payload['agent_id'],
                'branch_id' => $payload['branch_id'],
            ]);

            Member::whereId($user->member_id)->update([
                'mobile_no' => $request['mobile_no'],
                'is_member' => $request['is_member'],
                'branch_id' => $request['branch_id']
            ]);

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
            $user = $this->model->find($id);
            $data = $user->delete();

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


    public function getRoles() {
        try {
            $data = Role::all(['name as label', 'id as value']);

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

    public function detail()
    {
        try {
            $data = User::with('userRoles')->find(Auth::user()->id);

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
            'username' => 'required|unique:users,username,NULL,id,deleted_at,NULL',
            'email' => 'required|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required',
            'role' => 'required',
            'agent_id' => 'required',
            'branch_id' => 'required',
            'is_member' => 'required',
            'mobile_no' => 'required'
        ];

        if($id) {
            unset($validation['password']);
            $validation['username'] = 'required|unique:users,username,'.$id.',id,deleted_at,NULL';
            $validation['email'] = 'required|unique:users,email,'.$id.',id,deleted_at,NULL';
        }

        return $validation;
    }
}
