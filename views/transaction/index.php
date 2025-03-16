<?php

/* @var $this yii\web\View */
/* @var $searchModel rabint\finance\models\FinanceTransactionsSearch */

/* @var $dataProvider yii\data\ActiveDataProvider */

use rabint\finance\models\FinanceTransactions;

$this->title = Yii::t('rabint', 'صورت‌حساب ها');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="list_box finance-transactions-index">

    <div class="row">
        <?php //echo $this->render('_search', ['model' => $searchModel]); ?>


        <?=
        \kartik\grid\GridView::widget([
            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
            'id' => 'crud-datatable',
            'columns' => [
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
//                [
//                    'class' => '\kartik\grid\DataColumn',
//                    'attribute' => 'transactioner',
//                    'value' => function ($model) {
//                        return isset($model->transactionerUser) ? $model->transactionerUser->displayName : null;
//                    }
//                ],
                [
                    'class' => \rabint\components\grid\JDateColumn::class,
                    'attribute' => 'created_at',
                    'dateFormat' => 'j F Y H:i:s',
                ],
                [
                    'attribute' => 'amount',
                    'value' => function ($model) {
                        return number_format($model->amount) . ' ریال';
                    }
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
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'template' => '{pay}',
                    'buttons' => [
                        'pay' => function ($url, $model) {
                            if ($model->status > FinanceTransactions::TRANSACTION_INPROCESS) {
                                return;
                            }
                            $url = ['/finance/transaction/view', 'id' => $model->id];
                            return \yii\bootstrap4\Html::a(Yii::t('app', 'پرداخت'), $url, [
                                'title' => Yii::t('rabint', 'short link'),
//                                'target' => '_BLANK',
                                'class' => 'btn btn-success btn-sm',
                                //'role' => 'modal-remote',
                                //'data-toggle' => 'tooltip'
                            ]);
                        },
                    ],
                ],
            ]
        ]);
        ?>

    </div>
</div>
