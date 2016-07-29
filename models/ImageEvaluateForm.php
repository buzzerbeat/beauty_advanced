<?php

namespace beauty\models;


use common\components\Utility;
use yii\base\Model;

class ImageEvaluateForm extends Model
{
    public $sid;
    private $userId;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['sid'], 'required'],
        ];
    }

    public function getId() {
        return Utility::id($this->sid);
    }

    public function dig()
    {
        $this->userId = \Yii::$app->user->identity->id;
        $imgEvl = ImageEvaluate::find()->where([
            'image_id' => $this->getId(),
            'user_id' => $this->userId,
            //'evaluate' => ImageEvaluate::EVALUATE_DIG,
        ])->one();
        if (!$imgEvl) {
            $imgEvl = new ImageEvaluate();
            $imgEvl->image_id = $this->getId();
            $imgEvl->user_id = $this->userId;
            $imgEvl->evaluate = ImageEvaluate::EVALUATE_DIG;
            $imgEvl->time = time();
            if (!$imgEvl->save()) {
                $this->addErrors($imgEvl->getErrors());
                return false;
            }
        }
        elseif($imgEvl->evaluate == ImageEvaluate::EVALUATE_BURY){
        	$imgEvl->evaluate = ImageEvaluate::EVALUATE_DIG;
        	if(!$imgEvl->save()){
        	    $this->addErrors($imgEvl->getErrors());
        	    return false;
        	}
        }
        return true;
    }
    
    public function bury(){
        $this->userId = \Yii::$app->user->identity->id;
        $imgEvl = ImageEvaluate::find()->where([
            'image_id' => $this->getId(),
            'user_id' => $this->userId,
        ])->one();
        if (!$imgEvl) {
            $imgEvl = new ImageEvaluate();
            $imgEvl->image_id = $this->getId();
            $imgEvl->user_id = $this->userId;
            $imgEvl->evaluate = ImageEvaluate::EVALUATE_BURY;
            $imgEvl->time = time();
            if (!$imgEvl->save()) {
                $this->addErrors($imgEvl->getErrors());
                return false;
            }
        }
        elseif($imgEvl->evaluate == ImageEvaluate::EVALUATE_DIG){
            $imgEvl->evaluate = ImageEvaluate::EVALUATE_BURY;
            $imgEvl->time = time();
            if(!$imgEvl->save()){
                $this->addErrors($imgEvl->getErrors());
                return false;
            }
        }
        return true;
    }
}