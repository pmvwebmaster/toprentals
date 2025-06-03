<?php
/*
Plugin Name: Cookie Consent Banner
Description: Displays a simple cookie consent banner at the bottom of the site.
Version: 1.0
Author: Pablo Viana
*/

// Add styles and banner HTML to the footer
function ccb_add_cookie_banner() {
    ?>
    <style>
    #cookie-notice {
      position: fixed;
      bottom: 0;
      width: 100%;
      background: #222;
      color: #fff;
      padding: 15px;
      text-align: center;
      z-index: 9999;
    }
    #cookie-notice button {
      margin-left: 10px;
      padding: 5px 15px;
      background: #f1c40f;
      color: #000;
      border: none;
      cursor: pointer;
      border-radius: 4px;
    }
    </style>

    <div id="cookie-notice">
      We use cookies to improve your experience. By continuing, you agree to our
      <a href="/privacy-policy" style="color: #f1c40f;">Privacy Policy</a>.
      <button onclick="acceptCookies()">Accept</button>
    </div>

    <script>
    function acceptCookies() {
      document.getElementById('cookie-notice').style.display = 'none';
      localStorage.setItem('cookiesAccepted', 'true');
    }
    if (localStorage.getItem('cookiesAccepted') === 'true') {
      document.addEventListener('DOMContentLoaded', function () {
        const notice = document.getElementById('cookie-notice');
        if (notice) notice.style.display = 'none';
      });
    }
    </script>
    <?php
}
add_action('wp_footer', 'ccb_add_cookie_banner');
