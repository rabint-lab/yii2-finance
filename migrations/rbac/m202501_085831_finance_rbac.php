<?php


use rabint\rbac\AutoMigration;
use rabint\user\models\User;

class m202501_085831_finance_rbac extends AutoMigration
{


    public function roles()
    {
        return [
//           'support' => [
//                'description' => \Yii::t('rabint', 'کاربر پشتیبان'),
//                'children' => [User::ROLE_USER],
//            ],
        ];
    }

    public function permissions()
    {
        return [
            'FinView' => [
                'description' => \Yii::t('rabint', 'مشاهده (صورتحساب، تراکنش ها، پرداخت آفلاین)'),
                'parents' => [User::ROLE_MANAGER],
                'data' => ['group' => Yii::t('rabint', 'مالی')]
            ],
            'FinAddInvoice' => [
                'description' => \Yii::t('rabint', 'تعریف صورتحساب برای کاربر'),
                'parents' => [User::ROLE_MANAGER],
                'data' => ['group' => Yii::t('rabint', 'مالی')]
            ],
            'FinChangeWallet' => [
                'description' => \Yii::t('rabint', 'تغییر موجودی کاربر'),
                'parents' => [User::ROLE_MANAGER],
                'data' => ['group' => Yii::t('rabint', 'مالی')]
            ],
            'FinOffline' => [
                'description' => \Yii::t('rabint', 'ثبت و تایید واریز آفلاین'),
                'parents' => [User::ROLE_MANAGER],
                'data' => ['group' => Yii::t('rabint', 'مالی')]
            ],
            'FinWalletConnect' => [
                'description' => \Yii::t('rabint', 'مدیریت اتصال به کیف پول'),
                'parents' => [User::ROLE_MANAGER],
                'data' => ['group' => Yii::t('rabint', 'مالی')]
            ],
        ];
    }

    public function rules()
    {
        return [
//            'updateOnwModel' => [
//                'class' => OwnModelRule::class,
//                'objConfig' => ['name' => 'updateOnwModel'],
//                'description' => \Yii::t('rabint', 'ویرایش آیتم های خود'),
//                'parents' => [
//                    User::ROLE_AUTHOR
//                ],
//            ],
//            'publishOnwModel' => [
//                'class' => OwnModelRule::class,
//                'objConfig' => ['name' => 'publishOnwModel'],
//                'description' => \Yii::t('rabint', 'انتشار آیتم های خود'),
//                'parents' => [
//                    User::ROLE_AUTHOR
//                    /* , User::ROLE_CONTRIBUTOR, User::ROLE_AUTHOR, User::ROLE_EDITOR */
//                ],
//            ],
        ];
    }

    public function seeds()
    {
        return [
//            ['role', 1, User::ROLE_ADMINISTRATOR],
//            ['role', 2, User::ROLE_MANAGER],
//            ['role', [3], User::ROLE_USER],
            //['permission', [1,2], 'loginToBackend'],
            //['rule', User::ROLE_USER, User::RULE_USER_OWN_MODEL],
        ];
    }
}
