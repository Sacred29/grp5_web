activateMenu();
document.addEventListener("DOMContentLoaded", function ()
{
// Code to be executed when the DOM is ready (i.e. the document is
// fully loaded):
registerEventListeners(); // You need to write this function...
});

function registerEventListeners()
{

    var logos = document.getElementsByClassName("img-thumbnail");
    var modal = document.getElementById("imgModal");
    var modalImg = document.getElementById("img01");

    for (let i = 0; i < logos.length; i++) {
        logos[i].onclick = function() {
            const imgSrc = logos[i].src.replace("small", "large");
            modal.style.display = "block";
            modalImg.src = imgSrc;
        }
      }

    imgModal.onclick = function() {
        modal.style.display = "none";
    }
    
}

function activateMenu() {
    const navLinks = document.querySelectorAll('nav a');
    navLinks.forEach(link => {
        if (link.href === location.href) {
            link.classList.add('active');
        }
    })
}

$('.owl-carousel').owlCarousel({
    loop:true,
    margin:30,
    responsiveClass:true,
    responsive:{
        0:{
            items:1,
            nav:true
        },
        500:{
            items:2,
            nav:false
        },
        800:{
            items:3,
            nav:false
        },
        1000:{
            items:3,
            nav:true,
            loop:false
        },
        1200:{
            items:4,
            nav:true,
            loop:false
        },
        1500:{
            items:4,
            nav:true,
            loop:false
        }
    }
})

















// function openImgPopup() {
//     const arrayNew = new Array(document.getElementsByClassName("img-thumbnail"));
//     console.log(arrayNew);
//     arrayNew.forEach(element => {
//         document.getElementById("img-thumbnail").addEventListener("click", newFunction);
//     });

// }

// function newFunction() {
//     var selected = document.getElementById("img-popup").classList.toggle("active");
//     console.log(selected);
// }
