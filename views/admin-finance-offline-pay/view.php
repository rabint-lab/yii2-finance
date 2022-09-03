<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model rabint\finance\models\FinanceOfflinePay */

$this->title = Yii::t('app','فیش پرداخت');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rabint', 'Finance Offline Pays'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$isModalAjax = Yii::$app->request->isAjax;

$this->context->layout = "@themeLayouts/full";

?>


<div class="box-view finance-offline-pay-view"  id="ajaxCrudDatatable">
    <h2 class="ajaxModalTitle" style="display: none"><?=  $this->title; ?></h2>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card block block-rounded <?= $isModalAjax?'ajaxModalBlock':'';?> ">
                <div class="card-header block-header block-header-default">
                    <div class="block-action d-block mr-auto ml-auto mt-2">

                    </div>
                </div>
                <div class="card-body block-content block-content-full">
<div class="row">
    <div class="col-sm-12">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                //'created_at' => [
                //       'attribute' => 'created_at',
                //    'value' => \rabint\helpers\locality::jdate('j F Y H:i:s', $model->created_at),
                //    ],
                //     'transactioner' => [
                //         'attribute' => 'transactioner',
                //        'value' => isset($model->transactionerUser)?$model->transactionerUser->displayName:null,
                //     ],
                //     'amount',
                //      [
                //         'attribute' => 'gateway',
                //           'value' => function() use($model){
                //              $value =$model->gateway;
                //               $enum = \rabint\finance\Config::paymentGateways();
                //             $data = isset($enum[$value]['title']) ? $enum[$value]['title'] : $value;
                //             $class = isset($enum[$value]['class']) ? $enum[$value]['class'] : 'default';
                //             return '<span class="badge badge-' . $class . '">' . $data . '</span>';
                //         },
                //         'format' => 'raw',
                //     ],
//                                'id',
                'user_id'=>[
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'user_id',
                    'value'=>function($model){
                        if($model->user_id==null)
                            return '';
                        $user = \common\models\User::findOne($model->user_id);
                        return $user->displayName;
                    }
                ],
                'transaction_id'=>[
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'transaction_id',
                    'label'=>Yii::t('rabint','شماره فاکتور')
                ],
//            'callback:ntext',
                'amount',
                'image'=>[
                    'format'=>'raw',
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'image',
                    'value'=>function($model){
                        if($model->image==null)
                            return '';
                        $file = \rabint\attachment\models\Attachment::findOne($model->image);
                        return "<img src='".$file->getUrl()."' >";
//                    $user = ::findOne($model->user_id);
//                    return $user->displayName;
                    }
                ],
                'date_pay',
                'tracking_cod',
                'description:ntext',
                'created_at'=>[
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'created_at',
                    'value'=>function($model){
                        return rabint\helpers\locality::anyToJalali($model->created_at,'Y-m-d');
                    }
                ],
//            'updated_at',
            ],
        ]) ?>
    </div>
</div>
                    <div class="row">
                        <div class="col-sm-6">
                            <?= Html::a(Yii::t('rabint', 'Delete'), ['delete', 'id' => $model->id], [
                                'class' => 'btn btn-danger d-block mr-auto ml-auto mb-2',
                                'data' => [
                                    'confirm' => Yii::t('rabint', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                            ]) ?>
                        </div>
                        <div class="col-sm-6">
                            <?php
                            if($model->status !==\rabint\finance\models\FinanceOfflinePay::STATUS_ACCEPTED):
                            ?>
                            <?=Html::a(Yii::t('rabint','تایید'),["/finance/default/offline-pay-change-status","id"=>$model->id,"status"=>\rabint\finance\models\FinanceOfflinePay::STATUS_ACCEPTED],['class'=>'btn btn-success d-block mr-auto ml-auto mb-2'])?>
                            <?php else: ?>
                            <span class="btn btn-success d-block mr-auto ml-auto mb-2"><?=Yii::t('rabint','تایید شده')?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
