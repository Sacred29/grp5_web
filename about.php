<?php
    include "inc/head.inc.php";
?>
<body>
<?php
        include "inc/nav.inc.php";
?>

<style>
      body {
    font: 20px Montserrat, sans-serif;
    line-height: 1.8;
    color: #f5f6f7;
  }
  p {font-size: 16px;}
  .margin {margin-bottom: 45px;}
  .bg-1 { 
    background-color: #1abc9c; /* Green */
    color: #ffffff;
  }
  .bg-2 { 
    background-color: #474e5d; /* Dark Blue */
    color: #ffffff;
  }
  .bg-3 { 
    background-color: #ffffff; /* White */
    color: #555555;
  }
  .bg-4 { 
    background-color: #2f2f2f; /* Black Gray */
    color: #fff;
  }
  .container-fluid {
    padding-top: 70px;
    padding-bottom: 70px;
  }

  </style>

<div class="container-fluid bg-1 text-center" style="background-image: url('images/bg.jpg'); background-size: cover; background-position: center; height: 300px;">
</div>

<!-- Second Container -->
<div class="container-fluid bg-2 text-center">
  <h3 class="margin">Who Are We?</h3>
  <p style="max-width: 300px; overflow-wrap: break-word; margin: 0 auto; text-align: center; color:beige;">Bookshelf Boutique is an online book procurement website for book lovers. With the closure of Book Depository, we bridge the gap between book lovers and affordable books.</p>   
</div>

<div class="container-fluid bg-1 text-center" style="background-image: url('images/bg.jpg'); background-size: cover; background-position: center; height:300px"></div>

<?php
    include "inc/footer.inc.php"
?>

</body>
</html>
