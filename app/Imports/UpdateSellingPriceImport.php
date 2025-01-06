<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;

class UpdateSellingPriceImport implements ToCollection
{
    public $rows;

    public function collection(Collection $collection) {
        $this->rows = $collection;
    }
}
