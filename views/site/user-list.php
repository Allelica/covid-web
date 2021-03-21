<?php

/* @var $this yii\web\View */

$this->title = 'Patients list';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-index">


    <div class="body-content">
        [+] Add New User
        <div class="row">
            <div class="col-lg-12">
              <h1><?= Yii::t('ui', 'Patient list');?></h1>
              <table class="table table-striped display nowrap">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col"><?= Yii::t('ui', 'Sample code');?></th>
                    <th scope="col"><?= Yii::t('ui', 'Full Name');?></th>
                    <th scope="col"><?= Yii::t('ui', 'Patient profile');?></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    foreach($user_list as $user) {
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
