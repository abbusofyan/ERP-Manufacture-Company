<?php

use App\Http\Controllers\CacheController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Customer\AddressController;
use App\Http\Controllers\Customer\BankController;
use App\Http\Controllers\Customer\CustomerSaleOrderController;
use App\Http\Controllers\Customer\CustomerVehicleController;
use App\Http\Controllers\Customer\DiscountController;
use App\Http\Controllers\Customer\IndividualController;
use App\Http\Controllers\Customer\OrganisationController;
use App\Http\Controllers\Customer\PocController;
use App\Http\Controllers\Customer\SalesPersonController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EngineeringScheduleController;
use App\Http\Controllers\EngineeringWorkingHourController;
use App\Http\Controllers\GINController;
use App\Http\Controllers\GoodsReceiptController;
use App\Http\Controllers\MilestonesSettingController;
use App\Http\Controllers\Quotation\QuotationExportPartController;
use App\Http\Controllers\SaleOrder\SalesOrderExportPartController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionScheduleController;
use App\Http\Controllers\ProductionWorkingHourController;
use App\Http\Controllers\ProjectAppointmentController;
use App\Http\Controllers\ProjectContractController;
use App\Http\Controllers\ProjectOrderOutsourcedController;
use App\Http\Controllers\ProjectOrderReportController;
use App\Http\Controllers\ProjectOrderScheduleController;
use App\Http\Controllers\ProjectProcessDetailController;
use App\Http\Controllers\ProjectScheduleController;
use App\Http\Controllers\ProjectTechnicianController;
use App\Http\Controllers\Quotation\QuotationHygieneRoserController;
use App\Http\Controllers\Quotation\QuotationHygieneSikaController;
use App\Http\Controllers\Quotation\QuotationHygieneVikanController;
use App\Http\Controllers\Quotation\QuotationPartController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RefrigerationSaleController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\RequisitionToGINController;
use App\Http\Controllers\RequisitionToPOController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleOrder\SalesOrderHygieneRoserController;
use App\Http\Controllers\SaleOrder\SalesOrderHygieneSikaController;
use App\Http\Controllers\SaleOrder\SalesOrderHygieneVikanController;
use App\Http\Controllers\SaleOrder\SalesOrderPartController;
use App\Http\Controllers\SalesOrderECOController;
use App\Http\Controllers\ServiceAppointmentController;
use App\Http\Controllers\ServiceContractController;
use App\Http\Controllers\ServiceOrderOutsourcedController;
use App\Http\Controllers\ServiceOrderReportController;
use App\Http\Controllers\ServiceOrderScheduleController;
use App\Http\Controllers\ServiceScheduleController;
use App\Http\Controllers\ServiceInvoiceController;
use App\Http\Controllers\ServiceOrderAttachmentController;
use App\Http\Controllers\ServiceOrderController;
use App\Http\Controllers\ServiceOrderProcessController;
use App\Http\Controllers\ServiceOrderRequirementController;
use App\Http\Controllers\ServiceOrderRequirementUsedController;
use App\Http\Controllers\ServiceQuotationController;
use App\Http\Controllers\ServiceTechnicianController;
use App\Http\Controllers\ServiceWorkingHourController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockAdjustmentTypeController;
use App\Http\Controllers\StockDocumentController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\UnitOfMeasurementController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\Vendor\VendorAddressController;
use App\Http\Controllers\Vendor\VendorAttachmentController;
use App\Http\Controllers\Vendor\VendorPocController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\AssemblyController;
use App\Http\Controllers\ProductionProcessDetailController;
use App\Http\Controllers\ProductionOrderController;
use App\Http\Controllers\EngineeringOrderController;
use App\Http\Controllers\EngineeringProcessDetailController;
use App\Http\Controllers\ProcessController;
use App\Http\Controllers\VehicleSpecController;
use App\Http\Controllers\ProductionOrderAttachmentController;
use App\Http\Controllers\EngineeringOrderAttachmentController;
use App\Http\Controllers\FabricationController;
use App\Http\Controllers\ServiceProcessDetailController;
use App\Http\Controllers\ProjectOrderController;
use App\Http\Controllers\ProjectQuotationController;
use App\Http\Controllers\ProjectInvoiceController;
use App\Http\Controllers\ProjectOrderAttachmentController;
use App\Http\Controllers\ProjectOrderProcessController;
use App\Http\Controllers\ProjectOrderRequirementController;
use App\Http\Controllers\ProjectOrderRequirementUsedController;
use App\Http\Controllers\ProjectWorkingHourController;
use App\Http\Controllers\InventoryWarehouseController;
use App\Http\Controllers\FinishGoodsController;
use App\Http\Controllers\ItemMovementController;
use App\Http\Controllers\GSTController;
use App\Http\Controllers\PurchaseGoodsReturnController;
use App\Http\Controllers\UpdateSellingPriceController;
use App\Http\Controllers\Select2Controller;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

require __DIR__ . '/auth.php';

Route::middleware(['auth'])->group(function () {
    /** DATA QUERY **/
    Route::post('/data/users', [UserController::class, 'select2Query']);
    Route::post('/data/managers', [UserController::class, 'select2QueryManager']);
    Route::post('/data/customers', [CustomerController::class, 'select2Query']);
    Route::post('/data/products', [ProductController::class, 'select2Query']);
    Route::post('/data/products-by-category', [ProductController::class, 'select2QueryByCategory']);
    Route::post('/data/products-with-storage', [ProductController::class, 'select2QueryWithStorage']);
    Route::post('/data/products/get-prices-by-vendor', [ProductController::class, 'getPricesByVendor']);
    Route::post('/data/requisitions', [Select2Controller::class, 'select2QueryRequisitions']);
    Route::post('/data/requisitions/transaction-number', [Select2Controller::class, 'select2QueryRequisitionTransactionNumber']);
    Route::post('/data/quotations', [QuotationController::class, 'select2Query']);
    Route::post('/data/vehicle', [VehicleController::class, 'get']);
    Route::post('/data/vehicles', [VehicleController::class, 'select2Query']);
    Route::post('/data/refrigeration-sales/header', [RefrigerationSaleController::class, 'headerSelect2Query']);
    Route::post('/data/service-orders', [ServiceOrderController::class, 'select2Query']);
    Route::post('/data/service-appointments', [ServiceAppointmentController::class, 'select2Query']);
    Route::post('/data/service-quotations', [ServiceQuotationController::class, 'select2Query']);
    Route::post('/data/project-orders', [ProjectOrderController::class, 'select2Query']);
    Route::post('/data/project-appointments', [ProjectAppointmentController::class, 'select2Query']);
    Route::post('/data/project-quotations', [ProjectQuotationController::class, 'select2Query']);
    Route::post('/data/storage-items', [StorageController::class, 'select2Query']);
    Route::post('/data/requirements', [ServiceOrderRequirementController::class, 'select2Query']);
    Route::post('/data/vendors', [VendorController::class, 'select2Query']);
    Route::post('/data/assemblies', [AssemblyController::class, 'select2Query']);
    Route::post('/data/assembly/replace-item/items', [AssemblyController::class, 'getByItem']);
    Route::post('/data/categories', [CategoryController::class, 'select2Query']);
    Route::post('/data/processes', [ProcessController::class, 'select2Query']);
    Route::post('/data/vehicle-spec/get-item-options', [VehicleSpecController::class, 'select2Query']);
    Route::post('/data/vehicle-specs', [VehicleSpecController::class, 'select2QuerySpec']);
    Route::post('/data/production-orders', [ProductionOrderController::class, 'select2Query']);
    Route::post('/data/warehouse', [StoreController::class, 'select2Query']);
    Route::post('/data/warehouse-with-stock', [StoreController::class, 'select2QueryWithStock']);
    Route::post('/data/locations', [LocationController::class, 'select2Query']);
    Route::post('/data/stock-adjustment-type', [StockAdjustmentTypeController::class, 'select2Query']);
    Route::post('/data/store-has-product', [StoreController::class, 'select2QueryStoreHasProduct']);
    Route::post('/data/uom', [UnitOfMeasurementController::class, 'select2Query']);
    Route::post('/data/gst', [GSTController::class, 'select2Query']);

    /** PAGES **/
    Route::get('/', [CustomerController::class, 'index']);

    /** User **/
    Route::resource('/users', UserController::class);
    Route::resource('/roles', RoleController::class);

    /** Stores **/
    Route::resource('/stores', StoreController::class);
    Route::get('/stores/store-has-product/{product}', [StoreController::class, 'storeHasProduct']);
    Route::post('/stores/{store}/get-products', [StoreController::class, 'getProducts']);

    /** Locations **/
    Route::resource('/locations', LocationController::class);

    /** Customers **/
    Route::resource('/customers', CustomerController::class);
    Route::resource('/customers/organisation', OrganisationController::class);
    Route::resource('/customers/individual', IndividualController::class);
    Route::resource('/customers/salesperson', SalesPersonController::class);
    Route::post('/customers/{customer}/restore', [CustomerController::class, 'restore']);

    Route::resource('/customers/{customer}/poc', PocController::class);
    Route::get('/customers/{customer}/pocs/{poc}/mark-approve', [PocController::class, 'markApprove']);

    Route::resource('/customers/{customer}/addresses', AddressController::class);
    Route::post('/customers/{customer}/addresses/{address}/switch-status', [AddressController::class, 'switch']);
    Route::get('/customers/{customer}/addresses/{address}/mark-approve', [AddressController::class, 'markApprove']);
    Route::get('/customers/{customer}/addresses/{address}/duplicate', [AddressController::class, 'duplicate']);

    Route::resource('/customers/{customer}/banks', BankController::class);
    Route::post('/customers/{customer}/banks/{bank}/switch-status', [BankController::class, 'switch']);
    Route::get('/customers/{customer}/banks/{bank}/mark-approve', [BankController::class, 'markApprove']);
    Route::get('/customers/{customer}/banks/{bank}/duplicate', [BankController::class, 'duplicate']);

    Route::resource('/customers/{customer}/discounts', DiscountController::class);
    Route::post('/customers/{customer}/discounts/{discount}/switch-status', [DiscountController::class, 'switch']);
    Route::get('/customers/{customer}/discounts/{discount}/mark-approve', [DiscountController::class, 'markApprove']);
    Route::get('/customers/{customer}/discounts/{discount}/duplicate', [DiscountController::class, 'duplicate']);

    Route::resource('/customers/{customer}/invoices', CustomerSaleOrderController::class);

    Route::resource('/customers/{customer}/vehicles', CustomerVehicleController::class);

    /** Categories **/
    Route::resource('/categories', CategoryController::class);
    Route::post('/categories/{category}', [CategoryController::class, 'update']);
    Route::get('/categories/restore/{category_id}', [CategoryController::class, 'restore']);

    /** Types **/
    Route::resource('/types', StockAdjustmentTypeController::class);
    Route::get('/types/restore/{type_id}', [StockAdjustmentTypeController::class, 'restore']);

    /** UOM **/
    Route::resource('/uom', UnitOfMeasurementController::class);
    Route::get('/uom/restore/{uom_id}', [UnitOfMeasurementController::class, 'restore']);

    /** Vendors **/
    Route::resource('/vendors', VendorController::class);
    Route::resource('/vendors/{vendor}/poc', VendorPocController::class);
    Route::resource('/vendors/{vendor}/addresses', VendorAddressController::class);
    Route::get('/vendors/{vendor}/pocs/{poc}/mark-approve', [VendorPocController::class, 'markApprove']);
    Route::get('/vendors/{vendor}/addresses/{address}/mark-approve', [VendorAddressController::class, 'markApprove']);
    Route::get('/vendors/{vendor}/addresses/{address}/duplicate', [VendorAddressController::class, 'duplicate']);

    Route::resource('/vendors/{vendor}/attachments', VendorAttachmentController::class);

    /** Products **/
    Route::post('/products/get-last-non-inventory-code', [ProductController::class, 'getLastNonInventoryCode']);
    Route::resource('/products', ProductController::class);
    Route::post('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::post('/products/{product}/switch-status', [ProductController::class, 'updateStatus'])->name('products.updateStatus');
    Route::post('/products/{product}/upload-sales-photo', [ProductController::class, 'uploadSalesPhoto']);
    Route::post('/products/{product}/update-price', [ProductController::class, 'updatePrice']);
    Route::get('/products/{product}/{store}/get-stock-by-warehouse', [ProductController::class, 'getStockByWarehouse']);
    Route::get('/products-inactive', [ProductController::class, 'inactive']);

    /** inventory warehouse **/
    Route::resource('/inventory-warehouse', InventoryWarehouseController::class);

    /** Storages **/
    Route::resource('/storages', StorageController::class);
    Route::post('/storages/{storage}/assign-to-bin/{storageItem}', [StorageController::class, 'assignToBin']);

    Route::resource('/transfers', TransferController::class);
    Route::get('/transfer/copy/{transfer}', [TransferController::class, 'copy']);
    Route::post('/transfers/cancel/{transfer}', [TransferController::class, 'cancel']);
    Route::post('/transfer/send-for-approval/{transfer}', [TransferController::class, 'sendForApproval']);
    Route::post('/transfer/approve/{transfer}', [TransferController::class, 'approve']);



    Route::resource('/stock-adjustments', StockAdjustmentController::class);
    Route::post('/stock-adjustments/{stockAdjustment}', [StockAdjustmentController::class, 'update']);
    Route::resource('/stock-adjustments/{stockAdjustment}/documents', StockDocumentController::class);

    /** Order **/
    Route::resource('/orders', OrderController::class);
    Route::post('/orders/update-status/{order}', [OrderController::class, 'updateStatus']);
    Route::post('/orders/generate-goods-receipt/{order}', [OrderController::class, 'generateGoodsReceipt']);
    Route::get('/orders/{order}/pdf', [OrderController::class, 'pdf']);
    Route::get('/orders/get-items/{order}', [OrderController::class, 'getItem']);

    /** Requisition **/
    Route::resource('/requisitions', RequisitionController::class);
    // Route::post('/requisitions/{requisitionItem}/update-release', [RequisitionController::class, 'updateRelease']);
    Route::resource('/requisitions-to-order', RequisitionToPOController::class)->except(['show']);
    Route::get('/requisitions-release', [RequisitionController::class, 'release']);
    Route::get('/requisitions-to-order/convert', [RequisitionToPOController::class, 'convert'])->name('requisitions-to-order.convert');
    Route::get('/requisitions-to-order/add-to-existing-po', [RequisitionToPOController::class, 'addToExistingPO'])->name('requisitions-to-order.addToExistingPO');
    Route::post('/requisitions-to-order/add-to-existing-po', [RequisitionToPOController::class, 'submitAddToExistingPO'])->name('requisition-to-order.submitAddToExistingPO');
    Route::post('/requisitions-to-order/update-status', [RequisitionToPOController::class, 'cancel']);
    Route::post('/requisitions/item-checked', [RequisitionController::class, 'itemChecked']);
    Route::post('/requisitions/{requisition}/send-for-approval', [RequisitionController::class, 'sendForApproval']);
    Route::get('/requisitions/{requisition}/approve', [RequisitionController::class, 'approve']);
    Route::post('/requisitions/{requisition}/reject', [RequisitionController::class, 'reject']);
    Route::post('/requisitions/{requisition}/void', [RequisitionController::class, 'void']);
    Route::post('/requisitions/{requisition}/cancel-item/{item}', [RequisitionController::class, 'cancelItem'])->name('requisitions.cancel-item');
    Route::post('/requisitions/{requisitionItem}/edit-remark', [RequisitionController::class, 'editRemark'])->name('requisitions.edit-remark');
    Route::get('/requisitions/{requisition}/duplicate', [RequisitionController::class, 'duplicate'])->name('requisitions.duplicate');
    Route::get('/requisitions/{requisitionItem}/cancel-pending-order', [RequisitionController::class, 'cancelPendingOrder'])->name('requisitions.cancel-pending-order');

    /** Vehicles **/
    Route::resource('/vehicles', VehicleController::class);

    /** GIN **/
    Route::resource('goods-receipts', GoodsReceiptController::class);
    Route::post('/goods-receipts/update-item-status', [GoodsReceiptController::class, 'updateReturned']);
    Route::get('/goods-receipts/{goodsReceipt}/pdf', [GoodsReceiptController::class, 'pdf']);
    Route::post('/goods-receipts/{goodsReceipt}/set-void', [GoodsReceiptController::class, 'setVoid']);
    Route::post('/goods-receipts/{goodsReceipt}/upload-document', [GoodsReceiptController::class, 'uploadDocument']);
    Route::post('/goods-receipts/{goodsReceipt}/delete-document/{document}', [GoodsReceiptController::class, 'deleteDocument']);
    Route::post('/goods-receipts/{goodsReceipt}/receive-item', [GoodsReceiptController::class, 'receiveItem']);
    Route::get('/goods-receipts/{goodsReceipt}/confirm', [GoodsReceiptController::class, 'confirm']);
    Route::get('/goods-receipts/{goodsReceipt}/convert-to-goods-return', [GoodsReceiptController::class, 'convertToGoodsReturn']);
    Route::get('/goods-receipts/{goodsReceipt}/redirect-to-goods-return', [GoodsReceiptController::class, 'redirectToGoodsReturn']);
    Route::resource('/requisitions-to-goods-issue-note', RequisitionToGINController::class);
    Route::post('/requisitions-to-goods-issue-note/convert', [RequisitionToGINController::class, 'convert']);
    Route::resource('/goods-issue-notes', GINController::class);
    Route::post('/goods-issue-notes/{gin}/update-remark', [GINController::class, 'updateRemark']);
    // Route::get('/goods-issue-notes/{gin}/return', [GINController::class, 'return']);
    Route::post('/goods-issue-notes/{gin}/return', [GINController::class, 'returnUpdate']);
    Route::post('/goods-issue-notes/{gin}/confirm', [GINController::class, 'confirm']);
    Route::get('/incoming-goods', [OrderController::class, 'incoming']);


    /** SALES ORDER **/
    Route::prefix('/{shipment}/')->where(['shipment' => 'local|overseas'])->group(function () {
        /** Refrigeration Sales **/
        Route::get('refrigeration-sales/download-co-file', [RefrigerationSaleController::class, 'downloadCOFile']);
        Route::post('refrigeration-sales/{refrigeration_sale}', [RefrigerationSaleController::class, 'update']);
        Route::post('refrigeration-sales/{refrigeration_sale}/upload-photo', [RefrigerationSaleController::class, 'updatePhoto']);
        Route::get('refrigeration-sales/{refrigeration_sale}/generate-proforma-invoice', [RefrigerationSaleController::class, 'generateProformaInvoicePdf']);
        Route::get('refrigeration-sales/{refrigeration_sale}/generate-invoice', [RefrigerationSaleController::class, 'generateInvoicePdf']);
        Route::resource('refrigeration-sales', RefrigerationSaleController::class);

        /** Sale order **/
        Route::resource('sales-order-eco', SalesOrderECOController::class);
        Route::get('sales-order-eco/{order}/edit-milestone', [SalesOrderECOController::class, 'editMilestone']);
        Route::post('sales-order-eco/{order}/edit-milestone', [SalesOrderECOController::class, 'updateMilestone']);;
        Route::post('sales-order-eco/update-status/{order}', [SalesOrderECOController::class, 'updateStatus']);
        Route::put('sales-order-eco/{order}/update-detail', [SalesOrderECOController::class, 'updateDetail']);


        /** Part **/
        Route::resource('parts', QuotationPartController::class);
        Route::post('parts/{part}/upload-photo', [QuotationPartController::class, 'updatePhoto']);
        Route::resource('sales-order-parts', SalesOrderPartController::class);

        /** Export Parts **/
        Route::resource('export-parts', QuotationExportPartController::class);
        Route::post('export-parts/{part}/upload-photo', [QuotationExportPartController::class, 'updatePhoto']);
        Route::resource('sales-order-export-parts', SalesOrderExportPartController::class);

        /** Vikan **/
        Route::resource('hygienes-vikan', QuotationHygieneVikanController::class);
        Route::post('hygienes-vikan/{hygienes_vikan}/upload-photo', [QuotationHygieneVikanController::class, 'updatePhoto']);
        Route::resource('sales-order-hygienes-vikan', SalesOrderHygieneVikanController::class);

        /** Sika **/
        Route::resource('hygienes-sika', QuotationHygieneSikaController::class);
        Route::post('hygienes-sika/{hygienes_sika}/upload-photo', [QuotationHygieneSikaController::class, 'updatePhoto']);
        Route::resource('sales-order-hygienes-sika', SalesOrderHygieneSikaController::class);

        /** Roser **/
        Route::resource('hygienes-roser', QuotationHygieneRoserController::class);
        Route::post('hygienes-roser/{hygienes_roser}/upload-photo', [QuotationHygieneRoserController::class, 'updatePhoto']);
        Route::resource('sales-order-hygienes-roser', SalesOrderHygieneRoserController::class);
    });


    Route::resource('/milestones-settings', MilestonesSettingController::class);
    Route::get('/milestones-settings/restore/{milestones_setting_id}', [MilestonesSettingController::class, 'restore']);

    //--------------------------------------------------------------------------------

    /** Service Appointment **/
    Route::resource('/service-appointments', ServiceAppointmentController::class);
    Route::post('/service-appointments/{service_appointment}/convert-draft', [ServiceAppointmentController::class, 'convertDraft']);
    Route::post('/service-appointments/{service_appointment}/convert-service-order', [ServiceAppointmentController::class, 'convertServiceOrder']);

    /** Service Order **/
    Route::resource('/service-orders', ServiceOrderController::class);
    Route::post('/service-orders/{service_order}/update-detail', [ServiceOrderController::class, 'updateDetail']);
    Route::post('/service-orders/{service_order}/start-service', [ServiceOrderController::class, 'startService']);

    /** Service Order Process **/
    Route::resource('/service-orders/{service_order}/processes', ServiceOrderProcessController::class);
    Route::post('/service-orders/{service_order}/processes/create-first', [ServiceOrderProcessController::class, 'storeFirst']);
    Route::post('/service-orders/{service_order}/processes/{process}/pause', [ServiceOrderProcessController::class, 'pause']);
    Route::post('/service-orders/{service_order}/processes/{process}/continue', [ServiceOrderProcessController::class, 'continue']);
    Route::post('/service-orders/{service_order}/processes/{process}/complete', [ServiceOrderProcessController::class, 'completed']);
    Route::post('/service-orders/{service_order}/processes/{process}/set-standard-time', [ServiceOrderProcessController::class, 'setStandardTime']);
    Route::post('/service-orders/{service_order}/processes/vehicle-collected', [ServiceOrderProcessController::class, 'collected']);

    /** Service Order Attachments **/
    Route::resource('/service-orders/{service_order}/attachments', ServiceOrderAttachmentController::class);

    /** Service Order Requirement **/
    Route::resource('/service-orders/{service_order}/requirements', ServiceOrderRequirementController::class);

    Route::post('/service-orders/{service_order}/requirements-used/generate-quotation', [ServiceOrderRequirementUsedController::class, 'generateQuotation']);
    Route::get('/service-orders/{service_order}/requirements-used/requisitions', [ServiceOrderRequirementUsedController::class, 'showRequisitions']);
    Route::get('/service-orders/{service_order}/requirements-used/live-stock-status', [ServiceOrderRequirementUsedController::class, 'showRequisitionStockStatus']);
    Route::resource('/service-orders/{service_order}/requirements-used', ServiceOrderRequirementUsedController::class);

    /** Service Order Reports **/
    Route::resource('/service-orders/{service_order}/reports', ServiceOrderReportController::class);

    /** Service Order Outsourced **/
    Route::resource('/service-orders/{service_order}/outsourced', ServiceOrderOutsourcedController::class);

    /** Service Order Schedule **/
    Route::get('/service-orders-schedule', [ServiceOrderScheduleController::class, 'index']);
    Route::get('/service-orders-schedule/{service_order}', [ServiceOrderScheduleController::class, 'index']);

    /** Service Quotations **/
    Route::resource('/service-quotations', ServiceQuotationController::class);
    Route::post('/service-quotations/{service_quotation}', [ServiceQuotationController::class, 'update']);
    Route::post('/service-quotations/{service_quotation}/upload-photo', [ServiceQuotationController::class, 'updatePhoto']);
    Route::post('/service-quotations/{service_quotation}/void', [ServiceQuotationController::class, 'void']);
    Route::get('/service-quotations/{service_quotation}/generate-proforma-invoice', [ServiceQuotationController::class, 'generateProformaInvoicePdf']);
    Route::get('/service-quotations/{service_quotation}/generate-invoice', [ServiceQuotationController::class, 'generateInvoicePdf']);

    /** Service Invoices **/
    Route::resource('/service-invoices', ServiceInvoiceController::class);
    Route::post('/service-invoices/{service_invoice}/paid', [ServiceInvoiceController::class, 'storePaid']);
    Route::post('/service-invoices/{service_invoice}/generate-delivery-order', [ServiceInvoiceController::class, 'generateDeliveryOrder']);

    /** Service Contract **/
    Route::resource('/service-contracts', ServiceContractController::class);
    Route::post('/service-contracts/{service_contract}/upload-photo', [ServiceContractController::class, 'updatePhoto']);
    Route::post('/service-contracts/{service_contract}/void', [ServiceContractController::class, 'void']);
    Route::post('/service-contracts/{service_contract}/early-termination', [ServiceContractController::class, 'earlyTermination']);
    Route::get('/service-contracts/{service_contract}/download', [ServiceContractController::class, 'downloadPdf']);

    /** Service Schedule **/
    Route::resource('/service-schedules', ServiceScheduleController::class);

    /** Service Schedule **/
    Route::resource('/service-technicians', ServiceTechnicianController::class);

    /** Service Working Hours **/
    Route::resource('/service-working-hours', ServiceWorkingHourController::class);

    /** Service Working Hours **/
    Route::resource('/service-process-detail', ServiceProcessDetailController::class);



    //--------------------------------------------------------------------------------

    /** Project Appointment **/
    Route::resource('/project-appointments', ProjectAppointmentController::class);
    Route::post('/project-appointments/{project_appointment}/convert-draft', [ProjectAppointmentController::class, 'convertDraft']);
    Route::post('/project-appointments/{project_appointment}/convert-project-order', [ProjectAppointmentController::class, 'convertProjectOrder']);

    /** Project Order **/
    Route::resource('/project-orders', ProjectOrderController::class);
    Route::post('/project-orders/{project_order}/update-detail', [ProjectOrderController::class, 'updateDetail']);
    Route::post('/project-orders/{project_order}/start-project', [ProjectOrderController::class, 'startProject']);

    /** Project Order Process **/
    Route::resource('/project-orders/{project_order}/processes', ProjectOrderProcessController::class);
    Route::post('/project-orders/{project_order}/processes/create-first', [ProjectOrderProcessController::class, 'storeFirst']);
    Route::post('/project-orders/{project_order}/processes/{process}/pause', [ProjectOrderProcessController::class, 'pause']);
    Route::post('/project-orders/{project_order}/processes/{process}/continue', [ProjectOrderProcessController::class, 'continue']);
    Route::post('/project-orders/{project_order}/processes/{process}/complete', [ProjectOrderProcessController::class, 'completed']);
    Route::post('/project-orders/{project_order}/processes/{process}/set-standard-time', [ProjectOrderProcessController::class, 'setStandardTime']);
    Route::post('/project-orders/{project_order}/processes/vehicle-collected', [ProjectOrderProcessController::class, 'collected']);

    /** Project Order Attachments **/
    Route::resource('/project-orders/{project_order}/attachments', ProjectOrderAttachmentController::class);

    /** Project Order Requirement **/
    Route::resource('/project-orders/{project_order}/requirements', ProjectOrderRequirementController::class);

    Route::post('/project-orders/{project_order}/requirements-used/generate-quotation', [ProjectOrderRequirementUsedController::class, 'generateQuotation']);
    Route::get('/project-orders/{project_order}/requirements-used/requisitions', [ProjectOrderRequirementUsedController::class, 'showRequisitions']);
    Route::get('/project-orders/{project_order}/requirements-used/live-stock-status', [ProjectOrderRequirementUsedController::class, 'showRequisitionStockStatus']);
    Route::resource('/project-orders/{project_order}/requirements-used', ProjectOrderRequirementUsedController::class);

    /** Project Order Reports **/
    Route::resource('/project-orders/{project_order}/reports', ProjectOrderReportController::class);

    /** Project Order Outsourced **/
    Route::resource('/project-orders/{project_order}/outsourced', ProjectOrderOutsourcedController::class);

    /** Project Order Schedule **/
    Route::get('/project-orders-schedule', [ProjectOrderScheduleController::class, 'index']);
    Route::get('/project-orders-schedule/{project_order}', [ProjectOrderScheduleController::class, 'index']);

    /** Project Quotations **/
    Route::resource('/project-quotations', ProjectQuotationController::class);
    Route::post('/project-quotations/{project_quotation}', [ProjectQuotationController::class, 'update']);
    Route::post('/project-quotations/{project_quotation}/upload-photo', [ProjectQuotationController::class, 'updatePhoto']);
    Route::post('/project-quotations/{project_quotation}/void', [ProjectQuotationController::class, 'void']);
    Route::get('/project-quotations/{project_quotation}/generate-proforma-invoice', [ProjectQuotationController::class, 'generateProformaInvoicePdf']);
    Route::get('/project-quotations/{project_quotation}/generate-invoice', [ProjectQuotationController::class, 'generateInvoicePdf']);

    /** Project Invoices **/
    Route::resource('/project-invoices', ProjectInvoiceController::class);
    Route::post('/project-invoices/{project_invoice}/paid', [ProjectInvoiceController::class, 'storePaid']);
    Route::post('/project-invoices/{project_invoice}/generate-delivery-order', [ProjectInvoiceController::class, 'generateDeliveryOrder']);

    /** Project Contract **/
    Route::resource('/project-contracts', ProjectContractController::class);
    Route::post('/project-contracts/{project_contract}/upload-photo', [ProjectContractController::class, 'updatePhoto']);
    Route::post('/project-contracts/{project_contract}/void', [ProjectContractController::class, 'void']);
    Route::post('/project-contracts/{project_contract}/early-termination', [ProjectContractController::class, 'earlyTermination']);
    Route::get('/project-contracts/{project_contract}/download', [ProjectContractController::class, 'downloadPdf']);

    /** Project Schedule **/
    Route::resource('/project-schedules', ProjectScheduleController::class);

    /** Project Technicians **/
    Route::resource('/project-technicians', ProjectTechnicianController::class);

    /** Project Working Hours **/
    Route::resource('/project-working-hours', ProjectWorkingHourController::class);

    /** Project Process Detail **/
    Route::resource('/project-process-detail', ProjectProcessDetailController::class);




    /** Assembly **/
    Route::get('/assembly/update-item', [AssemblyController::class, 'updateItem']);
    Route::get('/assembly/replace-item', [AssemblyController::class, 'replaceItem']);
    Route::post('/assembly/replace-item', [AssemblyController::class, 'processReplaceItem']);
    Route::resource('/assembly', AssemblyController::class);
    Route::get('/assembly/toggle-status/{assembly_id}/{status}', [AssemblyController::class, 'toggleStatus']);
    Route::resource('/vendors/{vendor}/attachments', VendorAttachmentController::class);
    Route::get('/assembly-inactive', [AssemblyController::class, 'inactive']);
    Route::get('/assembly/{assembly}/duplicate', [AssemblyController::class, 'duplicate']);
    Route::get('/assembly/{assembly}/getItems', [AssemblyController::class, 'getItems']);

    /** Production Order **/
    Route::resource('/production-order', ProductionOrderController::class);
    Route::post('/production-order/{ProductionOrder}/update-item-used', [ProductionOrderController::class, 'updateItemUsed']);
    Route::post('/production-order/{ProductionOrder}/update-process', [ProductionOrderController::class, 'updateProcess']);
    Route::get('/production-order/start-process/{production_order}/{productionOrderProcess}', [ProductionOrderController::class, 'startProcess']);
    Route::get('/production-order/stop-process/{production_order}/{productionOrderProcess}', [ProductionOrderController::class, 'stopProcess']);
    Route::get('/production-order/detail-process/{production_order}/{process}', [ProductionOrderController::class, 'detailProcess']);
    Route::post('/production-order/submit-process-detail/{process}', [ProductionOrderController::class, 'submitDetailProcess']);

    Route::get('/production-orders-schedule', [ProductionScheduleController::class, 'index']);
    Route::get('/production-orders-schedule/{production_order}', [ProductionScheduleController::class, 'index']);

    /** production order attachment **/
    Route::resource('/production-order-attachment', ProductionOrderAttachmentController::class);

    /** production process detail **/
    Route::resource('/production-process-detail', ProductionProcessDetailController::class);

    /** Production Working Hours **/
    Route::resource('/production-working-hours', ProductionWorkingHourController::class);

    /** Engineering Order **/
    Route::resource('/engineering-order', EngineeringOrderController::class);
    Route::post('/engineering-order/{EngineeringOrder}/update-item-used', [EngineeringOrderController::class, 'updateItemUsed']);
    Route::post('/engineering-order/{EngineeringOrder}/update-process', [EngineeringOrderController::class, 'updateProcess']);
    Route::get('/engineering-order/start-process/{engineeringOrder}/{productionOrder}/{productionOrderProcess}', [EngineeringOrderController::class, 'startProcess']);
    Route::get('/engineering-order/stop-process/{engineeringOrder}/{productionOrder}/{productionOrderProcess}', [EngineeringOrderController::class, 'stopProcess']);

    Route::get('/engineering-orders-schedule', [EngineeringScheduleController::class, 'index']);
    Route::get('/engineering-orders-schedule/{production_order}', [EngineeringScheduleController::class, 'index']);

    /** production order attachment **/
    Route::resource('/engineering-order-attachment', EngineeringOrderAttachmentController::class);

    /** engineering process detail **/
    Route::resource('/engineering-process-detail', EngineeringProcessDetailController::class);

    /** Engineering Working Hours **/
    Route::resource('/engineering-working-hours', EngineeringWorkingHourController::class);

    /** process detail **/
    Route::get('/process/get-by-department/{department}', [ProcessController::class, 'getByDepartment']);

    /** vehicle spec **/
    Route::resource('/vehicle-spec', VehicleSpecController::class);

    /** fabrication **/
    Route::resource('/fabrication', FabricationController::class);

    /** Finish Goods **/
    Route::resource('/finish-goods', FinishGoodsController::class)->except(['show']);;
    Route::get('/finish-goods/{finishGoodsTransaction}', [FinishGoodsController::class, 'show']);
    Route::get('/finish-goods/toggle-status/{finish_goods_id}/{status}', [FinishGoodsController::class, 'toggleStatus']);
    Route::post('/finish-goods/{store}/fetch-item-qty', [FinishGoodsController::class, 'fetchItemQty']);

    /** Item Movement **/
    Route::resource('/item-movement', ItemMovementController::class);

    /** Purchase Goods Return **/
    Route::resource('/purchase-goods-return', PurchaseGoodsReturnController::class);

    Route::get('/update-selling-price/download-item-list/{category_id}', [UpdateSellingPriceController::class, 'downloadItemList']);
    Route::resource('/update-selling-price', UpdateSellingPriceController::class);

});

Route::get('/create-symlink', function () {
    Artisan::call('storage:link');
    return "Storage link created successfully!";
});

Route::get('/clear-cache', [CacheController::class, 'clearCache']);

Route::get('/run-migrate', function () {
    Artisan::call('migrate');
    return 'Migration completed!';
});

Route::get('/run-migrate-rollback', function () {
    Artisan::call('migrate:rollback');
    return 'Migration completed!';
});

Route::get('/run-seeder-by-class', function () {
    $class = request()->get('class');
    Artisan::call('db:seed --class=' . $class);
    return 'Seeder completed!';
});

Route::get('/run-seeder', function () {
    Artisan::call('db:seed');
    return 'Seeder completed!';
});

Route::get('/run-migrate-fresh-seed', function () {
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');
    return 'Migration and seeding completed!';
});

Route::get('/run-queue-work', function () {
    Artisan::call('queue:work');
    return 'Queue has started!';
});
