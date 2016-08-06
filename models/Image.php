<?php

namespace beauty\models;

use Yii;
use common\components\Utility;

/**
 * This is the model class for table "image".
 *
 * @property integer $id
 * @property integer $album_id
 * @property string $path
 * @property string $unique_code
 * @property string $title
 * @property integer $status
 * @property string $source_url
 * @property integer $width
 * @property integer $height
 * @property integer $size
 * @property string $extension
 * @property integer $collect_time
 */
class Image extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'image';
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
            [['album_id', 'path', 'unique_code', 'status', 'source_url', 'extension', 'collect_time'], 'required'],
            [['album_id', 'status', 'width', 'height', 'size', 'collect_time'], 'integer'],
            [['path', 'title', 'source_url'], 'string', 'max' => 1024],
            [['unique_code'], 'string', 'max' => 255],
            [['extension'], 'string', 'max' => 64],
            [['unique_code'], 'unique'],
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
            'path' => 'Path',
            'unique_code' => 'Unique Code',
            'title' => 'Title',
            'status' => 'Status',
            'source_url' => 'Source Url',
            'width' => 'Width',
            'height' => 'Height',
            'size' => 'Size',
            'extension' => 'Extension',
            'collect_time' => 'Collect Time',
        ];
    }
    
    public function fields(){
        $fields = [
            'sid',
            'unique'=>'unique_code',
            'width',
            'height',
            'extension',
        ];
        return $fields;
    }
    
    public function getSid(){
    	return Utility::sid($this->id);
    }
}
