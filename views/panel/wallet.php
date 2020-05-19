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
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'transactioner',
                    'value' => function($model) {
                        return $model->transactionerUser->displayName;
                    },
                ],
                [
                    'attribute' => 'user_id',
                    'headerOptions' => ['class' => 'hidden-xs'],
                    'contentOptions' => ['class' => 'hidden-xs'],
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
                        return number_format($model->amount) . ' تومان';
                    }
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function ($model) {
                        return (new \rabint\helpers\locality())->jdate('j F Y - H:i', $model->created_at);
                    },
                ],
                [
                    'attribute' => 'description',
                    'headerOptions' => ['class' => 'hidden-xs'],
                    'contentOptions' => ['class' => 'hidden-xs'],
                ]
            ]
        ]);
        ?>
    </div>
</div>
