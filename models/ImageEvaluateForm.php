<?php

namespace beauty\models;


use common\components\Utility;
use beauty\models\ImageEvaluate;
use beauty\models\AlbumLikeForm;
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

    public function evaluate($evaluate)
    {
        $this->userId = \Yii::$app->user->identity->id;
        $imgEvl = ImageEvaluate::find()->where([
            'image_id' => $this->getId(),
            'user_id' => $this->userId,
        ])->one();
        if (!$imgEvl) {
            $imgEvl = new ImageEvaluate();
            $imgEvl->image_id = $this->getId();
            $imgEvl->user_id = $this->userId;
            $imgEvl->evaluate = $evaluate;
            $imgEvl->time = time();
            if (!$imgEvl->save()) {
                $this->addErrors($imgEvl->getErrors());
                return false;
            }
            
            if(!$this->likeAlbum($evaluate)){
            	return false;
            }
        }
        elseif($imgEvl->evaluate != $evaluate){
        	$imgEvl->evaluate = $evaluate;
        	$imgEvl->time = time();
        	if(!$imgEvl->save()){
        	    $this->addErrors($imgEvl->getErrors());
        	    return false;
        	}
        	
        	if(!$this->likeAlbum($evaluate)){
        	    return false;
        	}
        }
        
        
        return true;
    }
    
    private function likeAlbum($evaluate){
        //对应like所属图集
        if($evaluate == ImageEvaluate::EVALUATE_DIG){
            $img = Image::findOne($this->getId());
            $albumForm = new AlbumLikeForm();
            $field = ['AlbumLikeForm' => ['sid'=>Utility::sid($img->album_id)]];
           
            if(!$albumForm->load($field) || !$albumForm->like()){
                $this->addErrors($albumForm->getErrors());
                return false;
            }
        }
        
        return true;
    }
    
}