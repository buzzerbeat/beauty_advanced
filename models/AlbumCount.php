<?php

namespace beauty\models;

use Yii;

/**
 * This is the model class for table "album_count".
 *
 * @property integer $album_id
 * @property integer $like
 * @property integer $dig
 * @property integer $fav
 * @property integer $visited
 * @property integer $bury
 * @property integer $share
 */
class AlbumCount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'album_count';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('bDb');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['like', 'dig', 'fav', 'visited', 'bury', 'share'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'album_id' => 'Album ID',
            'like' => 'Like',
            'dig' => 'Dig',
            'fav' => 'Fav',
            'visited' => 'Visited',
            'bury' => 'Bury',
            'share' => 'Share',
        ];
    }
}