<?php
use yii\helpers\Html;
use backend\assets\AppAsset;

$this->title = $code;

AppAsset::register($this);
?>
<style type="text/css">
  .col-middle {
      margin-top: 12%;
  }
</style>
<div class="container body">
  <div class="main_container">
    <div class="col-md-12">
      <div class="col-middle">
        <div class="text-center text-center">
          <h1 class="error-number"><?= $code ?></h1>
          <h2><?= $message ?></h2>
        </div>
      </div>
    </div>
  </div>
</div>
