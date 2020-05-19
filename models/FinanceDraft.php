<?php

namespace rabint\finance\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "finance_draft".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $checker_id
 * @property string $title
 * @property string $bank
 * @property string $form_id
 * @property integer $created_at
 * @property string $updated_at
 * @property integer $status
 * @property string $description
 * @property string $check_url
 *
 * @property User $user
 * @property User $checker
 */
class FinanceDraft extends \yii\db\ActiveRecord {

    const STATUS_DRAFT = 0;
    const STATUS_REJECT = 1;
    const STATUS_CONFIRMED = 2;
    const SCENARIO_CHANGE_STATUS = 'changeStatus';

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'finance_draft';
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CHANGE_STATUS] = ['status'];
        return $scenarios;
    }

    public static function statuses() {
        return [
            static::STATUS_DRAFT => ['title' => \Yii::t('rabint', 'بررسی نشده'), 'class' => 'default'],
            static::STATUS_REJECT => ['title' => \Yii::t('rabint', 'برگشت خورده'), 'class' => 'danger'],
            static::STATUS_CONFIRMED => ['title' => \Yii::t('rabint', 'تایید شده'), 'class' => 'success'],
        ];
    }

    public function behaviors() {
        return [
            [
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => time(),
            ],
            [
                'class' => \yii\behaviors\BlameableBehavior::className(),
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => 'checker_id',
            ],
            [
                'class' => \rabint\attachment\behaviors\AttechmentBehavior::className(),
                'attributes' => [
                    'check_url' => [
                        'storage' => 'local',
                        'component' => 'financeCheck',
                        'saveFilePath' => true,
                        'rules' => [
//                            'imageSize' => ['minWidth' => 300, 'minHeight' => 300],
                            'mimeTypes' => ['image/png', 'image/gif', 'image/jpg', 'image/jpeg'],
                            'extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                            'maxSize' => 1024 * 1024 * 1, // 1 MB
                            'tooBig' => Yii::t('rabint', 'File size must not exceed') . ' 1Mb'
                        ],
//                        'preset' => \rabint\attachment\attachment::imgPresetsFn('financeCheck'),
//                        'applyPresetAfterUpload' => '*'
                    ]
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['user_id', 'checker_id', 'created_at', 'status'], 'integer'],
            [['bank', 'form_id', 'description'], 'required'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['bank', 'form_id', 'updated_at'], 'string', 'max' => 45],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['checker_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['checker_id' => 'id']],
            ['check_url', 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('rabint', 'شناسه'),
            'user_id' => Yii::t('rabint', 'کاربر'),
            'checker_id' => Yii::t('rabint', 'تایید کننده'),
            'title' => Yii::t('rabint', 'عنوان'),
            'bank' => Yii::t('rabint', 'بانک -شعبه'),
            'form_id' => Yii::t('rabint', 'کد رهگیری/شماره چک'),
            'created_at' => Yii::t('rabint', 'تاریخ ثبت'),
            'updated_at' => Yii::t('rabint', 'تاریخ ویرایش'),
            'status' => Yii::t('rabint', 'وضعیت'),
            'description' => Yii::t('rabint', 'توضیحات'),
            'check_url' => Yii::t('rabint', 'تصویر چک'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChecker() {
        return $this->hasOne(User::className(), ['id' => 'checker_id']);
    }

    public function getCheckImg($default = null) {
        return \rabint\attachment\models\Attachment::getUrlByPath($this->check_url);
    }

}
