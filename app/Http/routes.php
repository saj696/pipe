<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    Route::get('/', 'HomeController@index');
    Route::auth();
    Route::get('/home', 'HomeController@index');
    Route::resource('components', 'System\ComponentsController');
    Route::resource('modules', 'System\ModulesController');
    Route::resource('tasks', 'System\TasksController');
    Route::resource('groups', 'User\UserGroupsController');
    Route::resource('users', 'User\UsersController');
    Route::resource('roles', 'User\RolesController');
    Route::resource('workspaces', 'Setup\WorkspacesController');
    Route::resource('product_types', 'Setup\ProductTypesController');
    Route::resource('products', 'Setup\ProductsController');
    Route::resource('materials', 'Setup\MaterialsController');
    Route::resource('usageRegisters', 'Register\UsageRegistersController');
    Route::resource('customers', 'Customer\CustomersController');
    Route::resource('providers', 'Setup\ProvidersController');
    Route::get('sales_invoice/{id}', 'Sales\SalesOrderController@invoice_print');
    Route::resource('salesOrder', 'Sales\SalesOrderController');
    Route::resource('productionRegisters', 'Register\ProductionRegistersController');
    Route::resource('designations', 'Employee\DesignationsController');
    Route::resource('employees', 'Employee\EmployeesController');
    Route::resource('suppliers', 'Setup\SuppliersController');
    Route::resource('purchases', 'Setup\PurchasesController');
    Route::resource('charts', 'Account\ChartOfAccountsController');
    Route::resource('recorders', 'Account\TransactionRecordersController');
    Route::resource('initializations', 'Account\InitializationsController');
    Route::resource('salesDelivery', 'Sales\SalesDeliveryController');
    Route::resource('sales_return', 'Sales\SalesReturnController');
    Route::resource('adjustments', 'Account\AdjustmentsController');
    Route::resource('workspace_closing', 'Account\WorkspaceClosingController');
    Route::resource('year_closing', 'Account\YearClosingController');
    Route::resource('profile_update', 'User\ProfileUpdateController');
    Route::resource('cash_transaction', 'Account\CashTransactionController');
    Route::resource('purchases_return', 'Setup\PurchasesReturnController');
    Route::resource('salary_generator', 'Payroll\SalaryGeneratorController');
    Route::resource('salary_payment', 'Payroll\SalaryPaymentController');
    Route::resource('financial_year', 'System\FinancialYearSetupController');
    Route::resource('rollback', 'Account\RollbackController');
    Route::resource('receive_defect', 'Sales\ReceiveDefectController');
    Route::resource('daily_wage_payment', 'Payroll\DailyWagePaymentController');
    Route::resource('discarded_stock', 'Discarded\DiscardedMaterialStockController');
    Route::resource('discarded_sale', 'Discarded\DiscardedMaterialSaleController');

    // Report route
    Route::get('report_print', 'Report\PrintReportController@index');

    Route::get('purchase_report', 'Report\PurchasesReportController@index');
    Route::post('purchase_report', array('as' => 'ajax.purchase_report', 'uses' => 'Report\PurchasesReportController@getReport'));
    Route::get('sales_report', 'Report\SalesReportController@index');
    Route::post('sales_report', array('as' => 'ajax.sales_report', 'uses' => 'Report\SalesReportController@getReport'));
    Route::get('customer_report', 'Report\CustomerReportController@index');
    Route::post('customer_report', array('as' => 'ajax.customer_report', 'uses' => 'Report\CustomerReportController@getReport'));

    Route::get('trial_balance', 'Report\TrialBalanceController@index');
    Route::post('trial_balance', array('as' => 'ajax.trial_balance', 'uses' => 'Report\TrialBalanceController@getReport'));
    Route::get('material_usage_report', 'Report\MaterialUsageReportController@index');
    Route::post('material_usage_report', array('as' => 'ajax.material_usage_report', 'uses' => 'Report\MaterialUsageReportController@getReport'));
    Route::get('production_register_report', 'Report\ProductionRegisterReportController@index');
    Route::post('production_register_report', array('as' => 'ajax.production_register_report', 'uses' => 'Report\ProductionRegisterReportController@getReport'));
    Route::get('stock_report', 'Report\StockReportController@index');
    Route::post('stock_report', array('as' => 'ajax.stock_report', 'uses' => 'Report\StockReportController@getReport'));
    Route::get('personal_accounts_report', 'Report\PersonalAccountsReportController@index');
    Route::post('personal_accounts_report', array('as' => 'ajax.personal_accounts_report', 'uses' => 'Report\PersonalAccountsReportController@getReport'));
    Route::get('income_statement_report', 'Report\IncomeStatementReportController@index');
    Route::post('income_statement_report', array('as' => 'ajax.income_statement_report', 'uses' => 'Report\IncomeStatementReportController@getReport'));

    Route::get('payroll_report', 'Report\PayrollReportController@index');
    Route::post('payroll_report',array('as'=>'ajax.payroll_report', 'uses'=>'Report\PayrollReportController@getReport')) ;

    Route::post('adjustment_amounts', array('as' => 'ajax.adjustment_amounts', 'uses' => 'AjaxController@getAdjustmentAmounts'));
    Route::post('product_select', array('as' => 'ajax.product_select', 'uses' => 'AjaxController@getProducts'));
    Route::post('get_person_due_amount', array('as' => 'ajax.get_person_due_amount', 'uses' => 'AjaxController@getPersonDueAmount'));
    Route::post('get_person_balance_amount', array('as' => 'ajax.get_person_balance_amount', 'uses' => 'AjaxController@getPersonBalanceAmount'));
    Route::post('get_employee_list', array('as' => 'ajax.get_employee_list', 'uses' => 'AjaxController@getEmployeeList'));
    Route::post('get_employee', array('as' => 'ajax.get_employee', 'uses' => 'AjaxController@getEmployee'));
    Route::post('get_employee_payment', array('as' => 'ajax.get_employee_payment', 'uses' => 'AjaxController@getEmployeePayment'));
    Route::post('get_daily_worker_list', array('as' => 'ajax.get_daily_worker_list', 'uses' => 'AjaxController@getDailyWorkerList'));
    Route::get('cash_flow_report', 'Report\DailyCashFlowReportController@index');
    Route::post('cash_flow_report',array('as'=>'ajax.cash_flow_report', 'uses'=>'Report\DailyCashFlowReportController@getReport')) ;
});

Route::post('module_select', array('as' => 'ajax.module_select', 'uses' => 'AjaxController@getModules'));
Route::post('parent_select', array('as' => 'ajax.parent_select', 'uses' => 'AjaxController@checkParents'));
Route::post('customer_select', array('as' => 'ajax.customer_select', 'uses' => 'AjaxController@getCustomers'));
Route::post('supplier_select', array('as' => 'ajax.supplier_select', 'uses' => 'AjaxController@getSuppliers'));
Route::post('employee_select', array('as' => 'ajax.employee_select', 'uses' => 'AjaxController@getEmployees'));
Route::post('provider_select', array('as' => 'ajax.provider_select', 'uses' => 'AjaxController@getProviders'));
Route::post('get_personal_account_balance', array('as' => 'ajax.get_personal_account_balance', 'uses' => 'AjaxController@getPersonalAccountBalance'));

Route::post('transaction_recorder_amount', array('as' => 'ajax.transaction_recorder_amount', 'uses' => 'AjaxController@getTransactionRecorderAmount'));

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);



