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
    Route::resource('articles', 'ArticlesController');
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
    Route::resource('salesOrder', 'Sales\SalesOrderController');
    Route::resource('productionRegisters', 'Register\ProductionRegistersController');
    Route::resource('designations', 'Employee\DesignationsController');
    Route::resource('employees', 'Employee\EmployeesController');
    Route::resource('suppliers', 'Setup\SuppliersController');
    Route::resource('purchases', 'Setup\PurchasesController');
    Route::resource('charts', 'Account\ChartOfAccountsController');
});

Route::post('module_select', array('as' => 'ajax.module_select', 'uses' => 'AjaxController@getModules'));
Route::post('parent_select', array('as' => 'ajax.parent_select', 'uses' => 'AjaxController@checkParents'));
Route::post('customer_select', array('as' => 'ajax.customer_select', 'uses' => 'AjaxController@getCustomers'));
Route::post('supplier_select', array('as' => 'ajax.supplier_select', 'uses' => 'AjaxController@getSuppliers'));
Route::post('employee_select', array('as' => 'ajax.employee_select', 'uses' => 'AjaxController@getEmployees'));
Route::post('product_select', array('as' => 'ajax.product_select', 'uses' => 'AjaxController@getProducts'));

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);