<?php
include "./inc/head.inc.php";
echo '<link href="path_to_bootstrap_css/bootstrap.min.css" rel="stylesheet">';
session_start();
include "./inc/nav.inc.php"
?>

<main class="container">
    <h2>Account</h2>
    <?php if (isset($_SESSION['message'])) {
        echo "<script>alert('{$_SESSION['message']}');</script>";
        unset($_SESSION['message']); // Clear the message after displaying it
    }
    ?>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="accountTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link" id="namechange-tab" data-bs-toggle="tab" href="#namechange" role="tab" aria-controls="namechange" aria-selected="false">Change Name</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="password-tab" data-bs-toggle="tab" href="#password" role="tab" aria-controls="password" aria-selected="false">Change Password</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="orders-tab" data-bs-toggle="tab" href="#orders" role="tab" aria-controls="orders" aria-selected="false">Order Details</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="reviews-tab" data-bs-toggle="tab" href="#reviews" role="tab" aria-controls="reviews" aria-selected="false">Your Reviews</a>
        </li>
    </ul>
    <div id= "display_content"></div>
</div>


<!-- Include Bootstrap Bundle with Popper -->
<script src="path_to_bootstrap_js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>

$(document).ready(function(){
// When the 'user management' tab is shown
    $('a[data-bs-toggle="tab"][href="#namechange"]').on('shown.bs.tab', function (e) {
        var targetElement = document.getElementById("display_content");
        removeElement();
        fetch('./account/name_change.php')
        .then(response => response.text())
        .then(data => {
            if (targetElement) {
                targetElement.innerHTML = data;
            }
        })
        .catch(error => {
            console.error('Error fetching hello.php:', error);
        });
    });


    // When the 'password' tab is shown
    $('a[data-bs-toggle="tab"][href="#password"]').on('shown.bs.tab', function (e) {
        var targetElement = document.getElementById("display_content");
        removeElement();
         // Fetch content from hello.php using AJAX
        fetch('./account/password_change.php')
        .then(response => response.text())
        .then(data => {
            if (targetElement) {
                targetElement.innerHTML = data;
            }
        })
        .catch(error => {
            console.error('Error fetching hello.php:', error);
        });
        
       
        // You can perform any action you want here
    });

    $('a[data-bs-toggle="tab"][href="#orders"]').on('shown.bs.tab', function (e) {
        var targetElement = document.getElementById("display_content");
        removeElement();
        fetch('./account/user_orders.php')
        .then(response => response.text())
        .then(data => {
            if (targetElement) {
                targetElement.innerHTML = data;
            }
        })
        .catch(error => {
            console.error('Error fetching hello.php:', error);
        });
    });

    $('a[data-bs-toggle="tab"][href="#reviews"]').on('shown.bs.tab', function (e) {
        var targetElement = document.getElementById("display_content");
        removeElement();
        fetch('./account/user_reviews.php')
        .then(response => response.text())
        .then(data => {
            if (targetElement) {
                targetElement.innerHTML = data;
            }
        })
        .catch(error => {
            console.error('Error fetching hello.php:', error);
        });
    });
    

    //Function to remove html when switch tab
    function removeElement() {
            var element = document.getElementById("display_content");
            if (element) {
                element.innerHTML = "";
            }
        }
});
</script>



<script>
    // on load
document.addEventListener("DOMContentLoaded", (event) => {
    var targetElement = document.getElementById("display_content");
        fetch('./account/name_change.php')
        .then(response => response.text())
        .then(data => {
            if (targetElement) {
                targetElement.innerHTML = data;
            }
        })
        .catch(error => {
            console.error('Error fetching hello.php:', error);
        });
   
});
</script>
