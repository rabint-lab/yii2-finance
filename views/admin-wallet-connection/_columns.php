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
     [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'user_id',
        'value' => function ($model) {
            return $model->user ? $model->user->displayName : $model->user_id;
        },
    ],
    //[
    //    'class' => \rabint\components\grid\AttachmentColumn::class,
    //    'attribute' => 'avatar',
    //    'size' => [60, 80],
    // // 'filterOptions' => ['style' => 'max-width:60px;'],
    //],
    [
        'class' => \rabint\components\grid\AdvanceEnumColumn::class,
        'attribute' => 'provider',
        'enum' => \rabint\finance\models\WalletConnection::providers(),
    ],

    [
        'class' => \rabint\components\grid\JDateColumn::class,
        'attribute' => 'expire_date',
        'dateFormat' => 'j F Y H:i:s',
    ],
    [
        'class' => \rabint\components\grid\JDateColumn::class,
        'attribute' => 'created_at',
        'dateFormat' => 'j F Y H:i:s',
    ],

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
        'template' => '{delete} <br/>{shortlink}',
//        'buttons' => [
//            'shortlink' => function ($url, $model) {
//                $url = \Yii::$app->urlManager->createUrl(['/open/admin/index', 'EmployeeExecutiveSearch' => ['employee_id'=>$model->_id]]);
//                return \yii\bootstrap4\Html::a('<span class="fas fa-th-list"></span>', $url, [
//                    'title' => Yii::t('rabint', 'short link'),
//                    'target' => '_BLANK'
//                    //'role' => 'modal-remote',
//                    //'data-toggle' => 'tooltip'
//                ]);
//            },
//        ],
    ],

];   
