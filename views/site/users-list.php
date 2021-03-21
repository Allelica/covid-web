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
              <div id="toolbar">
                [+] <?= Yii::t('ui', 'Add New Patient');?>
                <div id="search">
                  <input type="text" id="search-field" placeholder="<?= Yii::t('ui', 'Search');?>"/>
                </div>
              </div>
              <table class="table table-striped user-list">
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
