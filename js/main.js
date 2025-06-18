(function ($) {
  "use strict";

  // Sticky Navbar
  $(window).scroll(function () {
    if ($(this).scrollTop() > 40) {
      $(".navbar").addClass("sticky-top");
    } else {
      $(".navbar").removeClass("sticky-top");
    }
  });

  // Dropdown on mouse hover
  $(document).ready(function () {
    function toggleNavbarMethod() {
      if ($(window).width() > 992) {
        $(".navbar .dropdown")
          .on("mouseover", function () {
            $(".dropdown-toggle", this).trigger("click");
          })
          .on("mouseout", function () {
            $(".dropdown-toggle", this).trigger("click").blur();
          });
      } else {
        $(".navbar .dropdown").off("mouseover").off("mouseout");
      }
    }
    toggleNavbarMethod();
    $(window).resize(toggleNavbarMethod);
  });

  // Back to top button
  $(window).scroll(function () {
    if ($(this).scrollTop() > 100) {
      $(".back-to-top").fadeIn("slow");
    } else {
      $(".back-to-top").fadeOut("slow");
    }
  });
  $(".back-to-top").click(function () {
    $("html, body").animate({ scrollTop: 0 }, 1500, "easeInOutExpo");
    return false;
  });

  // Testimonials carousel
  $(".testimonial-carousel").owlCarousel({
    autoplay: true,
    smartSpeed: 1000,
    items: 1,
    dots: false,
    loop: true,
    nav: true,
    navText: [
      '<i class="bi bi-arrow-left"></i>',
      '<i class="bi bi-arrow-right"></i>',
    ],
  });

  setTimeout(function () {
    var successMessage = document.querySelector(".success-message");
    var errorMessage = document.querySelector(".error-message");
    if (successMessage) successMessage.style.display = "none";
    if (errorMessage) errorMessage.style.display = "none";
  }, 3000);

  // JavaScript for the user dropdown
  const userDropdown = document.querySelector(".user-dropdown");
  const userProfile = userDropdown.querySelector(".user-profile");
  const dropdownContent = userDropdown.querySelector(".dropdown-content");

  userProfile.addEventListener("click", function (event) {
    event.preventDefault(); // Prevent default link behavior
    dropdownContent.style.display =
      dropdownContent.style.display === "block" ? "none" : "block";
  });

  window.addEventListener("click", function (event) {
    if (!userDropdown.contains(event.target)) {
      dropdownContent.style.display = "none";
    }
  });

  // admin table start
 // Dynamically update the values in the HTML
 document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("totalVisitors").innerText =
    "<?php echo $totalVisitors; ?>";
  document.getElementById("totalOwners").innerText =
    "<?php echo $totalOwners; ?>";
  document.getElementById("totalSecurity").innerText =
    "<?php echo $totalSecurity; ?>";
});
})(jQuery);
