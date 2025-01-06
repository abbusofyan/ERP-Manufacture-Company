<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use League\Csv\Reader;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AuthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Define the list of unique roles
        $roles = [
            'IT',
            'ENGR',
            'FINANCE',
            'HR',
            'MARKETING',
            'MANAGE',
            'PROJECT',
            'PRODUCTION',
            'PURCHASE',
            'QC',
            'SERVICE',
            'STORE',
            'ADMIN', // Add ADMIN if needed
        ];

        // 2. Create roles if they do not exist
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 3. Define the list of permissions
        $permissions = [
            // General Permissions
            'view-any',

            'view-user',
            'create-user',
            'update-user',
            'delete-user',

            'view-customer',
            'view-customer-revenue',
            'create-customer',
            'update-customer',
            'delete-customer',

            'view-bank',
            'create-bank',
            'update-bank',
            'delete-bank',

            'view-discount',
            'create-discount',
            'update-discount',
            'delete-discount',

            'view-vendor',
            'create-vendor',
            'update-vendor',
            'delete-vendor',

            'view-requisition',
            'create-requisition',
            'update-requisition',
            'delete-requisition',
            'approve-requisition',

            'view-requisition_to_gin',
            'create-requisition_to_gin',
            'update-requisition_to_gin',
            'delete-requisition_to_gin',

            'view-requisition_to_order',
            'create-requisition_to_order',
            'update-requisition_to_order',
            'delete-requisition_to_order',

            'view-order',
            'create-order',
            'update-order',
            'delete-order',

            'view-store',
            'create-store',
            'update-store',
            'delete-store',

            'view-location',
            'create-location',
            'update-location',
            'delete-location',

            'view-product',
            'create-product',
            'update-product',
            'delete-product',

            'view-storage',
            'create-storage',
            'update-storage',
            'delete-storage',

            'view-stock',
            'create-stock',
            'update-stock',
            'delete-stock',

            'view-transfer',
            'create-transfer',
            'update-transfer',
            'delete-transfer',
            'approve-transfer',


            'view-uom',
            'create-uom',
            'update-uom',
            'delete-uom',

            'view-category',
            'create-category',
            'update-category',
            'delete-category',

            'view-service_appointment',
            'create-service_appointment',
            'update-service_appointment',
            'delete-service_appointment',

            'view-service_contract',
            'create-service_contract',
            'update-service_contract',
            'delete-service_contract',

            'view-service_invoice',
            'create-service_invoice',
            'update-service_invoice',
            'delete-service_invoice',

            'view-service_order',
            'create-service_order',
            'update-service_order',
            'delete-service_order',

            'view-service_order_attachment',
            'create-service_order_attachment',
            'update-service_order_attachment',
            'delete-service_order_attachment',

            'view-service_order_process',
            'create-service_order_process',
            'update-service_order_process',
            'delete-service_order_process',

            'view-service_order_report',
            'create-service_order_report',
            'update-service_order_report',
            'delete-service_order_report',

            'view-service_order_requirement',
            'create-service_order_requirement',
            'update-service_order_requirement',
            'delete-service_order_requirement',

            'view-service_order_requirement_used',
            'create-service_order_requirement_used',
            'update-service_order_requirement_used',
            'delete-service_order_requirement_used',

            'view-service_quotation',
            'create-service_quotation',
            'update-service_quotation',
            'delete-service_quotation',

            'view-service_schedule',
            'create-service_schedule',
            'update-service_schedule',
            'delete-service_schedule',

            'view-service_technician',
            'create-service_technician',
            'update-service_technician',
            'delete-service_technician',

            'view-project_appointment',
            'create-project_appointment',
            'update-project_appointment',
            'delete-project_appointment',

            'view-project_contract',
            'create-project_contract',
            'update-project_contract',
            'delete-project_contract',

            'view-project_invoice',
            'create-project_invoice',
            'update-project_invoice',
            'delete-project_invoice',

            'view-project_order',
            'create-project_order',
            'update-project_order',
            'delete-project_order',

            'view-project_order_attachment',
            'create-project_order_attachment',
            'update-project_order_attachment',
            'delete-project_order_attachment',

            'view-project_order_process',
            'create-project_order_process',
            'update-project_order_process',
            'delete-project_order_process',

            'view-project_order_report',
            'create-project_order_report',
            'update-project_order_report',
            'delete-project_order_report',

            'view-project_order_requirement',
            'create-project_order_requirement',
            'update-project_order_requirement',
            'delete-project_order_requirement',

            'view-project_order_requirement_used',
            'create-project_order_requirement_used',
            'update-project_order_requirement_used',
            'delete-project_order_requirement_used',

            'view-project_quotation',
            'create-project_quotation',
            'update-project_quotation',
            'delete-project_quotation',

            'view-project_schedule',
            'create-project_schedule',
            'update-project_schedule',
            'delete-project_schedule',

            'view-project_technician',
            'create-project_technician',
            'update-project_technician',
            'delete-project_technician',

            'view-stock',
            'create-stock',
            'update-stock',
            'delete-stock',

            'view-vehicle',
            'create-vehicle',
            'update-vehicle',
            'delete-vehicle',

            'view-goods-receipt',
            'create-goods-receipt',
            'update-goods-receipt',
            'delete-goods-receipt',

            'view-gin',
            'create-gin',
            'update-gin',
            'delete-gin',

            'view-goods_receipt',
            'create-goods_receipt',
            'update-goods_receipt',
            'delete-goods_receipt',

            'view-hygiene',
            'create-hygiene',
            'update-hygiene',
            'delete-hygiene',

            'view-quotation',
            'create-quotation',
            'update-quotation',
            'delete-quotation',

            'view-refrigeration_sale',
            'create-refrigeration_sale',
            'update-refrigeration_sale',
            'delete-refrigeration_sale',

            'view-sales_order',
            'create-sales_order',
            'update-sales_order',
            'delete-sales_order',

            'view-sales_order_eco',
            'create-sales_order_eco',
            'update-sales_order_eco',
            'delete-sales_order_eco',

            'view-assembly',
            'create-assembly',
            'update-assembly',
            'activate-assembly',
            'inactivate-assembly',

            'view-finish-goods',
            'create-finish-goods',
            'update-finish-goods',
            'activate-finish-goods',
            'inactivate-finish-goods',

            'view-production_order',
            'create-production_order',
            'update-production_order',
            'delete-production_order',

            'view-production_process_detail',
            'create-production_process_detail',
            'update-production_process_detail',
            'delete-production_process_detail',

            'view-engineering_order',
            'create-engineering_order',
            'update-engineering_order',
            'delete-engineering_order',

            'view-engineering_process_detail',
            'create-engineering_process_detail',
            'update-engineering_process_detail',
            'delete-engineering_process_detail',
        ];

        // 4. Create permissions if they do not exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 5. Retrieve all permissions
        $allPermissions = Permission::all();

        // 6. Map permissions to each role
        $rolePermissionsMap = [
            'ADMIN' => $allPermissions, // ADMIN has all permissions

            'IT' => $allPermissions,

            'ENGR' => Permission::where('name', 'like', 'view-engineering%')
                ->orWhere('name', 'like', 'create-engineering%')
                ->orWhere('name', 'like', 'update-engineering%')
                ->orWhere('name', 'like', 'delete-engineering%')
                ->orWhere('name', 'view-customer')
                ->get(),

            'FINANCE' => Permission::where('name', 'like', 'view-finance%')
                ->orWhere('name', 'like', 'create-finance%')
                ->orWhere('name', 'like', 'update-finance%')
                ->orWhere('name', 'like', 'delete-finance%')
                ->orWhere('name', 'view-customer')
                ->get(),

            'HR' => Permission::where('name', 'like', 'view-hr%')
                ->orWhere('name', 'like', 'create-hr%')
                ->orWhere('name', 'like', 'update-hr%')
                ->orWhere('name', 'like', 'delete-hr%')
                ->orWhere('name', 'view-customer')
                ->get(),

            'MARKETING' => Permission::where('name', 'like', 'view-marketing%')
                ->orWhere('name', 'like', 'create-marketing%')
                ->orWhere('name', 'like', 'update-marketing%')
                ->orWhere('name', 'like', 'delete-marketing%')
                ->orWhere('name', 'view-customer')
                ->get(),

            'MANAGE' => Permission::where('name', 'like', 'view-manage%')
                ->orWhere('name', 'like', 'create-manage%')
                ->orWhere('name', 'like', 'update-manage%')
                ->orWhere('name', 'like', 'delete-manage%')
                ->orWhere('name', 'view-customer')
                ->get(),

            'PROJECT' => Permission::where('name', 'like', 'view-project%')
                ->orWhere('name', 'like', 'create-project%')
                ->orWhere('name', 'like', 'update-project%')
                ->orWhere('name', 'like', 'delete-project%')
                ->orWhere('name', 'view-customer')
                ->get(),

            'PRODUCTION' => Permission::where('name', 'like', 'view-production%')
                ->orWhere('name', 'like', 'create-production%')
                ->orWhere('name', 'like', 'update-production%')
                ->orWhere('name', 'like', 'delete-production%')
                ->orWhere('name', 'view-customer')
                ->get(),

            'PURCHASE' => Permission::where('name', 'like', 'view-purchase%')
                ->orWhere('name', 'like', 'create-purchase%')
                ->orWhere('name', 'like', 'update-purchase%')
                ->orWhere('name', 'like', 'delete-purchase%')
                ->orWhere('name', 'view-customer')
                ->get(),

            'QC' => Permission::where('name', 'like', 'view-qc%')
                ->orWhere('name', 'like', 'create-qc%')
                ->orWhere('name', 'like', 'update-qc%')
                ->orWhere('name', 'like', 'delete-qc%')
                ->orWhere('name', 'view-customer')
                ->get(),

            'SERVICE' => Permission::where('name', 'like', 'view-service%')
                ->orWhere('name', 'like', 'create-service%')
                ->orWhere('name', 'like', 'update-service%')
                ->orWhere('name', 'like', 'delete-service%')
                ->orWhere('name', 'view-customer')
                ->get(),

            'STORE' => Permission::where('name', 'like', 'view-store%')
                ->orWhere('name', 'like', 'create-store%')
                ->orWhere('name', 'like', 'update-store%')
                ->orWhere('name', 'like', 'delete-store%')
                ->orWhere('name', 'view-customer')
                ->get(),
        ];

        // 7. Assign permissions to each role
        foreach ($roles as $roleName) {
            $role = Role::findByName($roleName);

            if ($roleName === 'ADMIN') {
                // ADMIN has all permissions
                $role->syncPermissions($allPermissions);
            } elseif (isset($rolePermissionsMap[$roleName])) {
                // Other roles have their specific permissions
                $role->syncPermissions($rolePermissionsMap[$roleName]);
            } else {
                // If role is not defined in the map, assign only 'view-customer'
                $viewCustomer = Permission::where('name', 'view-customer')->first();
                if ($viewCustomer) {
                    $role->syncPermissions([$viewCustomer]);
                }
            }
        }

        // 8. Create special users with specific passwords and unique fake phone numbers
        $specialUsers = [
            [
                'name' => 'Service User',
                'username' => 'service',
                'email' => 'service@hweejan.com.sg',
                'role' => 'SERVICE',
                'password' => '7e98v3zfg9vg6h',
            ],
            [
                'name' => 'Production User',
                'username' => 'production',
                'email' => 'production@hweejan.com.sg',
                'role' => 'PRODUCTION',
                'password' => '7e98v3zfg9vg6h',
            ],
            [
                'name' => 'Engineering User',
                'username' => 'engineering',
                'email' => 'engineering@hweejan.com.sg',
                'role' => 'ENGR',
                'password' => '7e98v3zfg9vg6h',
            ],
        ];

        foreach ($specialUsers as $specialUser) {
            $user = User::firstOrCreate(
                ['username' => $specialUser['username']],
                [
                    'name' => $specialUser['name'],
                    'email' => $specialUser['email'],
                    'password' => $specialUser['password'],
                    'phone' => fake()->unique()->phoneNumber, // Assign unique fake phone number
                ]
            );

            // Assign role to the user
            $roleName = $specialUser['role'];

            if (in_array($roleName, $roles)) {
                $user->assignRole($roleName);
            } else {
                $this->command->warn("Role '{$roleName}' does not exist. User '{$user->username}' was not assigned a role.");
            }
        }

        // 9. Import users from CSV file without headers
        $csvPath = database_path('seeders/data_2024nov11/users.csv');
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file does not exist at path: {$csvPath}");
            return;
        }

        $csv = Reader::createFromPath($csvPath, 'r');

        // Define custom headers since CSV does not have headers
        $headers = ['username', 'name', 'email', 'role']; // Define headers based on CSV column order
        $csv->setHeaderOffset(null); // Disable automatic header detection
        $csv->setDelimiter(','); // Set delimiter if different

        // Initialize faker unique generator
        $faker = fake();
        $faker->unique(true); // Reset unique generator

        foreach ($csv->getRecords() as $record) {
            // Map columns to headers
            $data = [];
            foreach ($record as $index => $value) {
                if (isset($headers[$index])) {
                    $data[$headers[$index]] = $value;
                }
            }

            // Create or find the user
            $user = User::firstOrCreate(
                ['username' => $data['username']],
                [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => 123456, // Default password
                    'phone' => $faker->unique()->phoneNumber, // Assign unique fake phone number
                ]
            );

            // Assign role to the user
            $roleName = $data['role'];

            if (in_array($roleName, $roles)) {
                $user->assignRole($roleName);
            } else {
                $this->command->warn("Role '{$roleName}' does not exist. User '{$user->username}' was not assigned a role.");
            }
        }

        // Optional: Reset unique generator to allow reuse if needed
        $faker->unique(false);
    }
}
