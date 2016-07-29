<?php

namespace beauty\models;

use Yii;
use common\components\Utility;
use beauty\models\TagRel;
/**
 * This is the model class for table "tag".
 *
 * @property integer $id
 * @property string $name
 * @property string $ename
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag';
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
            [['name', 'ename'], 'required'],
            [['name', 'ename'], 'string', 'max' => 60],
            [['name'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'ename' => 'Ename',
        ];
    }
    
    public function fields(){
        $fields = [
            'sid',
            'name',
        ];
        return $fields;
    }
    
    public function getSid(){
    	return Utility::sid($this->id);
    }
    
    public function extraFields()
    {
        return ['subTags'];
    }
    
    public function getTags(){
        return $this->hasMany(TagRel::className(), ['tag_id'=>'id'])->orderBy('rank desc');
    }
    
    public function getSubTags(){
        $tags = $this->tags;
        $ret = [];
        foreach($tags as $tag){
            $ar = Tag::findOne($tag->rel_id);
            if(empty($ar)){
            	continue;
            }
            $ret[] = ['sid'=>Utility::sid($tag->rel_id), 'name'=>$ar->name];
        }
         
        return $ret;
    }
}
