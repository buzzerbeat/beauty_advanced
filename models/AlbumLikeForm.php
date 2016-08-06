<?php

namespace beauty\models;


use common\components\Utility;
use yii\base\Model;

class AlbumLikeForm extends Model
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

    public function like()
    {
        $this->userId = \Yii::$app->user->identity->id;
        $alLike = AlbumLike::find()->where([
            'album_id' => $this->getId(),
            'user_id' => $this->userId,
            'like' => 1,
        ])->one();
        if (!$alLike) {
            $alLike = new AlbumLike();
            $alLike->album_id = $this->getId();
            $alLike->user_id = $this->userId;
            $alLike->like = 1;
            $alLike->time = time();
            if (!$alLike->save()) {
                $this->addErrors($alLike->getErrors());
                return false;
            }

            $aCount = AlbumCount::findOne(['album_id'=>$this->getId()]);
            if (!$aCount) {
                $aCount = new AlbumCount();
                $aCount->album_id = $this->getId();
                $aCount->like = 1;
                if (!$aCount->save()) {
                    $this->addErrors($aCount->getErrors());
                    return false;
                }
            } else {
                if (!$aCount->updateCounters(['like' => 1])) {
                    $this->addErrors($aCount->getErrors());
                    return false;
                }
            }
        }
        return true;
    }
}