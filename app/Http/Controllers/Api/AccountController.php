<?php

namespace App\Http\Controllers\Api;

use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AccountCreateRequest;
use App\Http\Resources\Api\AccountShowResource;
use App\Http\Resources\Api\AccountCreateResource;

class AccountController extends Controller
{
    public function create(AccountCreateRequest $request)
    {
        $account = Account::create($request->all());

        return new AccountCreateResource($account);
    }

    public function show(Request $request, $id)
    {
        try {
            $account = Account::where('uuid', $id)->firstOrFail();
            return new AccountShowResource($account);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Conta não encontrada.',
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try{
            $account = Account::where('uuid', $id)->firstOrFail();
            $account->update($request->all());
            return new AccountShowResource($account);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Conta não encontrada.',
            ], 404);
        }
    }
}
