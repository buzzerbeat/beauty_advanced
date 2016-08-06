<?php

namespace beauty\models;


use common\components\Utility;
use yii\base\Model;

class AlbumFavForm extends Model
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

    public function fav()
    {
        $this->userId = \Yii::$app->user->identity->id;
        $alFav = AlbumFav::find()->where([
            'album_id' => $this->getId(),
            'user_id' => $this->userId,
            'fav' => 1,
        ])->one();
        if (!$alFav) {
            $alFav = new AlbumFav();
            $alFav->album_id = $this->getId();
            $alFav->user_id = $this->userId;
            $alFav->fav = 1;
            $alFav->time = time();
            if (!$alFav->save()) {
                $this->addErrors($alFav->getErrors());
                return false;
            }
            $aCount = AlbumCount::findOne(['album_id'=>$this->getId()]);
            if (!$aCount) {
                $aCount = new AlbumCount();
                $aCount->album_id = $this->getId();
                $aCount->fav = 1;
                if (!$aCount->save()) {
                    $this->addErrors($aCount->getErrors());
                    return false;
                }
            } else {
                if (!$aCount->updateCounters(['fav' => 1])) {
                    $this->addErrors($aCount->getErrors());
                    return false;
                }
            }
        }
        return true;
    }
}