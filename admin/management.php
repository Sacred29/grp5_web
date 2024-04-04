<?php
include "./../inc/head.inc.php";
echo '<link href="path_to_bootstrap_css/bootstrap.min.css" rel="stylesheet">';
session_start();
?>

<main class="container">
    <h2>Admin</h2>
    <?php if (isset($_SESSION['message'])) {
        echo "<script>alert('{$_SESSION['message']}');</script>";
        unset($_SESSION['message']); // Clear the message after displaying it
    }
    ?>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="accountTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="user_management-tab" data-bs-toggle="tab" href="#user_management" role="tab" aria-controls="user_management" aria-selected="true">User Management</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cart-tab" data-bs-toggle="tab" href="#cart" role="tab" aria-controls="cart" aria-selected="false">Cart Management</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="order-tab" data-bs-toggle="tab" href="#order" role="tab" aria-controls="order" aria-selected="false">Order Management</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="review-tab" data-bs-toggle="tab" href="#review" role="tab" aria-controls="review" aria-selected="false">Reviews Management</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="product-tab" data-bs-toggle="tab" href="#product" role="tab" aria-controls="product" aria-selected="false">Products Management</a>
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
$('a[data-bs-toggle="tab"][href="#user_management"]').on('shown.bs.tab', function (e) {
        var targetElement = document.getElementById("display_content");
        removeElement();
        fetch('/admin/user_management.php')
        .then(response => response.text())
        .then(data => {
            if (targetElement) {
                targetElement.innerHTML = data;
            }
        })
        .catch(error => {
            console.error('Error fetching user_management.php:', error);
        });
    });


    // When the 'cart Management' tab is shown
    $('a[data-bs-toggle="tab"][href="#cart"]').on('shown.bs.tab', function (e) {
        var targetElement = document.getElementById("display_content");
        removeElement();
         // Fetch content from hello.php using AJAX
        fetch('/admin/cart_management.php')
        .then(response => response.text())
        .then(data => {
            if (targetElement) {
                targetElement.innerHTML = data;
            }
        })
        .catch(error => {
            console.error('Error fetching cart_management.php:', error);
        });
    });

     // When the 'order Management' tab is shown
     $('a[data-bs-toggle="tab"][href="#order"]').on('shown.bs.tab', function (e) {
        var targetElement = document.getElementById("display_content");
        removeElement();
         // Fetch content from hello.php using AJAX
        fetch('/admin/order_management.php')
        .then(response => response.text())
        .then(data => {
            if (targetElement) {
                targetElement.innerHTML = data;
            }
        })
        .catch(error => {
            console.error('Error fetching order_management.php:', error);
        });
    });

    $('a[data-bs-toggle="tab"][href="#review"]').on('shown.bs.tab', function (e) {
        var targetElement = document.getElementById("display_content");
        removeElement();
        
        fetch('/admin/review_management.php')
        .then(response => response.text())
        .then(data => {
            if (targetElement) {
                targetElement.innerHTML = data;
            }
        })
        .catch(error => {
            console.error('Error fetching review_management.php:', error);
        });
    });

    $('a[data-bs-toggle="tab"][href="#product"]').on('shown.bs.tab', function (e) {
        var targetElement = document.getElementById("display_content");
        removeElement();
        
        fetch('/admin/product_management.php')
        .then(response => response.text())
        .then(data => {
            if (targetElement) {
                targetElement.innerHTML = data;
            }
        })
        .catch(error => {
            console.error('Error fetching product_management.php:', error);
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
        fetch('/admin/user_management.php')
        .then(response => response.text())
        .then(data => {
            if (targetElement) {
                targetElement.innerHTML = data;
            }
        })
        .catch(error => {
            console.error('Error fetching user_management.php:', error);
        });
   
});
</script>

