<?php

namespace beauty\models;

use Yii;

/**
 * This is the model class for table "image_evaluate".
 *
 * @property integer $id
 * @property integer $image_id
 * @property integer $user_id
 * @property integer $evaluate
 * @property integer $time
 */
class ImageEvaluate extends \yii\db\ActiveRecord
{
    
    const EVALUATE_DIG = 0;
    const EVALUATE_BURY = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'image_evaluate';
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
            [['image_id', 'user_id', 'evaluate', 'time'], 'required'],
            [['image_id', 'user_id', 'evaluate', 'time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'image_id' => 'Image ID',
            'user_id' => 'User ID',
            'evaluate' => 'Evaluate',
            'time' => 'Time',
        ];
    }
}
