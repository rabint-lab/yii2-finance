<?php

use rabint\widgets\GridView;

/* @var $this yii\web\View */
/* @var $searchModel rabint\finance\models\FinanceTransactionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rabint', 'صورتحساب ها');
$this->params['breadcrumbs'][] = $this->title;

$this->context->layout = "@themeLayouts/full";


?>

<div class="finance-transactions-index" id="ajaxCrudDatatable">

    <h2 class="ajaxModalTitle" style="display: none"><?= $this->title; ?></h2>
    <div class="content-search">
        <?php //echo $this->render('_search',['model'=>$searchModel]);?>
    </div>
    <div id="ajaxCrudDatatable">
        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            'showAddBtn' => (\rabint\helpers\user::can('FinAddInvoice')),
            'pjax' => true,
            'columns' => require(__DIR__ . '/_columns.php'),
            'modelTitle' => $this->title,
            'bulkActions' => $this->context::bulkActions(),
        ]) ?>
    </div>
</div>
