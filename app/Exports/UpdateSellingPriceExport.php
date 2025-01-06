<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class UpdateSellingPriceExport implements FromView
{
    protected $category_id;
    protected $data;

    public function __construct($category_id, $data) {
        $this->category_id = $category_id;
        $this->data = $data;
    }

    public function view(): View {
        return view('export.update-selling-price',[
            'data' => $this->data
        ]);
    }
}
