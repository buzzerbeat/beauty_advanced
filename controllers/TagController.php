<?php

namespace beauty\controllers;

use Yii;
use yii\rest\Controller;
use yii\data\ActiveDataProvider;
use beauty\models\Tag;
use common\components\Utility;
/**
 * CategoryController implements the CRUD actions for AlbumTag model.
 */
class TagController extends Controller
{
    public $modelClass = 'app\models\Tag';
    public function actionIndex()
    {
        return new ActiveDataProvider([
            'query' => Tag::find()
        ]);
    }
    
    public function actionView($sid){
        return Tag::findOne(Utility::id($sid));
    }

}
