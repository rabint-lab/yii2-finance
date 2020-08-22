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
    [
        'attribute' => 'created_at',
        'value' => function ($model) {
            return (new \rabint\helpers\locality())->jdate('j F Y - H:i', $model->created_at);
        },
    ],
    [
        'class' => \rabint\components\grid\EnumColumn::className(),
        'attribute' => 'status',
        'enum' => yii\helpers\ArrayHelper::getColumn(rabint\finance\models\FinanceDraft::statuses(), 'title'),
        'format' => 'raw',
        'value' => function ($model) {
            return '<span class="label label-' . rabint\finance\models\FinanceDraft::statuses()[$model->status]['class'] . '">' . rabint\finance\models\FinanceDraft::statuses()[$model->status]['title'] . '</span>';
        }
    ],
    'description:ntext',
    [
        'attribute' => 'check_url',
        'format' => 'raw',
        'value' => function ($model) {
            return Html::a(\Yii::t('rabint', 'مشاهده'), $model->checkImg, ['target' => '_blank']);
        },
    ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'created_at',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'updated_at',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'status',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'description',
    // ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'check_url',
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
        //        'template' => '{view} {update} {delete} <br/>{shortlink}',
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
