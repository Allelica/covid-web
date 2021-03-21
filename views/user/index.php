<?php

/* @var $this yii\web\View */

$this->title = 'COVID-19 hg';
if(!isset($error)) {
  $error = "";
}
if(isset($permission)) {
  $error =  Yii::t('ui', 'Login to use this function');
}
?>
<div class="site-index">


    <div class="body-content">

        <div class="row">
            <div class="col-lg-6">
              <img src="/img/Background_blu_01.png"  height="500"/>
             </div>
             <div class="col-lg-6">
               <center><h1 style="margin:auto;"><?= Yii::t('ui', 'Data collection portal');?></h1></center>
                <div class="row" style="margin-top:35px;">
                  <div class="col-lg-12">
                    <?= $error;?>
                  </div>
                  <div class="col-lg-6">
                  <div class="card">
                    <div class="card-header">
                      <?= Yii::t('ui', 'Health care professional');?>
                    </div>
                    <div class="card-body" style="min-height: 300px;">
                        <form method="post" action="/site/login" id="form-operator">
                              <div class="form-group">
                                <label for="exampleInputEmail1"><?= Yii::t('ui', 'Hospital code');?></label>
                                <input type="text" class="form-control" name="hospital-code" id="hospital-code"  placeholder="<?= Yii::t('ui', 'Enter hospital code');?>">
                              </div>
                              <div class="form-group">
                                <label for="exampleInputPassword1"><?= Yii::t('ui', 'Password');?></label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="<?= Yii::t('ui', 'Enter your password');?>">
                              </div>
                              <input type="hidden" name="type" value="operator">
                              <button class="btn btn-primary login operator"><?= Yii::t('ui', 'Login');?></button>
                        </form>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6">
                <div class="card">
                  <div class="card-header">
                    <?= Yii::t('ui', 'Patient');?>
                  </div>
                  <div class="card-body" style="min-height: 300px;">
                      <form method="post" action="/site/login" id="form-user">
                        <div class="form-group">
                          <label for="exampleInputEmail1"><?= Yii::t('ui', 'Hospital code');?></label>
                          <input type="text" class="form-control" name="hospital-code" id="hospita-code-user" placeholder="<?= Yii::t('ui', 'Enter hospital code');?>">
                        </div>
                        <div class="form-group">
                          <label for="exampleInputPassword1"><?= Yii::t('ui', 'Full Name');?></label>
                          <input type="text" class="form-control" name="full-name" id="full-name" placeholder="<?= Yii::t('ui', 'Enter your name');?>">
                        </div>
                        <div class="form-group">
                          <label for="exampleInputPassword1"><?= Yii::t('ui', 'Sample code');?></label>
                          <input type="text" class="form-control" name="sample-code" id="sample-code-user" placeholder="<?= Yii::t('ui', 'Enter Sample Code');?>">
                        </div>
                        <input type="hidden" name="type" value="user">
                        <button class="btn btn-primary login user"><?= Yii::t('ui', 'Login');?></button>
                      </form>
                  </div>
                </div>
              </div>
            </div>

            </div>

        </div>

    </div>
</div>
