<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RequisitionItem;
use App\Models\Requisition;

class Select2Controller extends Controller
{
    public function select2QueryRequisitionTransactionNumber(Request $request)
    {
        if (!$request->transaction_type) {
            return [];
        }
        $groupedRequisitionable = RequisitionItem::with(['requisition.requisitionable' => function($query) use ($request) {
            $query->select('id', 'code');

            if ($request->has('search')) {
                $query->where('code', 'LIKE', "%{$request->search}%");
            }
        }])
        ->where('committed_qty', '>', 0)
        ->whereHas('requisition', function ($query) use ($request) {
            $query->where('status', Requisition::APPROVED_STATUS)
                  ->when($request->transaction_type !== null, function ($query) use ($request) {
                      $query->where('type', $request->transaction_type);
                  });
        })
        ->get()
        ->pluck('requisition.requisitionable')
        ->filter()
        ->flatten(1)
        ->map(function ($item) {
            return $item->only(['id', 'code']);
        });

        $transactionCode = $groupedRequisitionable->unique('code')->values();

        return $transactionCode;
    }

    public function select2QueryRequisitions(Request $request)
    {
        return Requisition::when($request->has('search'), function ($query) use ($request) {
            $query->where('code', 'LIKE', "%{$request->search}%");
        })->limit(10)->get();
    }
}
