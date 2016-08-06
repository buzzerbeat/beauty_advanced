<?php

namespace beauty\models;

use Yii;

/**
 * This is the model class for table "album_like".
 *
 * @property integer $id
 * @property integer $album_id
 * @property integer $user_id
 * @property integer $like
 * @property integer $time
 */
class AlbumLike extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'album_like';
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
            [['album_id', 'user_id', 'like', 'time'], 'required'],
            [['album_id', 'user_id', 'like', 'time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'album_id' => 'Album ID',
            'user_id' => 'User ID',
            'like' => 'Like',
            'time' => 'Time',
        ];
    }
}