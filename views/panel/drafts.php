<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel rabint\finance\models\FinanceDraftSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rabint', 'حواله های ثبت شده');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grid-box finance-draft-index">
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-sm-12">
            <div class="box box-info">
                <div class="box-body">

                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'bank',
                            'form_id',
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
                                    return Html::a(\Yii::t('rabint', 'مشاهده'), $model->checkImg,['target'=>'_blank']);
                                },
                            ],
                        ],
                    ]);
                    ?>

                </div>
            </div>
        </div>
    </div>
</div>

