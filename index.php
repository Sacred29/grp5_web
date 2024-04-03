<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<?php
    include "inc/head.inc.php";
   
?>
<body>
<?php 
    if(isset($_SESSION["user_privilege"])) {
        "<strong>Well done!</strong> You successfully read this important alert message.";
        $message =  "you are logged in as " . $_SESSION["user_privilege"];
        include "inc/success-alert.inc.php";
    } 
?>
        <!-- Collapsible Top Navbar -->
        <?php
            include "inc/nav.inc.php";
        ?>
        <?php
            include "inc/header.inc.php";
        ?>
        
        <main class="container">
        <section id="gallery-carousell">
            <div class="row">
                    <div class="col">
                        <div class="section-heading">
                            <!-- <span>Featured Products</span> -->
                            <h2>Featured Products</h2>
                        </div>
                </div> 
            </div> 
            <div class="row">
                    <div class="col">
                        <?php include "inc/gallery.inc.php";?>  
                </div> 
            </div>   
        </section>
        <section class="featured-places">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="section-heading">
                            <!-- <span>Featured Products</span> -->
                            <h2>New Releases</h2>
                        </div>
                    </div> 
                </div> 
                <div class="row">
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <img src="images/tabby_small.jpg" alt="">
                            </div>
                            <div class="down-content">
                                <h4>Tabby Cats</h4>

                                <span><del><sup>$</sup>1999.00 </del> <strong><sup>$</sup>1779.00</strong></span>

                                <p>This is an image about tabby cats.</p>

                                <div class="text-button">
                                    <a href="product-details.html">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <img src="images/chihuahua_small.jpg" alt="Chihuahua">
                            </div>
                            <div class="down-content">
                                <h4>Chihuahua</h4>

                                <span><del><sup>$</sup>999.00 </del> <strong><sup>$</sup>779.00</strong></span>

                                <p>This is a book about Chihuahuas.</p>

                                <div class="text-button">
                                    <a href="product-details.html">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <img src="images/tabby_small.jpg" alt="">
                            </div>
                            <div class="down-content">
                                <h4>Tabby Cats</h4>

                                <span><del><sup>$</sup>1999.00 </del> <strong><sup>$</sup>1779.00</strong></span>

                                <p>This is an image about tabby cats.</p>

                                <div class="text-button">
                                    <a href="product-details.html">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <img src="images/tabby_small.jpg" alt="">
                            </div>
                            <div class="down-content">
                                <h4>Tabby Cats</h4>

                                <span><del><sup>$</sup>99.00 </del> <strong><sup>$</sup>79.00</strong></span>

                                <p>This is a book about tabbies.</p>

                                <div class="text-button">
                                    <a href="product-details.html">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <img src="images/chihuahua_small.jpg" alt="">
                            </div>
                            <div class="down-content">
                                <h4>Chihuahua.</h4>

                                <span><del><sup>$</sup>999.00 </del> <strong><sup>$</sup>779.00</strong></span>

                                <p>This is a book about Chihuahuas</p>

                                <div class="text-button">
                                    <a href="product-details.html">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <div class="featured-item">
                            <div class="thumb">
                                <img src="images/tabby_small.jpg" alt="">
                            </div>
                            <div class="down-content">
                                <h4>Tabby Cats</h4>

                                <span><del><sup>$</sup>1999.00 </del> <strong><sup>$</sup>1779.00</strong></span>

                                <p>This is an image about tabby cats.</p>

                                <div class="text-button">
                                    <a href="product-details.html">View More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    
        </main>
        <?php
            include "inc/footer.inc.php";
        ?>
   
        
        <!--Modal-->
        <div id="imgModal" class="imgModal">
            <span class="close">&times;</span>
            <img class="modal-content" id="img01">
        </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js" type="text/javascript"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>x
    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/datepicker.js"></script>
    <script src="js/plugins.js"></script>
</body>
</html>