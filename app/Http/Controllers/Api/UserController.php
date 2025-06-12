<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Call;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function UserList(Request $request)
    {
        try {
            $authUser = $request->user();

            if (!$authUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Token'
                ], 401);
            }

            $users = User::where('id', '!=', $authUser->id)
            ->when(
                $request->search_name,
                fn($query, $search) =>
                $query->where('name', 'LIKE', '%' . $search . '%')
            )
            ->paginate(10);

            return UserResource::collection($users)->additional([
                'success' => true,
                'message' => 'Users data fetched successfully!'
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'success' => false,
                'message' => 'Backend Error',
                'errors' => $th->getMessage()
            ]);
        }
    }

    public function StartCall(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'receiver_id' => 'required|integer|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $user = $request->user();
            if ($user->id == $request->receiver_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Token'
                ]);
            }
            $checkActiveCall = Call::where(['caller_id' => $user->id])->whereIn('status', [0, 1])->exists();
            if ($checkActiveCall) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can not start other call, one call is active'
                ]);
            }
            $data = Call::create([
                'caller_id' => $user->id,
                'receiver_id' => $request->receiver_id,
                'started_at' => Carbon::now()
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Call started',
                'data' => $data
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'success' => false,
                'message' => 'Backend Error',
                'erores' => $th->getMessage()
            ]);
        }
    }
    public function ResponseCall(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'call_id' => 'required|integer|exists:calls,id',
                'response' => 'required|integer|in:1,2',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $user = $request->user();
            $call = Call::find($request->call_id);
            if ($user->id != $call->receiver_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized response user!'
                ], 401);
            }
            $call->status = $request->response;
            $call->save();
            return response()->json([
                'success' => true,
                'message' => 'Call responsed successfully!',
                'data' => $call
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'success' => false,
                'message' => 'Backend Error',
                'erores' => $th->getMessage()
            ]);
        }
    }
    public function EndCall(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'call_id' => 'required|integer|exists:calls,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 401);
            }
            $user = $request->user();
            $call = Call::find($request->call_id);
            if ($user->id != $call->receiver_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized response user!'
                ], 401);
            }
            $call->status = 3;
            $call->ended_at = Carbon::now();
            $call->save();
            return response()->json([
                'success' => true,
                'message' => 'Call ended successfully!',
                'data' => $call
            ]);
        } catch (\Exception $th) {
            return response()->json([
                'success' => false,
                'message' => 'Backend Error',
                'erores' => $th->getMessage()
            ]);
        }
    }
}
