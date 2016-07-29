<?php
/**
 * Created by PhpStorm.
 * User: cx
 * Date: 2016/7/13
 * Time: 18:05
 */

namespace beauty\controllers;

use common\components\Utility;
//use common\models\Image;
use beauty\models\Album;
use beauty\models\Image;
use beauty\models\AlbumLikeForm;
use beauty\models\AlbumFavForm;
use beauty\models\ImageEvaluateForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\Response;

class AlbumController extends Controller
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'only' => ['fav',  'like', 'fav-list', 'like-list', 'dig-image', 'bury-image'],
        ];

        return $behaviors;
    }
    public function actionIndex()
    {
        $tagSid = \yii::$app->request->get('tagId');
        $query =  Album::find();
        $where = ['status' => [Album::STATUS_ACTIVE, Album::STATUS_INACTIVE]];
        
        if(!empty($tagSid)){
            $where['album_tag_relation.tag_id'] = Utility::id($tagSid);
            $query = $query->joinWith('tagRelation');
        }
        return new ActiveDataProvider([
            'query' => $query->where($where)->orderBy('id desc')
        ]);
    }

    public function actionFavList() {
        $user = \Yii::$app->user->identity;
        $query =  Album::find()
            ->leftJoin('album_fav', '`album_fav`.`album_id` = `album`.`id`')
            ->where([
                '`album_fav`.`user_id`' => $user->id,
            ]);
        return new ActiveDataProvider([
            'query' => $query->orderBy('`album_fav`.`time` desc')
        ]);
    }

    public function actionLikeList() {
        $user = \Yii::$app->user->identity;
        $query =  Album::find()
            ->leftJoin('album_like', '`album_like`.`album_id` = `album`.`id`')
            ->where([
                '`album_like`.`user_id`' => $user->id,
            ]);
        return new ActiveDataProvider([
            'query' => $query->orderBy('`album_like`.`time` desc')
        ]);
    }

    public function actionView($sid)
    {
        return Album::findOne(Utility::id($sid));
    }

    public function actionLike()
    {
        $likeForm = new AlbumLikeForm();
        if ($likeForm->load(Yii::$app->getRequest()->post(), '') && $likeForm->like()) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode(",", $likeForm->getFirstErrors())];
    }

    public function actionFav()
    {
        $favForm = new AlbumFavForm();
        if ($favForm->load(Yii::$app->getRequest()->post(), '') && $favForm->fav()) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode(",", $favForm->getFirstErrors())];

    }
    
    public function actionDigImage(){
        $digForm = new ImageEvaluateForm();
        if ($digForm->load(Yii::$app->getRequest()->post(), '') && $digForm->dig()) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode(",", $digForm->getFirstErrors())];
    }
    
    public function actionBuryImage(){
        $buryForm = new ImageEvaluateForm();
        if ($buryForm->load(Yii::$app->getRequest()->post(), '') && $buryForm->bury()) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode(",", $buryForm->getFirstErrors())];
    }
}
