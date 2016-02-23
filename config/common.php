<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 08-Feb-16
 * Time: 4:44 PM
 */



return [

    'workspace_type' => ['1'=>'Showroom', '2'=>'Delivery Center', '3'=>'Factory'],
    'pagination' => 10,
    'status' => ['1'=>'Active','0'=>'Inactive'],
    'material_type' => ['1'=>'Normal', '2'=>'Color', '3'=>'Mixer'],
    'customer_type' =>['1'=>'Dealer','2'=>'Non Dealer'],
    'sales_customer_type' => [1=>'Employee',2 =>'Supplier',3=>'Customer'],
    'material_type' => ['1'=>'Normal', '2'=>'Color', '3'=>'Mixer'],
    'supplier_types'=>[1=>'Local',2=>'International'],
    'person_type_employee'=>1,
    'person_type_supplier'=>2,
    'person_type_customer'=>3,
    'delivery_type'=>[1=>'Not Yet',2=>'Partial',3=>'Fully'],
    'transaction_accounts'=>['11000', '12000', '20000', '30000', '41000', '50000', '60000', '31000', '32000', '23000', '24000', '25000', '26000'],
    'delivery_status'=>[1=>'Not Yet',2=>'Partial',4=>'Delivered'],
    'product_return_type'=> [ 1=>'Cash',2=>'Pay Due',3=>'Due',4=>'Pay Due & Cash Return'],
    'balance_type_opening'=>0,
    'balance_type_intermediate'=>1,
    'balance_type_closing'=>2,
    'transaction_type'=> ['general'=>1,'sales'=>2,'sales_return'=>3,'purchase'=>4,'purchase_return'=>5,'wages'=>6, 'personal'=>7],
    'debit_credit_indicator'=>['debit'=>1,'credit'=>2],
];