<?php

namespace beauty\models;

use Yii;
use common\components\Utility;

/**
 * This is the model class for table "album".
 *
 * @property integer $id
 * @property string $album_name
 * @property string $album_desc
 * @property string $category
 * @property integer $type
 * @property string $cover_img
 * @property integer $status
 * @property integer $is_review
 * @property integer $pub_time
 * @property integer $collect_time
 * @property integer $last_update_time
 * @property string $source_url
 * @property integer $collect_history_id
 * @property integer $is_checked
 * @property integer $rank
 */
class Album extends \yii\db\ActiveRecord
{
    
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    const STATUS_DELETE = 99;

    const STATUS_MAP = [
        self::STATUS_INACTIVE => "不可用",
        self::STATUS_ACTIVE => "可用",
        self::STATUS_DELETE => "已删除",
    ];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'album';
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
            [['album_name', 'category', 'type', 'pub_time', 'collect_time', 'last_update_time', 'source_url', 'collect_history_id', 'is_checked', 'rank'], 'required'],
            [['album_desc'], 'string'],
            [['type', 'status', 'is_review', 'pub_time', 'collect_time', 'last_update_time', 'collect_history_id', 'is_checked', 'rank'], 'integer'],
            [['album_name', 'cover_img', 'source_url'], 'string', 'max' => 1024],
            [['category'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'album_name' => 'Album Name',
            'album_desc' => 'Album Desc',
            'category' => 'Category',
            'type' => 'Type',
            'cover_img' => 'Cover Img',
            'status' => 'Status',
            'is_review' => 'Is Review',
            'pub_time' => 'Pub Time',
            'collect_time' => 'Collect Time',
            'last_update_time' => 'Last Update Time',
            'source_url' => 'Source Url',
            'collect_history_id' => 'Collect History ID',
            'is_checked' => 'Is Checked',
            'rank' => 'Rank',
        ];
    }
    
    public function extraFields()
    {
        $fields = [
            'images', 'randomImage', 'relations', 'tag'
        ];
        return $fields;
    }
    
    public function fields(){
        $fields = [
            'sid',
            'album_name',
            'album_desc',
            'category',
            'cover_img',
            'is_review',
            'imageNum'
        ];
        return $fields;
    }
    
    public function getImageNum(){
        return $this->hasMany(Image::className(), ['album_id' => 'id'])->count();
    }
    
    public function getSid(){
    	return Utility::sid($this->id);
    }
    
    public function getImages(){
    	return $this->hasMany(Image::className(), ['album_id' => 'id']);
    }
    
    public function getTagRelation(){
        return $this->hasMany(AlbumTagRelation::className(), ['album_id'=>'id']);
    }
    
    public function getRelations($total = 40) {
        $tags = $this->tag;
        $ret = [];
        $enough = false;
        shuffle($tags);
        foreach($tags as $tag){
            if($enough){
                break;
            }
            $albumPrev = Album::find()
            ->select('album.id')
            ->limit(ceil($total/2))
            ->joinWith('tagRelation')->where('album_tag_relation.tag_id = ' . $tag->id . ' and pub_time <' . $this->pub_time)
            ->orderBy('pub_time desc')
            ->asArray()
            ->all();
            $albumNext = Album::find()
            ->select('album.id')
            ->limit(ceil($total/2))
            ->joinWith('tagRelation')->where('album_tag_relation.tag_id = ' . $tag->id . ' and pub_time >' . $this->pub_time)
            ->orderBy('pub_time asc')
            ->asArray()
            ->all();
            $albums = array_merge($albumPrev, $albumNext);
            if(!empty($albums)){
                foreach($albums as $album){
                    $ret[] = Utility::sid($album['id']);
                    if(count($ret) == $total){
                        $enough = true;
                        break;
                    }
                }
            }
        }
        shuffle($ret);
    
        return $ret;
    }
    
    public function getTag()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])
        ->viaTable('album_tag_relation', ['album_id' => 'id']);
    }
    
    public function getRandomImage() {
        $cache = yii::$app->cache;
        $key = "randomImg_" . $this->id;
        $data = $cache->get($key);
        if ($data === false) {
            $images = $this->images;
            if (count($images)) {
                $data = $images[array_rand($images)];
            } else {
                $data = null;
            }
            $cache->set($key, $data, 300);
        }
        return $data;
    }
}