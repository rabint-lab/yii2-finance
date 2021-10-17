<?php


namespace rabint\finance\services;


use rabint\finance\models\FinanceTransactions;

class service
{

    public static function factory(){
        return new static;
    }


    /**
     * @param $id integer
     * @param $title string
     * @param $count int
     * @param $object string
     * @param $price string
     * @param $object_id integer
     * @return bool
     */

    public function addItem($id,$title,$count,$object,$price,$object_id){
        $model = $this->findModel($id);
        if(!in_array($model->status,[FinanceTransactions::TRANSACTION_INPROCESS,FinanceTransactions::TRANSACTION_PENDING]))
            return false;
        $meta = json_decode($model->metadata,'true');
        $meta = array_filter($meta,function ($item)use ($object,$object_id){
            if(isset($item[5]) && $item[5]==$object && $item[4]==$object_id){
                return false;
            }
            else
                return true;
        });
        array_push($meta,[$title,$count,$price,$price*$count,$object_id,$object]);
        $model->metadata = json_encode($meta);
        $model->amount = self::getAmount($meta);
        return $model->save()===true?true:false;
    }

    public function addAdditionalRow($id,$user_id,$amount,$discription,$metadata){
        $model = $this->findModel($id);
        if(!in_array($model->status,[FinanceTransactions::TRANSACTION_INPROCESS,FinanceTransactions::TRANSACTION_PENDING]))
            return false;
        $meta = json_decode($model->additional_rows,'true');
        array_push($meta,[
            "user_id" => $user_id,
            "amount" => $amount,
            "description" => $discription,
            "metadata" => $metadata
        ]);
        $model->additional_rows = json_encode($meta);
        return $model->save()===true?true:false;
    }

    /**
     * @param $meta array|string
     * @param $without array
     * @return int
     */

    public function getAmount($meta,$without = []){
        if(!is_array($without))
            $without = [$without];
        if(!is_array($meta))
            $meta = json_decode($meta,true);
        $amount = 0;
        foreach ($meta as $item){
            if(isset($item[5])&&in_array($item[5],$without))continue;
            $amount += $item[3];
        }
        return $amount;
    }

    private function findModel($id)
    {
        if (($model = FinanceTransactions::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(\Yii::t('rabint', 'The requested page does not exist.'));
        }
    }

}