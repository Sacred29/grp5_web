<!-- Remove 'active' class, this is just to show in Codepen thumbnail
<link rel="stylesheet" type="text/css" href="css/ino-alert.css">
<script defer src="js/info-alert.js"></script>
<div class="toast active">
  
  <div class="toast-content">
    <i class="fas fa-solid fa-check check"></i>

    <div class="message">
      <span class="text text-1">Success</span>
      <span class="text text-2">Your changes has been saved</span>
    </div>
  </div>
  <i class="fa-solid fa-xmark close"></i>
</div> -->
<link rel="stylesheet" type="text/css" href="css/ino-alert.css">

  
  <div class="alert alert-success alert-dismissable fade in">
    <button type="button" data-dismiss="alert" aria-label="close" class="close"><span aria-hidden="true">×</span></button>
    <?php
    echo $message;
    ?>
    
  </div>

