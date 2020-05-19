<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel rabint\finance\models\FinanceTransactionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rabint', 'Finance Transactions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="list_box finance-transactions-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <div class="row">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            /*
             'id',  'created_at',  'transactioner',  'amount',  'status',  'gateway',  'gateway_reciept',  'gateway_meta',  'transactioner_ip',  'internal_reciept',  'token',  'return_url:url',  'additional_rows:ntext',  'metadata', 
            */        
            ob_start(); ?>
        
            <div class="col-sm-12">
                <h4 class="title">
                    <?= Html::a(Html::encode($model->id), ['view', 'id' => $model->id]);?>
                </h4>
            </div>
            
            <?php  return ob_get_clean();
        },
    ]) ?>

        
    </div>
</div>
