<?php

/* @var $this yii\web\View */

$this->title = 'Patients list';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-index">


    <div class="body-content">

        <div class="row">
            <div class="col-lg-12">
              <h1><?= Yii::t('ui', 'Patient list');?></h1>
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col"><?= Yii::t('ui', 'Sample code');?>Sample code</th>
                    <th scope="col"><?= Yii::t('ui', 'Full Name');?>Full Name</th>
                    <th scope="col"><?= Yii::t('ui', 'Patient profile');?>Patient profile</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
foreach($list as $user) {
                   ?>
                  <tr>
                    <th scope="row"><?= $user->id;?></th>
                    <td><?= $user->joinUserCollector->code;?></td>
                    <td><?= $user->full_name;?></td>
                    <td><button class="btn btn-secondary user-profile" id="<?= $user->id;?>">Check</button></td>
                  </tr>
                <?php }?>
                </tbody>
              </table>


            </div>

          </div>
</div>
