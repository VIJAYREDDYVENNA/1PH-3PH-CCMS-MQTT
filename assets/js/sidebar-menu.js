
function addOffcanvasClasses() {
  document.getElementById("offcanvasNavbar").classList.add("offcanvas", "offcanvas-start");
  var navbarToggler = document.querySelector(".navbar-toggler");
  navbarToggler.style.display = "block";

  var navbarClose = document.getElementById("sidebar-close");
  navbarClose.style.display = "block";
}
function removeOffcanvasClasses() {
  document.getElementById("offcanvasNavbar").classList.remove("offcanvas", "offcanvas-start", "show");
  var navbarToggler = document.querySelector(".navbar-toggler");
  navbarToggler.style.display = "none";
  var navbarClose = document.getElementById("sidebar-close");
  navbarClose.style.display = "none";
  var elementToRemove = document.querySelector('.offcanvas-backdrop');
  if (elementToRemove) {
    elementToRemove.parentNode.removeChild(elementToRemove);
  } 
}
function checkTabletSize(mediaQuery) {
  if (mediaQuery.matches) {
    addOffcanvasClasses();
  } else {
    removeOffcanvasClasses();
  }
}
const tabletMediaQuery = window.matchMedia("(max-width: 1200px)");
checkTabletSize(tabletMediaQuery);
tabletMediaQuery.addListener(checkTabletSize);


/*$(document).ready(function () {*/
const path = window.location.pathname.split("/").pop();
 /* if (path === 'acc_users_device_list.php') {
    path="acc_users_of_account.php";
  }*/
url_path=path;
const target = $('a[href="' + path + '"]');
target.addClass('active');
target.removeClass('link-body-emphasis');

$('.active').closest('.collapse').addClass('show');
$('.collapse .active').closest('.collapse').prev('.nav-link').addClass('border bg-body-secondary');
$('.collapse .active').closest('.collapse').prev('.nav-link').removeClass('collapsed');
$('.collapse .active').closest('.collapse').prev('.nav-link').attr('aria-expanded', 'true');


/*$(".nav-pills >li > a").click(function (e) {
 // $(".leftside-navigation ul ul").slideUp(), $(this).next().is(":visible") || $(this).next().slideDown(), e.stopPropagation();

 // $('.active').closest('.collapse').addClass('show');
});*/



/*});*/