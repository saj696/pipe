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
    Route::resource('materials', 'Setup\MaterialsController');
    Route::resource('usageRegisters', 'Register\UsageRegistersController');
    Route::resource('productionRegisters', 'Register\ProductionRegistersController');
    Route::resource('designations', 'Employee\DesignationsController');
    Route::resource('employees', 'Employee\EmployeesController');
    Route::resource('charts', 'Account\ChartOfAccountsController');
});

Route::post('module_select', array('as' => 'ajax.module_select', 'uses' => 'AjaxController@getModules'));
Route::post('parent_select', array('as' => 'ajax.parent_select', 'uses' => 'AjaxController@checkParents'));

Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);