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
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'created_at',
    // ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'transactioner',
        'value' => function ($model) {
            return isset($model->transactionerUser) ? $model->transactionerUser->displayName : null;
        }
    ],
      [
        'class' => \rabint\components\grid\JDateColumn::class,
       'attribute' => 'created_at',
       'dateFormat' => 'j F Y H:i:s',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'amount',
    ],
    [
        'class' => \rabint\components\grid\AdvanceEnumColumn::class,
        'attribute' => 'status',
        'enum' => \rabint\finance\Config::statuses(),
    ],
    [
        'class' => \rabint\components\grid\AdvanceEnumColumn::class,
        'attribute' => 'gateway',
        'enum' => \rabint\finance\Config::paymentGateways(),
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'gateway_reciept',
    ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'gateway_meta',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'transactioner_ip',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'internal_reciept',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'token',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'return_url',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'additional_rows',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'metadata',
    // ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'vAlign' => 'middle',

        'urlCreator' => function ($action, $model, $key, $index) {

            return Url::to([$action, 'id' => $key]);
        },

        'urlCreator' => function ($action, $model, $key, $index) {
            /*if($action=='view'){
                    return \Yii::$app->urlManagerFrontend->createAbsoluteUrl([$action,'id'=>$model->id]);    
                }*/
            return Url::to([$action, 'id' => $key]);
        },
        'viewOptions' => ['role' => 'modal-remote', 'title' => Yii::t('rabint', 'View'), 'data-toggle' => 'tooltip'],
        'updateOptions' => ['role' => 'modal-remote', 'title' => Yii::t('rabint', 'Update'), 'data-toggle' => 'tooltip'],
        'deleteOptions' => [
            'role' => 'modal-remote', 'title' => Yii::t('rabint', 'Delete'),
            'data-confirm' => false, 'data-method' => false, // for overide yii data api
            'data-request-method' => 'post',
            'data-toggle' => 'tooltip',
            'data-confirm-title' => Yii::t('rabint', 'Are you sure?'),
            'data-confirm-message' => Yii::t('rabint', 'Are you sure want to delete this item')
        ],
                'template' => '{view}',
        //        'buttons' => [
        //            'shortlink' => function ($url, $model) {
        //                $url = \Yii::$app->urlManager->createUrl(['/open/admin/index', 'EmployeeExecutiveSearch' => ['employee_id'=>$model->_id]]);
        //                return \yii\bootstrap4\Html::a('<span class="fa fa-th-list"></span>', $url, [
        //                    'title' => Yii::t('rabint', 'short link'),
        //                    'target' => '_BLANK'
        //                    //'role' => 'modal-remote',
        //                    //'data-toggle' => 'tooltip'
        //                ]);
        //            },
        //        ],
    ],

];
