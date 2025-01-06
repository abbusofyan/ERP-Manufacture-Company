<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Route;
use App\Models\ProductHistory;

class ProductHistoryService
{
    /**
     * Adds a history record for a product.
     *
     * @param Product $product
     * @param string|null $action
     * @return void
     */
    public function addProductHistory(Product $product, $action = null): void
    {
        $action = $action ?? Route::currentRouteName();
        $updatedData = $this->getUpdatedData($product, $action);

        if (!empty($updatedData)) {
            ProductHistory::create([
                'product_id' => $product->id,
                'reference_id' => null,
                'reference_model' => null,
                'action' => $action,
                'data' => $updatedData,
                'created_by' => auth()->id(),
            ]);
        }
    }

    /**
     * Determines the updated data based on the action.
     *
     * @param Product $product
     * @param string $action
     * @return mixed
     */
    private function getUpdatedData(Product $product, $action)
    {
        switch ($action) {
            case 'products.update':
            case 'products.updateStatus':
                return $this->filterChanges($product->getChanges());

            case 'products.store':
                return 'Product created';

            case 'uploadPartBook':
                return 'New part book uploaded';

            case 'uploadProductPhoto':
                return 'New product photo uploaded';

            default:
                return [];
        }
    }

    /**
     * Filters out unnecessary fields from the product changes.
     *
     * @param array $changes
     * @return array
     */
    private function filterChanges(array $changes): array
    {
        unset($changes['updated_at'], $changes['updated_by']);
        return $changes;
    }
}
