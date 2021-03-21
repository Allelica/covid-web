<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Lang;

abstract class CommonController extends Controller
{
    public function beforeAction($action) {

      $lang = Yii::$app->request->get('lang');
      if(is_null($lang)) {
        $lang = Yii::$app->params['defaultLanguage'];
      }
      $lang = Lang::findOne($lang);
      
      if(is_null($lang))
        throw new \yii\web\HttpException(404, 'The requested Language could not be found.');

      Yii::$app->params['language'] = $lang->id;
      return true;
  }
}
