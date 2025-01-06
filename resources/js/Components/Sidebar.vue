<template>
    <aside id="sidebar">
        <div class="head">
            <Link class="logo" href="/">
                <div class="image">
                    <img src="../../assets/img/Logo.svg" height="30"/>
                </div>
            </Link>
            <div class="toggle-btn"><img src="../../assets/img/ic-1.svg" alt=""></div>
        </div>

        <nav class="menu">
            <ul class="menu-list">
                <!--Start Customer-->
                <li v-if="permissionsIncludes(['view-customer'])" class="menu-item" :class="{ active: isComponentActive($page,['Customer']) }" @click="open = 'customers'">
                    <Link :class="{ active: isComponentActive($page,['Customer']) }" class="menu-link" href="/customers">
                        <span class="icon"><img src="../../assets/img/user.svg" alt=""></span>
                        <span class="text">Customer</span>
                    </Link>
                </li>
                <!--End Customer-->

                <!--Start Vendor-->
                <li v-if="permissionsIncludes(['view-vendor'])" class="menu-item" :class="{ active: isComponentActive($page,['Vendor']) }">
                    <Link class="menu-link" :class="{ active: isComponentActive($page,['Vendor']) }" href="/vendors">
                        <span class="icon fa-regular fa-store"></span>
                        <span class="text">Vendors</span>
                    </Link>
                </li>
                <!--End Vendor-->

                <!--Start Purchases-->
                <li v-if="permissionsIncludes(['view-requisition', 'view-goods_receipt', 'view-order'])"
                    class="menu-item has-dropdown">
                    <a class="menu-link" :class="{ active: isComponentActive($page,['Requisition/', 'RequisitionToPO/', 'Order', 'GoodsReceipt', 'PurchaseGoodsReturn'])}" href="javascript:void(0)">
                        <span class="icon"><img src="../../assets/img/inventory.svg" alt="Inventory"></span>
                        <span class="text">Purchase</span><em class="icon-dropdown fa-regular fa-angle-down"></em>
                    </a>
                    <ul class="nav-sub" :style="{ display: isComponentActive($page,['Requisition/', 'RequisitionToPO/', 'Order', 'GoodsReceipt', 'PurchaseGoodsReturn']) ? 'block' : 'none' }">
                        <li v-if="permissionsIncludes(['view-requisition'])">
                            <Link :class="{ active: $page.component.startsWith('Requisition/') }" href="/requisitions">Requisition</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-requisition', 'view-order'])">
                            <Link :class="{ active: $page.component.startsWith('RequisitionToPO/') }" href="/requisitions-to-order">Requisition to PO</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-order'])">
                            <Link :class="{ active: $page.component.startsWith('Order') && !$page.component.startsWith('Order/Incoming') }" href="/orders">Purchase Order</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-goods-receipt'])">
                            <Link :class="{ active: $page.component.startsWith('GoodsReceipt') }" href="/goods-receipts">Goods Receipt</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-purchase_goods_return'])">
                            <Link :class="{ active: $page.component.startsWith('PurchaseGoodsReturn') }" href="/purchase-goods-return">Goods Return</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-order'])">
                            <Link :class="{ active: $page.component.startsWith('Order/Incoming') }" href="/incoming-goods">Incoming Goods</Link>
                        </li>
                    </ul>
                </li>
                <!--End Purchases-->

                <!--Start Inventories-->
                <li v-if="permissionsIncludes(['view-product','view-storage','view-stock','view-transfer'])" class="menu-item has-dropdown">
                    <a class="menu-link" :class="{ active: isComponentActive($page,['Product', 'Storage', 'Stock', 'Transfer', 'UOM', 'Category', 'Types', 'InventoryWarehouse', 'FinishGoods', 'ItemMovement', 'RequisitionToGIN', 'GIN', 'UpdateSellingPrice'])}" href="javascript:void(0)">
                        <span class="icon"><img src="../../assets/img/inventory.svg" alt="Inventory"></span>
                        <span class="text">Inventory</span><em class="icon-dropdown fa-regular fa-angle-down"></em>
                    </a>
                    <ul class="nav-sub" :style="{ display:$page.url.includes('/products') || isComponentActive($page,['Storage', 'Stock', 'Transfer', 'UOM', 'Category', 'Types', 'InventoryWarehouse', 'FinishGoods', 'ItemMovement', 'RequisitionToGIN', 'GIN', 'UpdateSellingPrice']) ? 'block' : 'none' }">
                        <li v-if="permissionsIncludes(['view-product'])">
                            <Link
                                :class="{ active: $page.url.startsWith('/products') && !$page.url.startsWith('/products-inactive') }"
                                href="/products">
                                List
                            </Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-product'])">
                            <Link
                                :class="{ active: $page.url.startsWith('/products-inactive') }"
                                href="/products-inactive">
                                Inactive
                            </Link>
                        </li>
                        <li v-if="permissionsIncludes(['update-product'])">
                            <Link :class="{ active: $page.component.startsWith('UpdateSellingPrice') }" href="/update-selling-price/create">Update Selling Price</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-storage'])">
                            <Link :class="{ active: $page.component.startsWith('Storage') }" href="/storages">Storage</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-stock'])">
                            <Link :class="{ active: $page.component.startsWith('Stock') }" href="/stock-adjustments">Item Adjustment</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-transfer'])">
                            <Link :class="{ active: $page.component.startsWith('Transfer') }" href="/transfers">Item Transfer</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-product'])">
                            <Link :class="{ active: $page.url.startsWith('/inventory-warehouse') }" href="/inventory-warehouse">
                                Inventory Warehouse
                            </Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-transfer'])">
                            <Link :class="{ active: $page.component.startsWith('ItemMovement') }" href="/item-movement">Item Movement</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-product'])">
                            <Link :class="{ active: $page.component.startsWith('FinishGoods') }" href="/finish-goods">Assembly to FG</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-requisition_to_gin'])">
                            <Link :class="{ active: $page.component.startsWith('RequisitionToGIN/') }" href="/requisitions-to-goods-issue-note">Requisition to Goods Issue Note</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-gin'])">
                            <Link :class="{ active: $page.component.startsWith('GIN/') }" href="/goods-issue-notes">Goods Issue Note</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-uom','view-category'])">
                            <a class="menu-link" href="javascript:void(0)">
                                <span class="text">Setting</span><em class="icon-dropdown fa-regular fa-angle-down"></em>
                            </a>
                            <ul class="nav-sub" :style="{ display: isComponentActive($page,['UOM', 'Category', 'Types']) ? 'block' : 'none' }">
                                <li v-if="permissionsIncludes('view-uom')">
                                    <Link :class="{ active: $page.component.startsWith('UOM') }" href="/uom">UOM</Link>
                                </li>
                                <li v-if="permissionsIncludes('view-category')">
                                    <Link :class="{ active: $page.component.startsWith('Category') }" href="/categories">Categories</Link>
                                </li>
                                <li v-if="permissionsIncludes('view-category')">
                                    <Link :class="{ active: $page.component.startsWith('Types') }" href="/types">Item Adjustment Type</Link>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </li>

                <!--Start Location-->
                <li v-if="permissionsIncludes(['view-store','view-location'])" class="menu-item has-dropdown" :class="{ active: isComponentActive($page,['Store','Location'])}">
                    <a class="menu-link" href="javascript:void(0)" :class="{ active: $page.component.startsWith('Store') || $page.component.startsWith('Location')}">
                        <span class="icon"><img src="../../assets/img/location.svg" alt="Location"></span>
                        <span class="text">Location</span><em class="icon-dropdown fa-regular fa-angle-down"></em>
                    </a>
                    <ul class="nav-sub" :style="{ display: isComponentActive($page,['Store','Location']) ? 'block' : 'none' }">
                        <li v-if="permissionsIncludes(['view-location'])">
                            <Link :class="{ active: $page.component.startsWith('Location') }" href="/locations">Location</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-store'])">
                            <Link :class="{ active: $page.component.startsWith('Store') }" href="/stores">Warehouse</Link>
                        </li>
                    </ul>
                </li>
                <!--End Location-->

                <!--Start Assembly-->
                <li v-if="permissionsIncludes(['view-assembly'])" class="menu-item has-dropdown" :class="{ active: isComponentActive($page,['Assembly'])}">
                    <a class="menu-link" href="javascript:void(0)" :class="{ active: $page.component.startsWith('Store') || $page.component.startsWith('Assembly')}">
                        <span class="icon"><img src="../../assets/img/assembly.svg" alt="Assembly"></span>
                        <span class="text">Assembly</span><em class="icon-dropdown fa-regular fa-angle-down"></em>
                    </a>
                    <ul class="nav-sub" :style="{ display: isComponentActive($page,['Assembly']) ? 'block' : 'none' }">
                        <li v-if="permissionsIncludes(['view-assembly'])">
                            <Link
                                :class="{ active: $page.url.startsWith('/assembly') && !$page.url.startsWith('/assembly-inactive') }"
                                href="/assembly">
                                Assembly
                            </Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-assembly'])">
                            <Link
                                :class="{ active: $page.url.startsWith('/assembly-inactive') }"
                                href="/assembly-inactive">
                                Inactive
                            </Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-assembly'])">
                            <Link
                                :class="{ active: $page.url.startsWith('/update-item') }"
                                href="/assembly/update-item">
                                Update Item
                            </Link>
                        </li>
                    </ul>
                </li>
                <!--End Assembly-->

                <!--Start Finish Goods-->
                <!-- <li v-if="permissionsIncludes(['view-assembly'])" class="menu-item has-dropdown" :class="{ active: isComponentActive($page,['FinishGoods'])}">
                    <a class="menu-link" href="javascript:void(0)" :class="{ active: $page.component.startsWith('FinishGoods')}">
                        <span class="icon"><img src="../../assets/img/assembly.svg" alt="Assembly"></span>
                        <span class="text">Finish Goods</span><em class="icon-dropdown fa-regular fa-angle-down"></em>
                    </a>
                    <ul class="nav-sub" :style="{ display: isComponentActive($page,['FinishGoods']) ? 'block' : 'none' }">
                        <li v-if="permissionsIncludes(['view-assembly'])">
                            <Link
                                :class="{ active: $page.url.startsWith('/finish-goods') && !$page.url.startsWith('/finish-goods-inactive') }"
                                href="/finish-goods">
                                List
                            </Link>
                        </li>
                    </ul>
                </li> -->
                <!--End Finish Goods-->

                <!--Start Sales Order-->
                <li v-if="permissionsIncludes(['view-quotation', 'view-refrigeration_sale', 'view-sales_order', 'view-sales_order_eco', 'view-hygiene'])" class="menu-item has-dropdown" :class="{ active: isComponentActive($page,['Quotation','SalesOrder','RefrigerationSale','SalesOrderECO', 'Hygiene', 'VehicleSpec', 'MilestonesSetting'])}">
                    <a class="menu-link" href="javascript:void(0)">
                        <span class="icon"><img src="../../assets/img/documents.svg" alt="Sales Order"></span>
                        <span class="text">Sales Order</span><em class="icon-dropdown fa-regular fa-angle-down"></em>
                    </a>
                    <ul class="nav-sub" :style="{ display: isComponentActive($page,['Quotation','SalesOrder','RefrigerationSale','SalesOrderECO', 'Hygiene', 'VehicleSpec', 'MilestonesSetting']) ? 'block' : 'none' }">
                        <!-- Local Sales -->
                        <li class="menu-item has-dropdown" :class="{ active: isUrlActive($page,['/local/']) }">
                            <a class="menu-link" href="javascript:void(0)">
                                <span class="text">Local</span><em class="icon-dropdown fa-regular fa-angle-down"></em>
                            </a>
                            <ul class="nav-sub" :style="{ display: isUrlActive($page,['/local/']) ? 'block' : 'none' }">
                                <!-- Refrigeration Sales -->
                                <li class="has-dropdown">
                                    <a class="menu-link" :class="{ active: isUrlActive($page,['/local/refrigeration-sales', '/local/sales-order-eco']) }" href="javascript:void(0)">
                                        <span>Refrigeration Sales</span>
                                        <em class="icon-dropdown fa-regular fa-angle-down"></em>
                                    </a>
                                    <ul class="nav-sub" :style="{ display: isUrlActive($page,['/local/refrigeration-sales', '/local/sales-order-eco']) ? 'block' : 'none' }">
                                        <li v-if="permissionsIncludes(['view-refrigeration_sale'])">
                                            <Link :class="{ active: isUrlActive($page,['/local/refrigeration-sales']) }" href="/local/refrigeration-sales">Quotations</Link>
                                        </li>
                                        <li v-if="permissionsIncludes(['view-sales_order_eco'])">
                                            <Link :class="{ active: isUrlActive($page,['/local/sales-order-eco']) }" href="/local/sales-order-eco">Sales Orders</Link>
                                        </li>
                                    </ul>
                                </li>
                                <!-- Parts -->
                                <li class="has-dropdown">
                                    <a class="menu-link" :class="{ active: isUrlActive($page,['/local/parts', '/local/sales-order-parts']) }" href="javascript:void(0)">
                                        <span>Part</span>
                                        <em class="icon-dropdown fa-regular fa-angle-down"></em>
                                    </a>
                                    <ul class="nav-sub" :style="{ display: isUrlActive($page,['/local/parts', '/local/sales-order-parts']) ? 'block' : 'none' }">
                                        <li v-if="permissionsIncludes(['view-quotation'])">
                                            <Link :class="{ active: isUrlActive($page,['/local/parts']) }" href="/local/parts">Quotations</Link>
                                        </li>
                                        <li v-if="permissionsIncludes(['view-sales_order'])">
                                            <Link :class="{ active: isUrlActive($page,['/local/sales-order-parts']) }" href="/local/sales-order-parts">Sales Orders</Link>
                                        </li>
                                    </ul>
                                </li>
                                <!-- Hygiene -->
                                <li class="has-dropdown">
                                    <a class="menu-link" :class="{ active: isUrlActive($page,['/local/hygienes', '/local/sales-order-hygienes']) }" href="javascript:void(0)">
                                        <span>Hygiene</span>
                                        <em class="icon-dropdown fa-regular fa-angle-down"></em>
                                    </a>
                                    <ul class="nav-sub" :style="{ display: isUrlActive($page,['/local/hygienes', '/local/sales-order-hygienes']) ? 'block' : 'none' }">
                                        <!-- Vikan Products -->
                                        <li class="has-dropdown">
                                            <a class="menu-link" :class="{ active: isUrlActive($page,['/local/hygienes-vikan', '/local/sales-order-hygienes-vikan']) }" href="javascript:void(0)">
                                                <span>Vikan Products</span>
                                                <em class="icon-dropdown fa-regular fa-angle-down"></em>
                                            </a>
                                            <ul class="nav-sub" :style="{ display: isUrlActive($page,['/local/hygienes-vikan', '/local/sales-order-hygienes-vikan']) ? 'block' : 'none' }">
                                                <li v-if="permissionsIncludes(['view-hygiene'])">
                                                    <Link :class="{ active: $page.url.startsWith('/local/hygienes-vikan') }" href="/local/hygienes-vikan">Quotations</Link>
                                                </li>
                                                <li v-if="permissionsIncludes(['view-sales_order'])">
                                                    <Link :class="{ active: $page.url.startsWith('/local/sales-order-hygienes-vikan') }" href="/local/sales-order-hygienes-vikan">Sales Orders</Link>
                                                </li>
                                            </ul>
                                        </li>
                                        <!-- Sika Products -->
                                        <li class="has-dropdown">
                                            <a class="menu-link" :class="{ active: isUrlActive($page,['/local/hygienes-sika', '/local/sales-order-hygienes-sika']) }" href="javascript:void(0)">
                                                <span>Sika Products</span>
                                                <em class="icon-dropdown fa-regular fa-angle-down"></em>
                                            </a>
                                            <ul class="nav-sub" :style="{ display: isUrlActive($page,['/local/hygienes-sika', '/local/sales-order-hygienes-sika']) ? 'block' : 'none' }">
                                                <li v-if="permissionsIncludes(['view-hygiene'])">
                                                    <Link :class="{ active: $page.url.startsWith('/local/hygienes-sika') }" href="/local/hygienes-sika">Quotations</Link>
                                                </li>
                                                <li v-if="permissionsIncludes(['view-sales_order'])">
                                                    <Link :class="{ active: $page.url.startsWith('/local/sales-order-hygienes-sika') }" href="/local/sales-order-hygienes-sika">Sales Orders</Link>
                                                </li>
                                            </ul>
                                        </li>
                                        <!-- Roser Products -->
                                        <li class="has-dropdown">
                                            <a class="menu-link" :class="{ active: isUrlActive($page,['/local/hygienes-roser', '/local/sales-order-hygienes-roser']) }" href="javascript:void(0)">
                                                <span>Roser Products</span>
                                                <em class="icon-dropdown fa-regular fa-angle-down"></em>
                                            </a>
                                            <ul class="nav-sub" :style="{ display: isUrlActive($page,['/local/hygienes-roser', '/local/sales-order-hygienes-roser']) ? 'block' : 'none' }">
                                                <li v-if="permissionsIncludes(['view-hygiene'])">
                                                    <Link :class="{ active: $page.url.startsWith('/local/hygienes-roser') }" href="/local/hygienes-roser">Quotations</Link>
                                                </li>
                                                <li v-if="permissionsIncludes(['view-sales_order'])">
                                                    <Link :class="{ active: $page.url.startsWith('/local/sales-order-hygienes-roser') }" href="/local/sales-order-hygienes-roser">Sales Orders</Link>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <!-- Overseas Sales -->
                        <li class="menu-item has-dropdown" :class="{ active: isUrlActive($page,['/overseas/']) }">
                            <a class="menu-link" href="javascript:void(0)">
                                <span class="text">Overseas</span><em class="icon-dropdown fa-regular fa-angle-down"></em>
                            </a>
                            <ul class="nav-sub" :style="{ display: isUrlActive($page,['/overseas/']) ? 'block' : 'none' }">
                                <!-- Refrigeration Sales -->
                                <li class="has-dropdown">
                                    <a class="menu-link" :class="{ active: isUrlActive($page,['/overseas/refrigeration-sales', '/overseas/sales-order-eco']) }" href="javascript:void(0)">
                                        <span>Refrigeration Sales</span>
                                        <em class="icon-dropdown fa-regular fa-angle-down"></em>
                                    </a>
                                    <ul class="nav-sub" :style="{ display: isUrlActive($page,['/overseas/refrigeration-sales', '/overseas/sales-order-eco']) ? 'block' : 'none' }">
                                        <li v-if="permissionsIncludes(['view-refrigeration_sale'])">
                                            <Link :class="{ active: isUrlActive($page,['/overseas/refrigeration-sales']) }" href="/overseas/refrigeration-sales">Quotations</Link>
                                        </li>
                                        <li v-if="permissionsIncludes(['view-sales_order_eco'])">
                                            <Link :class="{ active: isUrlActive($page,['/overseas/sales-order-eco']) }" href="/overseas/sales-order-eco">Sales Orders</Link>
                                        </li>
                                    </ul>
                                </li>
                                <!-- Export Parts -->
                                <li class="has-dropdown">
                                    <a class="menu-link" :class="{ active: isUrlActive($page,['/overseas/export-parts', '/overseas/sales-order-export-parts']) }" href="javascript:void(0)">
                                        <span>Export Part</span>
                                        <em class="icon-dropdown fa-regular fa-angle-down"></em>
                                    </a>
                                    <ul class="nav-sub" :style="{ display: isUrlActive($page,['/overseas/export-parts', '/overseas/sales-order-export-parts']) ? 'block' : 'none' }">
                                        <li v-if="permissionsIncludes(['view-quotation'])">
                                            <Link :class="{ active: isUrlActive($page,['/overseas/parts']) }" href="/overseas/export-parts">Quotations</Link>
                                        </li>
                                        <li v-if="permissionsIncludes(['view-sales_order'])">
                                            <Link :class="{ active: isUrlActive($page,['/overseas/sales-order-parts']) }" href="/overseas/sales-order-export-parts">Sales Orders</Link>
                                        </li>
                                    </ul>
                                </li>
                                <!-- Parts -->
                                <li class="has-dropdown">
                                    <a class="menu-link" :class="{ active: isUrlActive($page,['/overseas/parts', '/overseas/sales-order-parts']) }" href="javascript:void(0)">
                                        <span>Part</span>
                                        <em class="icon-dropdown fa-regular fa-angle-down"></em>
                                    </a>
                                    <ul class="nav-sub" :style="{ display: isUrlActive($page,['/overseas/parts', '/overseas/sales-order-parts']) ? 'block' : 'none' }">
                                        <li v-if="permissionsIncludes(['view-quotation'])">
                                            <Link :class="{ active: isUrlActive($page,['/overseas/parts']) }" href="/overseas/parts">Quotations</Link>
                                        </li>
                                        <li v-if="permissionsIncludes(['view-sales_order'])">
                                            <Link :class="{ active: isUrlActive($page,['/overseas/sales-order-parts']) }" href="/overseas/sales-order-parts">Sales Orders</Link>
                                        </li>
                                    </ul>
                                </li>
                                <!-- Hygiene -->
                                <li class="has-dropdown">
                                    <a class="menu-link" :class="{ active: isUrlActive($page,['/overseas/hygienes', '/overseas/sales-order-hygienes']) }" href="javascript:void(0)">
                                        <span>Hygiene</span>
                                        <em class="icon-dropdown fa-regular fa-angle-down"></em>
                                    </a>
                                    <ul class="nav-sub" :style="{ display: isUrlActive($page,['/overseas/hygienes', '/overseas/sales-order-hygienes']) ? 'block' : 'none' }">
                                        <!-- Vikan Products -->
                                        <li class="has-dropdown">
                                            <a class="menu-link" :class="{ active: isUrlActive($page,['/overseas/hygienes-vikan', '/overseas/sales-order-hygienes-vikan']) }" href="javascript:void(0)">
                                                <span>Vikan Products</span>
                                                <em class="icon-dropdown fa-regular fa-angle-down"></em>
                                            </a>
                                            <ul class="nav-sub" :style="{ display: isUrlActive($page,['/overseas/hygienes-vikan', '/overseas/sales-order-hygienes-vikan']) ? 'block' : 'none' }">
                                                <li v-if="permissionsIncludes(['view-hygiene'])">
                                                    <Link :class="{ active: $page.url.startsWith('/overseas/hygienes-vikan') }" href="/overseas/hygienes-vikan">Quotations</Link>
                                                </li>
                                                <li v-if="permissionsIncludes(['view-sales_order'])">
                                                    <Link :class="{ active: $page.url.startsWith('/overseas/sales-order-hygienes-vikan') }" href="/overseas/sales-order-hygienes-vikan">Sales Orders</Link>
                                                </li>
                                            </ul>
                                        </li>
                                        <!-- Sika Products -->
                                        <li class="has-dropdown">
                                            <a class="menu-link" :class="{ active: isUrlActive($page,['/overseas/hygienes-sika', '/overseas/sales-order-hygienes-sika']) }" href="javascript:void(0)">
                                                <span>Sika Products</span>
                                                <em class="icon-dropdown fa-regular fa-angle-down"></em>
                                            </a>
                                            <ul class="nav-sub" :style="{ display: isUrlActive($page,['/overseas/hygienes-sika', '/overseas/sales-order-hygienes-sika']) ? 'block' : 'none' }">
                                                <li v-if="permissionsIncludes(['view-hygiene'])">
                                                    <Link :class="{ active: $page.url.startsWith('/overseas/hygienes-sika') }" href="/overseas/hygienes-sika">Quotations</Link>
                                                </li>
                                                <li v-if="permissionsIncludes(['view-sales_order'])">
                                                    <Link :class="{ active: $page.url.startsWith('/overseas/sales-order-hygienes-sika') }" href="/overseas/sales-order-hygienes-sika">Sales Orders</Link>
                                                </li>
                                            </ul>
                                        </li>
                                        <!-- Roser Products -->
                                        <li class="has-dropdown">
                                            <a class="menu-link" :class="{ active: isUrlActive($page,['/overseas/hygienes-roser', '/overseas/sales-order-hygienes-roser']) }" href="javascript:void(0)">
                                                <span>Roser Products</span>
                                                <em class="icon-dropdown fa-regular fa-angle-down"></em>
                                            </a>
                                            <ul class="nav-sub" :style="{ display: isUrlActive($page,['/overseas/hygienes-roser', '/overseas/sales-order-hygienes-roser']) ? 'block' : 'none' }">
                                                <li v-if="permissionsIncludes(['view-hygiene'])">
                                                    <Link :class="{ active: $page.url.startsWith('/overseas/hygienes-roser') }" href="/overseas/hygienes-roser">Quotations</Link>
                                                </li>
                                                <li v-if="permissionsIncludes(['view-sales_order'])">
                                                    <Link :class="{ active: $page.url.startsWith('/overseas/sales-order-hygienes-roser') }" href="/overseas/sales-order-hygienes-roser">Sales Orders</Link>
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>


                        <li>
                            <a class="menu-link" :class="{ active: isUrlActive($page,['/vehicle-spec', '/milestones-settings']) }" href="javascript:void(0)">
                                <span class="text">Setting</span><em class="icon-dropdown fa-regular fa-angle-down"></em>
                            </a>
                            <ul class="nav-sub" :style="{ display: isComponentActive($page,['VehicleSpec','MilestonesSetting']) ? 'block' : 'none' }">
                                <li>
                                    <Link :class="{ active: $page.component.startsWith('VehicleSpec') }" href="/vehicle-spec">Refrigeration Specs</Link>
                                </li>
                                <li>
                                    <Link :class="{ active: $page.component.startsWith('MilestonesSetting') }" href="/milestones-settings">Milestones</Link>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <!--End Sales Order-->

                <!--Start Service Order-->
                <li v-if="permissionsIncludes(['view-service_appointment', 'view-service_order', 'view-service_quotation', 'view-service_invoice', 'view-service_contract', 'view-service_schedule', 'view-service_technician', 'view-service_order_process'])" class="menu-item has-dropdown"
                    :class="{ active: isComponentActive($page,['ServiceAppointment', 'ServiceOrder', 'ServiceQuotation', 'ServiceInvoice', 'ServiceContract', 'ServiceSchedule', 'ServiceTechnician', 'ServiceWorkingHour'])}">
                    <a class="menu-link" href="javascript:void(0)" :class="{ active: isComponentActive($page,['ServiceAppointment', 'ServiceOrder', 'ServiceQuotation', 'ServiceInvoice', 'ServiceContract', 'ServiceSchedule', 'ServiceTechnician', 'ServiceWorkingHour'])}">
                        <span class="icon"><img src="../../assets/img/inventory.svg" alt="Service Order"></span>
                        <span class="text">Service Order</span><em class="icon-dropdown fa-regular fa-angle-down"></em>
                    </a>
                    <ul class="nav-sub" :style="{ display: isComponentActive($page,['ServiceAppointment', 'ServiceOrder', 'ServiceQuotation', 'ServiceInvoice', 'ServiceContract', 'ServiceSchedule', 'ServiceTechnician', 'ServiceWorkingHour', 'ServiceProcessDetail']) ? 'block' : 'none' }">
                        <li v-if="permissionsIncludes(['view-service_appointment'])">
                            <Link :class="{ active: $page.component.startsWith('ServiceAppointment') }" href="/service-appointments">Service Appointment</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-service_order'])">
                            <Link :class="{ active: $page.component.startsWith('ServiceOrder') && !$page.component.startsWith('ServiceOrder/Schedule') }" href="/service-orders">Service Order</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-service_quotation'])">
                            <Link :class="{ active: $page.component.startsWith('ServiceQuotation') }" href="/service-quotations">Quotations</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-service_invoice'])">
                            <Link :class="{ active: $page.component.startsWith('ServiceInvoice') }" href="/service-invoices">Invoices</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-service_contract'])">
                            <Link :class="{ active: $page.component.startsWith('ServiceContract') }" href="/service-contracts">CMP Contract</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-service_schedule'])">
                            <Link :class="{ active: $page.component.startsWith('ServiceSchedule') }" href="/service-schedules">Schedule</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-service_technician'])">
                            <Link :class="{ active: $page.component.startsWith('ServiceTechnician') }" href="/service-technicians">Technician Dashboard</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-service_order'])">
                            <Link :class="{ active: $page.component.startsWith('ServiceOrder/Schedule') }" href="/service-orders-schedule">Service Orders Schedule</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-service_order'])">
                            <Link :class="{ active: $page.component.startsWith('ServiceWorkingHour') }" href="/service-working-hours">Service Working Hours</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-service_order_process'])">
                            <Link :class="{ active: $page.component.startsWith('ServiceProcessDetail') }" href="/service-process-detail">Process Detail</Link>
                        </li>
                    </ul>
                </li>
                <!--End Service Order-->

                <!--Start Project Order-->
                <li v-if="permissionsIncludes(['view-project_appointment', 'view-project_order', 'view-project_quotation', 'view-project_invoice', 'view-project_contract', 'view-project_schedule', 'view-project_technician', 'view-project_order_process'])" class="menu-item has-dropdown"
                    :class="{ active: isComponentActive($page,['ProjectAppointment', 'ProjectOrder', 'ProjectQuotation', 'ProjectInvoice', 'ProjectContract', 'ProjectSchedule', 'ProjectTechnician', 'ProjectWorkingHour'])}">
                    <a class="menu-link" href="javascript:void(0)" :class="{ active: isComponentActive($page,['ProjectAppointment', 'ProjectOrder', 'ProjectQuotation', 'ProjectInvoice', 'ProjectContract', 'ProjectSchedule', 'ProjectTechnician', 'ProjectWorkingHour'])}">
                        <span class="icon"><img src="../../assets/img/inventory.svg" alt="Project Order"></span>
                        <span class="text">Project Order</span><em class="icon-dropdown fa-regular fa-angle-down"></em>
                    </a>
                    <ul class="nav-sub" :style="{ display: isComponentActive($page,['ProjectAppointment', 'ProjectOrder', 'ProjectQuotation', 'ProjectInvoice', 'ProjectContract', 'ProjectSchedule', 'ProjectTechnician', 'ProjectWorkingHour', 'ProjectProcessDetail']) ? 'block' : 'none' }">
                        <li v-if="permissionsIncludes(['view-project_appointment'])">
                            <Link :class="{ active: $page.component.startsWith('ProjectAppointment') }" href="/project-appointments">Project Appointment</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-project_order'])">
                            <Link :class="{ active: $page.component.startsWith('ProjectOrder') && !$page.component.startsWith('ProjectOrder/Schedule') }" href="/project-orders">Project Order</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-project_quotation'])">
                            <Link :class="{ active: $page.component.startsWith('ProjectQuotation') }" href="/project-quotations">Quotations</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-project_invoice'])">
                            <Link :class="{ active: $page.component.startsWith('ProjectInvoice') }" href="/project-invoices">Invoices</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-project_contract'])">
                            <Link :class="{ active: $page.component.startsWith('ProjectContract') }" href="/project-contracts">CMP Contract</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-project_schedule'])">
                            <Link :class="{ active: $page.component.startsWith('ProjectSchedule') }" href="/project-schedules">Schedule</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-project_technician'])">
                            <Link :class="{ active: $page.component.startsWith('ProjectTechnician') }" href="/project-technicians">Technician Dashboard</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-project_order'])">
                            <Link :class="{ active: $page.component.startsWith('ProjectOrder/Schedule') }" href="/project-orders-schedule">Project Orders Schedule</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-project_order'])">
                            <Link :class="{ active: $page.component.startsWith('ProjectWorkingHour') }" href="/project-working-hours">Project Working Hours</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-project_order_process'])">
                            <Link :class="{ active: $page.component.startsWith('ProjectProcessDetail') }" href="/project-process-detail">Process Detail</Link>
                        </li>
                    </ul>
                </li>
                <!--End Project Order-->

                <!--Start Production-->
                <li v-if="permissionsIncludes(['view-production_order','view-production_process_detail'])" class="menu-item has-dropdown" :class="{ active: isComponentActive($page,['ProductionOrder','ProductionProcessDetail','ProductionSchedule','ProductionWorkingHour'])}">
                    <a class="menu-link" href="javascript:void(0)" :class="{ active: $page.component.startsWith('ProductionOrder') || $page.component.startsWith('ProductionProcessDetail') || $page.component.startsWith('ProductionSchedule') || $page.component.startsWith('ProductionWorkingHour')}">
                        <span class="icon"><img src="../../assets/img/location.svg" alt="Production"></span>
                        <span class="text">Production</span><em class="icon-dropdown fa-regular fa-angle-down"></em>
                    </a>
                    <ul class="nav-sub" :style="{ display: isComponentActive($page,['ProductionOrder','ProductionProcessDetail','ProductionSchedule','ProductionWorkingHour']) ? 'block' : 'none' }">
                        <li v-if="permissionsIncludes(['view-production_order'])">
                            <Link :class="{ active: $page.component.startsWith('ProductionOrder') && !$page.component.startsWith('ProductionOrder/Schedule') }" href="/production-order">Production Order</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-production_process_detail'])">
                            <Link :class="{ active: $page.component.startsWith('ProductionProcessDetail') }" href="/production-process-detail">Process</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-production_order'])">
                            <Link :class="{ active: $page.component.startsWith('ProductionOrder/Schedule') }" href="/production-orders-schedule">Schedule</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-production_order'])">
                            <Link :class="{ active: $page.component.startsWith('ProductionWorkingHour') }" href="/production-working-hours">Working Hours</Link>
                        </li>
                    </ul>
                </li>
                <!--End Production-->

                <!--Start Engineering-->
                <li v-if="permissionsIncludes(['view-engineering_order','view-engineering_process_detail'])" class="menu-item has-dropdown" :class="{ active: isComponentActive($page,['EngineeringOrder','EngineeringProcessDetail','Fabrication','EngineeringWorkingHour'])}">
                    <a class="menu-link" href="javascript:void(0)" :class="{ active: $page.component.startsWith('EngineeringOrder') || $page.component.startsWith('EngineeringProcessDetail') || $page.component.startsWith('Fabrication') || $page.component.startsWith('EngineeringWorkingHour')}">
                        <span class="icon"><img src="../../assets/img/location.svg" alt="Engineering"></span>
                        <span class="text">Engineering</span><em class="icon-dropdown fa-regular fa-angle-down"></em>
                    </a>
                    <ul class="nav-sub" :style="{ display: isComponentActive($page,['EngineeringOrder','EngineeringProcessDetail', 'Fabrication', 'EngineeringWorkingHour']) ? 'block' : 'none' }">
                        <li v-if="permissionsIncludes(['view-engineering_order'])">
                            <Link :class="{ active: $page.component.startsWith('EngineeringOrder') && !$page.component.startsWith('EngineeringOrder/Schedule') }" href="/engineering-order">Engineering Order</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-engineering_order'])">
                            <Link :class="{ active: $page.component.startsWith('Fabrication') }" href="/fabrication">Fabrication / R&D</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-engineering_process_detail'])">
                            <Link :class="{ active: $page.component.startsWith('EngineeringProcessDetail') }" href="/engineering-process-detail">Process</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-engineering_order'])">
                            <Link :class="{ active: $page.component.startsWith('EngineeringOrder/Schedule') }" href="/engineering-orders-schedule">Schedule</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-engineering_order'])">
                            <Link :class="{ active: $page.component.startsWith('EngineeringWorkingHour') }" href="/engineering-working-hours">Working Hours</Link>
                        </li>
                    </ul>
                </li>
                <!--End Engineering-->

                <!--Start Vehicle-->
                <li v-if="permissionsIncludes(['view-vehicle'])" class="menu-item">
                    <Link :class="{ active: $page.url.startsWith('/vehicle') && !$page.url.startsWith('/vehicle-spec') }" class="menu-link" href="/vehicles">
                        <span class="icon fa-regular fa-car"></span>
                        <span class="text">Vehicle</span>
                    </Link>
                </li>
                <!--End Vehicle-->

                <!--Start User-->
                <li v-if="permissionsIncludes(['view-user', 'view-any'])" class="menu-item has-dropdown">
                    <a class="menu-link" :class="{ active: $page.component.startsWith('User') || $page.component.startsWith('Role')}" href="javascript:void(0)">
                        <span class="icon"><img src="../../assets/img/user.svg" alt="user"></span>
                        <span class="text">User Management</span><em class="icon-dropdown fa-regular fa-angle-down"></em>
                    </a>
                    <ul class="nav-sub" :style="{ display: $page.component.startsWith('User') || $page.component.startsWith('Role') ? 'block' : 'none' }">
                        <li v-if="permissionsIncludes(['view-user'])">
                            <Link :class="{ active: $page.component.startsWith('User') }" href="/users">User list</Link>
                        </li>
                        <li v-if="permissionsIncludes(['view-any'])">
                            <Link :class="{ active: $page.component.startsWith('Role') }" href="/roles">Roles</Link>
                        </li>
                    </ul>
                </li>
                <!--End User-->
            </ul>
        </nav>
    </aside>
</template>

<script setup>
import {ref, computed} from "vue";
import {usePage, Link} from "@inertiajs/inertia-vue3";

let open = ref(null);

const permissions = computed(() => usePage().props.value.auth.user.permissions);

const isComponentActive = ($page, components) => {
    return components.some(component => $page.component.startsWith(component));
};

const isUrlActive = ($page, urls) => {
    return urls.some(url => $page.url.startsWith(url));
};

const permissionsIncludes = (pers) => {
    return permissions.value.some(permission => pers.includes(permission));
};
</script>
