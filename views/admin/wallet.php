<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel rabint\term\models\RegisterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rabint', 'سوابق مالی');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="block">
    <div class="card-body block-content">
        <br/>
        <div class="allAmounts">
            <div class="col-sm-4">
                <?php
                $amounts = \rabint\finance\models\FinanceWallet::find()->select('amount')->sum('amount');
                ?>
                <span class="title">موجودی کل حساب ها: </span>
                <span class="value"><?= number_format($amounts). ' '.\rabint\helpers\currency::title() ?> </span>
            </div>
            <div class="col-sm-4">
                <?php
                $benefits = \rabint\finance\models\FinanceWallet::find()->select('amount')->where(['user_id' => '0'])->sum('amount');
                ?>
                <span class="title">درآمد سایت: </span>
                <span class="value"><?= number_format($benefits).' '.\rabint\helpers\currency::title() ?></span>
            </div>
            <div class="col-sm-4">
                <?php
                $amounts = \rabint\finance\models\FinanceWallet::find()->select('amount')->sum('amount');
                ?>
                <span class="title">طلب کاربران :</span>
                <span class="value"><?= number_format($amounts - $benefits).' '.\rabint\helpers\currency::title() ?> </span>
            </div>
            <div class="clearfix"></div>
        </div>
        <hr/>

        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'transactioner',
                    'value' => function($model) {
                        return ($model->transactionerUser)?$model->transactionerUser->displayName:"حذف شده";
                    },
                ],
                [
                    'attribute' => 'user_id',
                    'value' => function ($model) {
                        if (empty($model->user)) {
                            return 'وبسایت';
                        }
                        return $model->user->displayName;
                    }
                ],
                [
                    'attribute' => 'amount',
                    'value' => function ($model) {
                        return number_format($model->amount) . ' '.\rabint\helpers\currency::title();
                    }
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function ($model) {
                        return (new \rabint\helpers\locality())->jdate('j F Y - H:i', $model->created_at);
                    }
                ],
                'description:ntext'
            ]
        ]);
        ?>
    </div>
</div>
