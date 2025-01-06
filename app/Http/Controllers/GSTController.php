<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GST;

class GSTController extends Controller
{
    public function select2Query(Request $request)
    {
        return GST::when($request->has('search'), function ($query) use ($request) {
            $query->where('code', 'LIKE', "%{$request->search}%")
                ->where('description', 'LIKE', "%{$request->search}%");
        })->limit(10)->get();
    }
}
