<?php

namespace beauty\models;

use Yii;

/**
 * This is the model class for table "album_tag_relation".
 *
 * @property integer $id
 * @property integer $album_id
 * @property integer $tag_id
 */
class AlbumTagRelation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'album_tag_relation';
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
            [['album_id', 'tag_id'], 'required'],
            [['album_id', 'tag_id'], 'integer'],
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
            'tag_id' => 'Tag ID',
        ];
    }
}
