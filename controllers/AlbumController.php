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
use beauty\models\ImageEvaluate;
use beauty\models\ConfigInfo;

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
        $tagId = \yii::$app->request->get('tagId');
        $query =  Album::find();
        $where = ['status' => [Album::STATUS_ACTIVE, Album::STATUS_INACTIVE]];
        
        if(!empty($tagId)){
            $where['album_tag_relation.tag_id'] = $tagId;
            $query = $query->joinWith('tagRelation');
        }
        else {
            $showBeauty = ConfigInfo::getIsShowBeauty();
            if ($showBeauty > 1) {
                $where["type"] = 1;
            }
            elseif ($showBeauty == 1) {
                $where["type"] = 1;
                $where["is_review"] = 1;
            }
            else {
                $where["is_review"] = 1;
            }
        }
        return new ActiveDataProvider([
            'query' => $query->where($where)->orderBy('rank desc')
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

    public function actionView($id)
    {
        return Album::findOne($id);
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
        $evaluateForm = new ImageEvaluateForm();
        if ($evaluateForm->load(Yii::$app->getRequest()->post(), '') && $evaluateForm->evaluate(ImageEvaluate::EVALUATE_DIG)) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode(",", $evaluateForm->getFirstErrors())];
    }
    
    public function actionBuryImage(){
        $evaluateForm = new ImageEvaluateForm();
        if ($evaluateForm->load(Yii::$app->getRequest()->post(), '') && $evaluateForm->evaluate(ImageEvaluate::EVALUATE_BURY)) {
            return ["status"=>0, "message"=>""];
        }
        return ["status"=>1, "message"=>implode(",", $evaluateForm->getFirstErrors())];
    }
}
