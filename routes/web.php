<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AdminController,
    AjaxController,
    HomeController,
    ProductController,
    StockController,
    EstimationController,
    SaleController,
    PurchaseController,
    SupplierController,
    ClientController,
    ReportController,
    PettyCashController,
    HRController,
    AccountsController,
    SendMailController,
    RoleController,
    EmployeeController,
    SalaryGroupController,
    PayrollController, 
    DepartmentController,
    DesignationController,
    ContractTypeController,
    ContributionController,
    EmployeeBankController,
    BankController,
    LeaveController,
    LeaveTypeController,
    EmployeeNssfController,
    LoanController,
    SalaryAdvanceController
};

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

Route::get('/', fn() => view('pages/login'));

Auth::routes();

Route::middleware('auth')->group(function() {
    // Role & Permission Routes
    Route::controller(RoleController::class)->group(function() {
        // Route::get('role/permission/{id}', 'permission')->name('role.permission');
        // Route::post('role/set_permission', 'setPermission')->name('role.setPermission');

    Route::get('roles-permissions/index', [RoleController::class, 'index']);
    Route::get('role/permission/{id}', [RoleController::class, 'changePermissions'])->name('role.permission');
    Route::post('role/set_permission', [RoleController::class, 'setPermission'])->name('role.setPermission');
    Route::get('roles/{roleId}/permissions', [RoleController::class, 'changePermissions'])->name('roles.permissions');
    Route::post('roles/{roleId}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.updatePermissions');
        Route::resource('roles', RoleController::class);
    });

    // Home & User Management Routes
    Route::controller(HomeController::class)->group(function() {
        Route::get('/home', 'index')->name('home');
        Route::get('/test', 'testPDF')->name('test');
        Route::get('add', 'myTestAddToLog');
        Route::get('logs', 'logActivity');
        Route::get('settings/activity_logs', 'logActivity')->name('activity-log');
        
        // User routes
        Route::get('users/edit/{id}', 'editUser')->name('edit-user');
        Route::get('users/details/{id}', 'getUser')->name('get-user');
        Route::get('/users/delete/{id}', 'deleteUser')->name('delete');
        Route::post('users/update', 'updateUser')->name('update-users');
        Route::post('users/update/roles', 'updateUserRoles')->name('user-roles');
        
        Route::get('/users', 'userList')->name('user');
        Route::post('/profile/update', 'updateUserProfile')->name('user-');
        Route::post('/create-user', 'createUser')->name('user');
        Route::get('/user-profile', 'userProfile')->name('user');
        Route::post('/change_password', 'passwordReset')->name('user');
        
        // Settings
        Route::get('settings/product', 'settings')->name('settings');
        Route::get('settings/items', 'getAllItems')->name('settings.items');
        Route::get('settings/item-assigned-make', 'getAssignedMake')->name('settings.item-assigned-make');
        Route::get('settings/makes', 'getAllMakes')->name('settings.make');
        Route::get('settings/items/delete/{id}', 'deleteItem')->name('delete-items');
        Route::get('settings/makes/delete/{id}', 'deleteMake')->name('delete-make');
        Route::get('settings/assign-items/delete/{id}', 'deleteAssignItems')->name('delete-assign-item');
        
        Route::get('settings/products', 'settings')->name('settings');
        Route::get('/settings/system', 'generalSettings')->name('general-settings');
        Route::get('/settings/system/{id}', 'editGeneralSettings')->name('edit-general-settings');
        Route::post('setting/general/update', 'updateGeneralSettings')->name('update-general-settings');
        
        Route::post('settings/add-labour', 'saveLabour')->name('labour-settings');
        Route::post('settings/update-labour', 'updateLabour')->name('update-labour-settings');
        Route::get('labours/fetch-details/{id}', 'editLabour')->name('edit-labour-settings');
        Route::get('labours/details/delete/{id}', 'deleteLabour')->name('delete-labour-settings');
        
        Route::get('reports/sales', 'salesReportGraph')->name('sales');
        Route::get('reports/sales-purchases-report', 'salesAndPurchasesReport')->name('sales');
    });

    // Product & Inventory Routes
    Route::controller(ProductController::class)->group(function() {
        Route::get('/parts', 'index')->name('products');
        Route::get('/labours', 'getLabours')->name('products');
        Route::get('/products/{id}', 'productDetails')->name('products');
        Route::get('/products/details/{id}', 'getProductDetails')->name('products');
        Route::get('products/adjustments/details/{id}', 'getAdjustmentData')->name('products');
        Route::post('/products', 'createProduct')->name('save-products');
        Route::get('/stores', 'storeList')->name('stores');
        Route::post('/stores', 'createStore')->name('create-stores');
        Route::get('/item-adjustments', 'adjustmentList')->name('adjustments');
        Route::post('/adjustments', 'createItemAdjustment')->name('adjustments');
        Route::get('settings/adjustments/delete/{id}', 'deleteAdjustmentReasons');
        
        Route::post('product-brands', 'createModel')->name('make');
        Route::get('product-brands/edit/{id}', 'editMake')->name('make');
        Route::post('product-brands/update', 'updateMake')->name('make');
        Route::post('product-items', 'assignItemMake')->name('item');
        Route::get('products/assigned-items/edit/{id}', 'editAssignedMake')->name('make');
        Route::post('products/assigned-items/update', 'updateAssignedMake')->name('make');
        Route::get('products/items/edit/{id}', 'editItem')->name('item');
        Route::post('products/items/update', 'updateItem')->name('item');
        Route::get('products/items/delete/{id}', 'deleteItem')->name('item');
        Route::post('products/stocks/update', 'updateStockProduct')->name('stock');
        Route::post('products/price-list/update', 'updatePriceList')->name('price');
        Route::get('product-items/delete/{id}', 'deleteProductItem')->name('item');
        Route::get('product-brands/delete/{id}', 'deleteModel')->name('model');
        Route::get('products/delete/{id}', 'deleteProduct')->name('model');
        
        Route::post('/products/new-product', 'createNewItem')->name('item');
        Route::get('/products/view-details/{id}', 'getSingleProductDetails')->name('stock');
        Route::get('/products/edit/{id}', 'editStockProduct')->name('stock');
        Route::get('price-lists/clients/{client_id}/edit/{product_id}', 'editPriceList')->name('stock');
        Route::get('/products/price-lists/delete/{id}', 'deletePriceList')->name('stock');
        Route::post('/settings/add-notification', 'createNotification')->name('notifications');
        
        Route::get('/product/fetch-assigned-items', 'fetchProductModels')->name('model');
        Route::get('/products/fetch-product-adjustments/{id}', 'fetchProductAdjustment')->name('adjustment');
        Route::post('/product/update', 'updateQuantity')->name('product');
        
        Route::get('products/fetch-client-prices/{id}', 'fetchClientPrice');
        Route::get('products/find-client-prices/{id}', 'displayClientPrice');
        Route::post('/products/client-prices/pdf', 'generateClientPricePDF');
        
        Route::get('/supplier-reports', 'getAllProduct')->name('allproduct');
        
        Route::get('/store', 'getStore');
        Route::post('/items/display-selected', 'displaySelectedStore');
    });

    // Stock Routes
    Route::controller(StockController::class)->group(function() {
        Route::get('/current-stocks', 'currentStock')->name('stock');
        Route::get('/current-stocks/pdf', 'getCurrentStockPDF')->name('stock');
        Route::get('/current-stocks/details/{id}', 'getProductDetails')->name('stock');
        Route::get('/price-lists', 'priceList')->name('stock');
        Route::get('price-lists/clients/{id}', 'getClientPriceList')->name('stock');
        Route::post('/products/add-client-price-sheet', 'saveClientPrice')->name('add');
        Route::get('/minimum-stock', 'getMinimumStock')->name('stock');
        Route::get('stock-out', 'getOutStock')->name('stock');
        
        Route::get('sales/report-by-item', 'getStockReportByItem');
        Route::get('sales/item-reports', 'getStockByItem');
        Route::get('return-jobs', 'getReturnJob');
        Route::post('stocks/return-jobs/add', 'saveReturnJob');
        Route::get('sales/sales-report-by-items', 'getStockItemReportPDF');
        Route::get('sales/all-stock-reports', 'getAllStockReport');
        Route::get('stocks/all-stock-reports', 'getAllStockReportPDF');
    });

    // Estimation & Job Card Routes
    Route::controller(EstimationController::class)->group(function() {
        Route::get('create-estimates', 'createEstimations')->name('estimates');
        Route::post('estimate/create', 'saveEstimations')->name('post-estimates');
        Route::post('estimations/add-more-parts', 'addMoreEstimations')->name('post');
        Route::get('estimations-summary/delete/{id}', 'deleteEstimations')->name('delete-estimations');
        Route::get('job-cards/delete/{id}', 'deleteJobCard')->name('delete-estimations');
        Route::post('estimations/add-new-labour-details', 'saveNewLabourEstimations')->name('post-estimates');
        
        Route::get('estimations/edit-profoma-details/{id}', 'editProfomaDetails');
        Route::post('estimations/update-profoma-details', 'updateProfomaDetails');
        
        Route::get('/estimations-history', 'estimationSummary')->name('estimation-summary');
        Route::get('/job-cards', 'getJobCards')->name('estimation-summary');
        
        Route::get('estimations/details/{id}', 'getEstimationsDetails');
        Route::get('estimations/details/pdf/{id}/', 'getEstimationDetailsPDF');
        
        Route::get('job-cards/create-job-cards/{reference}', 'createJobCard');
        Route::get('estimations/delivery-note/pdf/{id}/', 'getDeliveryNotePDF')->name('delivery-note');
        Route::post('job-cards/create-job-cards/post', 'saveJobCard');
        
        Route::get('invoices/all-invoices', 'getAllInvoices');
        Route::get('invoices/edit/{id}', 'editInvoice');
        Route::post('invoices/update', 'updateInvoice');
        Route::get('invoices/create-invoice/{id}', 'getInvoice');
        Route::get('invoices/delete-invoice/delete/{id}', 'deleteInvoice');
        Route::get('invoices/tax-invoices/pdf/{id}', 'getInvoicePDF');
        Route::get('invoices/send-email', 'sendMail');
        Route::post('invoices/create-invoice/post', 'saveInvoice');
        Route::post('invoices/new-invoice', 'saveNewInvoice')->name('save-new-invoice');
        
        Route::get('job-cards/delivery-note/edit/{ref}', 'editDeliveryNote')->name('edit-delivery-note');
        Route::post('job-cards/delivery-note/update', 'updateDeliveryNote')->name('update-delivery-note');
        Route::get('job-cards/details/pdf/{id}', 'getJobCardPDF');
        Route::get('job-cards/job-card-status/{id}', 'getJobCardStatus');
        Route::post('job-cards/update-status', 'updateJobCardStatus');
        
        Route::post('estimations/update-part-details', 'updateEstimationDetails');
        Route::get('estimations/delete-estimations/{id}', 'deleteEstimationDetails');
    });

    // Sale & POS Routes
    Route::controller(SaleController::class)->group(function() {
        Route::get('/pos/edit/{id}', 'editPOS')->name('pos');
        Route::post('sales/update', 'updatePOS')->name('update');
        Route::get('/sales-history', 'saleSummary')->name('sales-summary');
        Route::get('sales-reports', 'getSalesReport')->name('stock');
        Route::get('sales-reports/{id}', 'getSalesReport')->name('stock');
        
        Route::get('clients/payments-details/{id}', 'getSingleClientPaymentDetails')->name('payments');
        Route::get('clients/fetch-clients/payments/{id}', 'fetchClientPayment')->name('purchase-payments');
        Route::get('clients/payments-delete/{id}', 'deleteClientPayment')->name('delete-payments');
        Route::post('clients/payments-update', 'updateClientPayment')->name('update-payment');
        
        Route::get('reports/generate-report', 'displaySalesReport')->name('pdf');
        Route::get('sales-report/pdf', 'salesReportPdf')->name('sales-report');
        Route::get('sales-summary/delete/{id}', 'deleteSale');
        Route::get('sales-summary/sales-items/delete/{id}', 'deleteSaleItems');
        Route::get('sales-summary/create-reference/{vehicle}/{date}', 'CreateReference');
        Route::post('sales/add-reference/post', 'addReference');
        
        Route::get('reports/annual-sales', 'getAnnualSales')->name('annual-sales');
        Route::get('reports/annual-sales-reports/', 'annualSalesReportPdf')->name('annual-sales-report');
        
        Route::get('estimations/edit-spareparts-details/{id}', 'editSparePartDetails');
        Route::get('estimations/edit-labours/{id}', 'editLabourDetails');
        Route::get('estimations/labours/delete/{id}', 'deleteLabourDetails');
        
        Route::get('sales/edit/{id}', 'editSalesSummary');
        Route::post('sales/update', 'updateSaleValue');
        Route::post('sales-summary/update', 'updateSaleSummary');
        
        Route::get('payments/deptors-report-filter', 'getDeptorsReportFilter');
        Route::get('accounts/deptors-report-filters', 'getDeptortReportByClient');
        Route::get('create', 'createPOS');
        Route::get('client-payment-report', 'getClientDeptors');
        Route::get('reports/client-deptors-report', 'getClientDeptorReport');
        
        Route::get('/products/price-lists/details/{id}', 'getPriceDetails')->name('pos');
        Route::get('sales/details/{id}/{date}', 'getSingleSalesDetails1');
        
        Route::get('clients/payments', 'getClientPayments')->name('payments');
        Route::post('clients/add-clients-debit', 'saveClientDebit')->name('debit');
        Route::get('clients/payments/view-payments/{id}', 'getClientPaymentDetails');
        Route::post('clients/add-payments', 'addClientPayments')->name('add-client-payment');
        
        Route::get('reports/sales-reports/profit', 'profitReportPdf')->name('profit-sales-report');
        Route::get('reports/sales/profit', 'getProfitLoss')->name('profit-sales');
    });

    // Purchase Routes
    Route::controller(PurchaseController::class)->group(function() {
        Route::get('/purchase-order', 'createPurchaseOrder')->name('order');
        Route::post('/purchase-order-post', 'savePurchaseOrder')->name('order');
        Route::get('/purchase-reports', 'purchaseReport')->name('report');
        Route::get('/purchase-history', 'getPurchaseHistory')->name('report');
        Route::get('/purchases/edit/{id}', 'getSinglePurchase')->name('report');
        Route::get('/purchase-report/data/{id}', 'getPurchaseDetailsPDF')->name('report');
        Route::get('/purchase-report/{date}/{id}', 'getPurchaseDetails')->name('report');
        Route::post('/purchases/update', 'updatePurchase')->name('update-purchase');
        Route::post('/purchases/update-purchase-details', 'updatePurchaseDetails')->name('update-purchase');
        Route::get('/purchases/lpo/{invoice}/{date}', 'createLPO')->name('lpo');
        Route::post('/purchases/create-lpo/post', 'addLPO')->name('add-lpo');
        
        // Purchase Payments
        Route::get('purchases/payments/details/{id}', 'getPurchasePayments')->name('purchase-payments');
        Route::get('purchases/payments/delete/{id}', 'deletePurchasePayments')->name('delete-payments');
        Route::post('purchases/payments/update', 'updatePurchasePayments')->name('update-purchase-payment');
        
        Route::get('/create-purchases', 'index')->name('purchases');
        Route::post('/send-purchases', 'createPurchases')->name('purchases');
        Route::get('purchases/details/{id}', 'getPaymentDetails');
        Route::get('purchases/view-details/{id}', 'editPurchaseDetails');
        Route::post('purchases/payment', 'savePayment');
        Route::get('/purchases/items/delete/{id}', 'deletePurchaseItem')->name('purchases');
        Route::get('/purchases/invoices/delete/{id}', 'deletePurchaseInvoice')->name('purchases');
        
        Route::get('purchases/reports/pdf', 'purchaseReportPdf')->name('purchases-report');
        Route::get('purchases/creditors-report-filter', 'getCreditorsReportFilter');
        Route::get('purchases/creditors-report-pdf', 'getCreditorsReportPDF');
        
        Route::get('reports/annual-purchases', 'getAnnualPurchases')->name('annual-purchases');
        Route::get('reports/annual-purchases-report', 'getAnnualPurchasesReportPdf')->name('annual-purchases');
        
        Route::get('suppliers/payments', 'getSupplierPurchasePayments')->name('purchase-payments');
        Route::get('accounts/payments/add-payments/{id}', 'getPaymentForm')->name('purchase-payments');
        Route::get('accounts/payments-details/{id}', 'getPaymentPurchaseDetails')->name('purchase-payments');
        Route::post('suppliers/purchase-payments/add-payment', 'savePurchasePayment')->name('purchase-payments');
        Route::get('purchases/edit-details/{id}', 'editPurchaseDetailedValues')->name('purchase-payments');
        Route::get('purchases/payment-details/{id}', 'getPurchaseDetailedValues')->name('purchase-payments');
        Route::get('suppliers/payments/invoice-details/{id}', 'getPurchaseInvoiceValues')->name('purchase-payments');
        Route::get('suppliers/payments/{id}', 'getSingleSupplierPaymentsDetails');
        
        Route::get('supplier-payment-report', 'getSupplierDeptors');
        Route::get('reports/supplier-deptors-report', 'getSupplierDeptorReport');
    });

    // Supplier Routes
    Route::controller(SupplierController::class)->group(function() {
        Route::get('/suppliers', 'index')->name('supplier');
        Route::post('/suppliers', 'addSupplier')->name('supplier');
        Route::get('/suppliers/edit/{id}', 'editSupplier')->name('supplier');
        Route::post('/suppliers/update', 'updateSupplier')->name('supplier');
        Route::get('/suppliers/delete/{id}', 'deleteSupplier')->name('supplier');
    });

    // Client Routes
    Route::controller(ClientController::class)->group(function() {
        Route::get('/clients', 'index')->name('client');
        Route::post('/clients/add', 'addClient')->name('client');
        Route::get('/clients/edit/{id}', 'editClient')->name('client');
        Route::post('/clients/update/', 'updateClient')->name('client');
        Route::get('/clients/delete/{id}', 'deleteClient')->name('client');
    });

    // Report Routes
    Route::controller(ReportController::class)->group(function() {
        Route::get('reports/labour-charge-reports', 'getLabourChargeReport');
        Route::get('reports/labour-charge-report-details', 'labourChargeReportDetails');
        
        Route::get('accounts/client-statements', 'getClientStatement')->name('client-statement');
        Route::get('accounts/client-statements/pdf', 'getClientStatementPDF');
        
        Route::get('accounts/petty-cash-reports', 'getPettyCashReports')->name('petty-cash-reports');
        Route::get('accounts/petty-cash-reports/pdf', 'getPettyCashReportsPDF')->name('petty-cash-pdf');
        
        Route::get('accounts/suppliers-statements', 'getSupplierStatement')->name('client-statement');
        Route::get('accounts/suppliers-statements/pdf', 'getSupplierStatementPDF');
        
        Route::get('accounts/deptors-report', 'getDeptorStatement')->name('deptor-statement');
        Route::get('accounts/deptors-statements/pdf', 'getDeptorStatementPDF');
        
        Route::get('clients/client-details/{id}', 'getClientInfo')->name('client-info');
        Route::get('clients/vehicle_details/{id}', 'getVehicleInfo')->name('vehicle-client-info');
        
        Route::get('accounts/edit/{id}', 'editAccount')->name('edit');
        Route::post('accounts/update', 'updateAccount')->name('update');
        Route::post('accounts/add', 'addAccount')->name('add');
        Route::post('accounts/add-cash', 'addCashAccount')->name('update');
        Route::get('accounts/cash-in-histories/{id}', 'getAccountHistories')->name('account-history');
    });

    // Petty Cash Routes
    Route::controller(PettyCashController::class)->group(function() {
        Route::get('accounts/petty-cash/settings', 'getPettyCashSettings')->name('petty-cash-settings');
        Route::get('accounts/petty-cash/settings/edit/{id}', 'editPettyCashSettings')->name('edit-petty-cash-settings');
        Route::post('accounts/petty-cash/settings', 'savePettyCashSettings')->name('post-petty-cash-settings');
        Route::get('petty-cash-settings/delete/{id}', 'deletePettyCashSettings')->name('delete-petty-cash-settings');
        
        Route::get('accounts/expenditures/petty-cash', 'index')->name('index');
        Route::post('accounts/add-petty-cash', 'saveExpenditure')->name('post-petty-cash');
        Route::get('accounts/expenditures/petty-cash/{date}', 'getExpenditureDetails')->name('index');
        Route::post('accounts/update-petty-cash', 'updateExpenditure')->name('update-petty-cash');
        Route::get('accounts/expenditures/petty-cash/delete-by-date/{id}', 'deleteExpenseByDate')->name('by-date');
        Route::get('accounts/expenditures/petty-cash/delete-by-id/{date}', 'deleteExpenseByID')->name('by-id');
        Route::get('accounts/expenditures/petty-cash/{id}/edit', 'editExpenditure')->name('edit');
        
        Route::get('accounts/account-statements', 'getAccountStatement')->name('account-statemt');
    });

    // Accounts Routes
    Route::controller(AccountsController::class)->group(function() {
        Route::get('accounts/account-list', 'index')->name('index');
        Route::get('accounts/delete/{id}', 'deleteAccount')->name('delete-account');
        Route::get('accounts/account-histories/delete/{id}', 'deleteAccountHistories')->name('delete-account');
    });

    // Mail Routes
    Route::controller(SendMailController::class)->group(function() {
        Route::get('mails/tax-invoice/{id}/{d}', 'sendTaxInvoice')->name('save-new-invoice');
        Route::get('mails/proforma-invoice/{id}/{d}', 'sendProformaInvoice')->name('save-new-invoice');
        Route::get('send_mail/{name}/{id}', 'index')->name('comming');
    });

    // Ajax Routes
    Route::controller(AjaxController::class)->group(function() {
        Route::get('customers/search/{id}', 'searchCustomer')->name('search');
        Route::get('sales/search', 'filterSalesByDate')->name('sales');
        Route::get('home/filters-by-supplier-name', 'filterSupplierByName')->name('supplier-search-filters');
        Route::get('home/filters-by-vehicle', 'filterSalesByVehicleReg')->name('vehicle-search-filters');
        Route::get('sales/sales-report-by-item', 'searchStockByItem')->name('search-filter');
        Route::get('labours/details/{id}', 'getLabourDetails')->name('products');

    });

    // payroll 
Route::get('/staffs/managements', [EmployeeController::class, 'index'])->name('employees.index');
Route::resource('employees', EmployeeController::class);
Route::get('/staffs/managements/employees/{id}', [EmployeeController::class, 'getEmployeeDetails'])->name('employees.details');
Route::post('/staffs/managements/employees/save', [EmployeeController::class, 'saveEmployee'])->name('employees.save');
Route::get('/employees/filter', [EmployeeController::class, 'filter'])->name('employees.filtered');

Route::resource('attendances', AttendanceController::class);
Route::resource('payrolls', PayrollController::class);
Route::resource('hr-settings', HRController::class);

// Departments
Route::resource('departments', DepartmentController::class)->only(['store', 'update', 'destroy']);

// Designations
Route::resource('designations', DesignationController::class)->only(['store', 'update', 'destroy']);

// Contract Types
Route::resource('contract-types', ContractTypeController::class)->only(['store', 'update', 'destroy']);

// Salary Groups
Route::resource('salary-groups', SalaryGroupController::class)->only(['store', 'update', 'destroy']);

Route::resource('contributions', ContributionController::class)->only(['index', 'store', 'update', 'destroy']);


Route::post('/salary-groups/store', [PayrollController::class, 'saveSalaryGroup'])->name('salary-groups.store');
Route::post('/salary-groups/generate', [PayrollController::class, 'generatePayroll'])->name('payroll.generate');
Route::resource('salary-groups', SalaryGroupController::class)->only(['store', 'update', 'destroy']);
Route::get('/payrolls/salary-slip-fetch/view', [PayrollController::class, 'fetchSalarySlip'])->name('payrolls.slip.fetch');
Route::get('/payrolls/salary-slip-download/view/pdf', [PayrollController::class, 'downloadSalarySlip'])->name('salary-slip.download');
Route::get('/payrolls/details/{reference}', [PayrollController::class, 'details']);
Route::get('/payrolls/download/view/details/{reference}', [PayrollController::class, 'downloadPayrollDetails']);

Route::delete('/payrolls/delete/{id}', [PayrollController::class, 'rollbackPayroll'])->name('payrolls.rollback');

// Bank details
Route::post('employee-bank/store', [EmployeeBankController::class, 'store'])->name('employee.bank.store');
Route::put('employee-bank/{id}', [EmployeeBankController::class, 'update'])->name('employee.bank.update');
Route::delete('employee-bank/{id}', [EmployeeBankController::class, 'destroy'])->name('employee.bank.destroy');

Route::resource('banks', BankController::class)->only(['store', 'update', 'destroy']);

Route::get('/payrolls/{reference}/bank-details', [PayrollController::class, 'bankDetails']);


// Leave

    Route::get('leaves', [LeaveController::class, 'index']);
    Route::get('leaves/apply', [LeaveController::class, 'create']);
    Route::post('leaves', [LeaveController::class, 'store']);
    Route::post('leaves/{id}/approve', [LeaveController::class, 'approve']);
    Route::post('leaves/{id}/reject', [LeaveController::class, 'reject']);

// Leave Type
Route::post('leave-types', [LeaveTypeController::class, 'store']);
Route::put('leave-types/{id}', [LeaveTypeController::class, 'update']);
Route::delete('leave-types/{id}', [LeaveTypeController::class, 'destroy']);



// Voucher
Route::get('payrolls/voucher/nssf/{reference}', [PayrollController::class, 'nssfVoucher']);
Route::get('payrolls/voucher/nhif/{reference}', [PayrollController::class, 'nhifVoucher']);
Route::get('payrolls/voucher/paye/{reference}', [PayrollController::class, 'payeVoucher']);

Route::get('/payrolls/nssf-voucher/{month}', [PayrollController::class, 'generateNssfVoucher']);
Route::get('/payrolls/nhif-voucher/{month}', [PayrollController::class, 'generateNhifVoucher']);
Route::get('/payrolls/paye-voucher/{month}', [PayrollController::class, 'generatePayeVoucher']);

Route::get('/payrolls/wcf-voucher/{month?}', [PayrollController::class, 'generateWcfVoucher']);

// Route::resource('employee/nssf', EmployeeNssfController::class)->except(['create', 'edit', 'show', 'index']);

// Route::resource('employee/nssf', EmployeeNssfController::class)->only(['store', 'update', 'destroy', 'create', 'edit', 'show', 'index']);

Route::post('/employee/nssf', [EmployeeNssfController::class, 'store'])->name('employee.nssf.store');
Route::put('/employee/nssf/{id}', [EmployeeNssfController::class, 'update'])->name('employee.nssf.update');
Route::delete('/employee/nssf/{id}', [EmployeeNssfController::class, 'destroy'])->name('employee.nssf.destroy');

Route::resource('loans', LoanController::class)
     ->only(['store', 'update', 'destroy']);

     Route::resource('salary-advances', SalaryAdvanceController::class);

     Route::get('/loans/{id}/statement', [LoanController::class, 'statement']);


 Route::get('/payrolls/{month}/nssf-voucher-view', [PayrollController::class, 'nssfVoucherView']);
Route::get('/payrolls/{month}/tuico-voucher-view', [PayrollController::class, 'tuicoVoucherView']);


});