<?php

namespace App\Http\Controllers\Api;

use App\Enum\Currency;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Transaction\TransactionRequest;
use App\Http\Resources\Transaction\TransactionResource;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
    ) {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return TransactionResource::collection(
            $this->transactionService->userTransactions($request->user(), $request->all())
        );
    }

    /**
     * @throws Throwable
     */
    public function store(TransactionRequest $request): TransactionResource
    {
        $transaction = $this->transactionService->sendTransaction(
            $request->user(),
            $request->get('to'),
            $request->get('amount'),
            $request->enum('currency', Currency::class)
        );

        return new TransactionResource($transaction);
    }
}
