<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/site.css?<?= date("Ymdhi");?>">
    <script src="https://kit.fontawesome.com/fc5460aa97.js" crossorigin="anonymous"></script>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<header style="padding: 10px;z-index:9999;position: fixed;color:#fff;size:24px;width:100%;height: 40px;background-color: midnightblue;">
<div class="container">
<div class="row">
  <div class="col-lg-2 col-sm-5" style="text-align:left;">
    COVID-19 hg
  </div>
  <div class="col-lg-9 col-sm-2">&nbsp;</div>
  <div class="col-lg-1 col-sm-2 logout" style="text-align:right;">
    <?php
    if(!Yii::$app->user->isGuest) {?>
      <a href="/site/logout" class="logout"><?= Yii::t('ui', 'Logout');?></a>
    <?php }
    ?>
  </div>
  <div class="col-3">&nbsp;</div>
</div>
</div>
</header>
<div class="wrap">



    <div class="container">

<div class="row">
  <div class="col-lg-1 col-sm-12">&nbsp;</div>
  <div class="col-lg-10 col-sm-12">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
  </div>
  <div class="col-lg-1 col-sm-12">&nbsp;</div>
</div>
    <?= Alert::widget() ?>
    <?= $content ?>
</div>

<!--footer class="footer" style="position:absolute;height:40px;z-index:9999;bottom:2px;width:100%;padding: 15px 0;">
    <div class="container">
        <p class="pull-left">&copy; Allelica <?= date('Y') ?></p>

        <p class="pull-right">The COVID-19 Host Genetics Initiative</p>
    </div>
</footer-->

<?php $this->endBody() ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<script src="/js/main.js?<?php echo date("Ymdhis");?>"></script>

</body>
</html>
<?php $this->endPage() ?>
