<?php
use yii\helpers\Url;

return [
    [
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    // [
    //    'class' => '\kartik\grid\DataColumn',
    //    'attribute' => 'creator_id',
    //    'value' => function ($model) {
    //        return $model->creator ? $model->creator->displayName : $model->creator_id;
    //    },
    //],
    //[
    //    'class' => \rabint\components\grid\AttachmentColumn::class,
    //    'attribute' => 'avatar',
    //    'size' => [60, 80],
    // // 'filterOptions' => ['style' => 'max-width:60px;'],
    //],
    //[
    //    'class' => \rabint\components\grid\AdvanceEnumColumn::class,
    //    'attribute' => 'status',
    //    'enum' => \app\modules\open\models\Company::statuses(),
    //],
    //[
    //    'class' => \rabint\components\grid\JDateColumn::class,
    //    'attribute' => 'establish_date',
    //    'dateFormat' => 'j F Y H:i:s',
    //],
        // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'id',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'user_id',
    ],
//    [
//        'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'transaction_id',
//    ],
//    [
//        'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'callback',
//    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'amount',
        'value'=>function($model){return $model->amount;}
    ],
//    [
//        'class'=>'\kartik\grid\DataColumn',
//        'attribute'=>'image',
//    ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'date_pay',
     ],
     [
         'class'=>'\kartik\grid\DataColumn',
         'attribute'=>'tracking_cod',
     ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'description',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_at',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'updated_at',
    // ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign'=>'middle',

        'urlCreator' => function($action, $model, $key, $index) { 
               
                return Url::to([$action,'id'=>$key]);
        },

        'urlCreator' => function($action, $model, $key, $index) { 
                /*if($action=='view'){
                    return \Yii::$app->urlManagerFrontend->createAbsoluteUrl([$action,'id'=>$model->id]);    
                }*/
                return Url::to([$action,'id'=>$key]);
        },
        'viewOptions'=>['role'=>'modal-remote','title'=>Yii::t('rabint', 'View'),'data-toggle'=>'tooltip'],
        'updateOptions'=>['role'=>'modal-remote','title'=>Yii::t('rabint', 'Update'), 'data-toggle'=>'tooltip'],
        'deleteOptions'=>['role'=>'modal-remote','title'=>Yii::t('rabint', 'Delete'),
                          'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                          'data-request-method'=>'post',
                          'data-toggle'=>'tooltip',
                          'data-confirm-title'=>Yii::t('rabint', 'Are you sure?'),
                          'data-confirm-message'=>Yii::t('rabint', 'Are you sure want to delete this item?')         ],
        'template' => '{accept} {view} {delete}',
        'buttons' => [
            'accept' => function ($url, $model) {
                if($model->status==\rabint\finance\models\FinanceOfflinePay::STATUS_ACCEPTED){
                    return "";
                }else{
                    return \yii\bootstrap4\Html::a('<i class="fas fa-check"></i>', ["/finance/default/offline-pay-change-status","id"=>$model->id,"status"=>\rabint\finance\models\FinanceOfflinePay::STATUS_ACCEPTED], [
                        'title' => Yii::t('rabint', 'تایید پرداخت'),
                        'target' => '_BLANK'
                    ]);
                }
            },
        ],
    ],

];   
