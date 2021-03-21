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
              <img src="/img/Background_blu_01.png" width="100%"/>
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
                      <?= Yii::t('ui', 'I am a Health care professional');?>
                    </div>
                    <div class="card-body" style="min-height: 300px;">
                        <form method="post" action="/site/login" id="form-operator" class="needs-validation" novalidate>
                              <div class="form-group">
                                <label for="exampleInputEmail1"><?= Yii::t('ui', 'Hospital code');?>*</label>
                                <input type="text" class="form-control" name="hospital-code" id="hospital-code" required placeholder="<?= Yii::t('ui', 'Enter hospital code');?>">
                                <div class="invalid-feedback"><?= Yii::t('ui', 'Please fill the Hospital code');?></div>
                              </div>
                              <div class="form-group">
                                <label for="exampleInputPassword1"><?= Yii::t('ui', 'Password');?>*</label>
                                <input type="password" class="form-control" name="password" id="password" required placeholder="<?= Yii::t('ui', 'Enter your password');?>">
                                <div class="invalid-feedback"><?= Yii::t('ui', 'Please fill the Password');?></div>
                              </div>
                              <input type="hidden" name="type" value="operator">
                              <button class="btn btn-primary login operator"><?= Yii::t('ui', 'Login');?></button>
                        </form>
                    </div>
                  </div>
                </div>
                <div class="col-lg-6">
                <div class="card spacer-mobile">
                  <div class="card-header">
                    <?= Yii::t('ui', 'I am a Patient');?>
                  </div>
                  <div class="card-body" style="min-height: 300px;">
                      <form method="post" action="/site/login" id="form-user" class="needs-validation" novalidate>
                        <div class="form-group">
                          <label for="exampleInputEmail1"><?= Yii::t('ui', 'Hospital code');?>*</label>
                          <input type="text" class="form-control" name="hospital-code" required id="hospita-code-user" placeholder="<?= Yii::t('ui', 'Enter hospital code');?>">
                          <div class="invalid-feedback"><?= Yii::t('ui', 'Please fill the Hospital code');?></div>
                        </div>
                        <div class="form-group">
                          <label for="exampleInputPassword1"><?= Yii::t('ui', 'Full Name');?></label>
                          <input type="text" class="form-control" name="full-name"  id="full-name" placeholder="<?= Yii::t('ui', 'Enter your name');?>">
                        </div>
                        <div class="form-group">
                          <label for="exampleInputPassword1"><?= Yii::t('ui', 'Sample code');?>*</label>
                          <input type="text" class="form-control" name="sample-code" required id="sample-code-user" placeholder="<?= Yii::t('ui', 'Enter Sample Code');?>">
                          <div class="invalid-feedback"><?= Yii::t('ui', 'Please fill the Sample code');?></div>
                        </div>
                        <input type="hidden" name="type" value="user">
                        <button class="btn btn-primary login user"><?= Yii::t('ui', 'Login');?></button>
                      </form>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12" style="font-size: 14px;margin-top: 15px;">
                This Application implements the <a href="https://www.covid19hg.org/" target="_blank">COVID-19 HG Initiative</a> questionnaire to collect data from hospitalised patients.
                <br><br>
                This is a DEMO platform, you can access as a Health Care Professional using "Demo" as Hospital code and "qwerty" as password.
                <br><br>
                The tool is available free of charge to bona-fide researchers.
                <br><br>
                Request the tool or seek assistance at <a href="mailto:info@allelica.com">info@allelica.com</a>.
              </div>
            </div>
            </div>

        </div>

    </div>
</div>
