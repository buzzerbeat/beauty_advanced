<?php

namespace beauty\models;

use Yii;

/**
 * This is the model class for table "tag_rel".
 *
 * @property integer $id
 * @property integer $tag_id
 * @property integer $rel_id
 * @property integer $rank
 */
class TagRel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tag_rel';
    }
    
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
            [['tag_id', 'rel_id'], 'required'],
            [['tag_id', 'rel_id', 'rank'], 'integer'],
            ['tag_id', 'unique', 'targetAttribute' => ['tag_id', 'rel_id']],
            ['tag_id', 'compare', 'compareAttribute' => 'rel_id', 'operator' => '!==']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tag_id' => 'Tag ID',
            'rel_id' => 'Rel ID',
            'rank' => 'Rank',
        ];
    }
    
    public function getTag(){
        return $this->hasOne(Tag::className(), ['id'=>'rel_id']);
    }
    
    public static function updateRelRank($tagId){
    	$rels = TagRel::find()->where(['tag_id'=>$tagId])->orderBy('rank asc')->all();
    	foreach($rels as $index=>$rel){
    	    $rel->rank = ($index+1);
    	    $rel->save();
    	}
    }
}
