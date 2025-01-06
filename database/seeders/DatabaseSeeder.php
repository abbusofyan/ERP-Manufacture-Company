<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Assembly;
use App\Models\Country;
use App\Models\Location;
use App\Models\Store;
use Illuminate\Database\Seeder;
use SebastianBergmann\CodeCoverage\Report\Xml\Unit;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(AuthSeeder::class);
         $this->call(CountrySeeder::class);
         $this->call(StoresLocationsSeeder::class);
         $this->call(CategorySeeder::class);
         $this->call(UnitOfMeasurementSeeder::class);
         $this->call(VendorSeeder::class);
         $this->call(VendorAddressSeeder::class);
         $this->call(VendorPocSeeder::class);
         $this->call(ProductSeeder::class);
         $this->call(ProductPriceSeeder::class);
         $this->call(StorageSeeder::class);
         $this->call(StorageItemSeeder::class);
         $this->call(CustomerSeeder::class);
         $this->call(PocSeeder::class);
         $this->call(StockAdjSeeder::class);
         $this->call(StockAdjustmentItemSeeder::class);
         $this->call(TransferSeeder::class);
         $this->call(TransferItemSeeder::class);
         $this->call(VehicleSeeder::class);
         $this->call(StoreProductSeeder::class);
         $this->call(AssemblySeeder::class);
         $this->call(AssemblyMaterialSeeder::class);
         $this->call(FinishGoodsSeeder::class);
//        $this->call(OrderSeeder::class);
//        $this->call(GoodsReceiptSeeder::class);
//        $this->call(GINSeeder::class);
    }
}
