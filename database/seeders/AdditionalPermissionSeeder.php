<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use League\Csv\Reader;
use Spatie\Permission\Models\Permission;
use DB;

class AdditionalPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permissions = [
            'view-purchase_goods_return',
            'update-purchase_goods_return',
            'create-purchase_goods_return',
            'delete-purchase_goods_return',

        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
            ]);
        }

    }
}
