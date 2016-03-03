<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 08-Feb-16
 * Time: 4:44 PM
 */


return [

    'chart_of_account_level_max'=>4,
    'workspace_type' => ['1' => 'Showroom', '2' => 'Delivery Center', '3' => 'Factory'],
    'pagination' => 10,
    'status' => ['1'=>'Active','0'=>'Inactive'],
    'material_type' => ['1'=>'Normal', '2'=>'Color', '3'=>'Mixer'],
    'customer_type' =>['1'=>'Dealer','2'=>'Non Dealer'],
    'sales_customer_type' => [1=>'Employee',2 =>'Supplier',3=>'Customer'],
    'supplier_types'=>[1=>'Local',2=>'International'],
    'person_type_employee'=>1,
    'person_type_supplier'=>2,
    'person_type_customer'=>3,
    'delivery_type'=>[1=>'Not Yet',2=>'Partial',3=>'Fully'],
    'transaction_accounts'=>['11000', '12000', '20000', '30000', '41000', '50000', '60000', '31000', '32000', '23000', '24000', '25000', '26000', '27000'],
    'delivery_status'=>[1=>'Not Yet',2=>'Partial',4=>'Delivered'],
    'product_return_type'=> [ 1=>'Cash',2=>'Pay Due',3=>'Due',4=>'Pay Due & Cash Return'],
    'balance_type_opening'=>0,
    'balance_type_intermediate'=>1,
    'balance_type_closing'=>2,
    'transaction_type'=> ['general'=>1,'sales'=>2,'sales_return'=>3,'purchase'=>4,'purchase_return'=>5,'wage'=>6, 'personal'=>7, 'draw'=>8, 'investment'=>9, 'office_supply'=>10,'salary'=>11,'salary_payment'=>12,'cash_transfer'=>13],
    'debit_credit_indicator'=>['debit'=>1,'credit'=>2],
    'month' => [
        'January' => '01',
        'February' => '02',
        'March' => '03',
        'April' => '04',
        'May' => '05',
        'June' => '06',
        'July' => '07',
        'August' => '08',
        'September' => '09',
        'October' => '10',
        'November' => '11',
        'December' => '12',
    ],
    'employee_type'=>[
        'Regular'=>1,
        'Daily Worker'=>2
    ],
    'adjustment_account_from'=>['25000', '27000'],
    'adjustment_account_to'=>['13000', '14000'],
    'salary_payment_status'=>['not_yet'=>0,'partial'=>1,'complete'=>2],
];