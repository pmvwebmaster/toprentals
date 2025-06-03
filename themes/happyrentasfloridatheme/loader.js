// Loader for site and admin - hides only after all AJAX (including translation) is done
(function(){
  function hideLoader(id) {
    var el = document.getElementById(id);
    if (el) el.classList.add('hide');
    setTimeout(function(){ if (el) el.style.display = 'none'; }, 500);
  }
  function showLoader(id) {
    var el = document.getElementById(id);
    if (el) el.classList.remove('hide');
    if (el) el.style.display = 'flex';
  }
  // Site loader
  if (!document.getElementById('site-loader-overlay')) {
    var loader = document.createElement('div');
    loader.id = 'site-loader-overlay';
    loader.innerHTML = '<img class="loader-logo" src="/wp-content/themes/happyrentasfloridatheme/assets/images/logo-02-transp.png-455x236.png" alt="Logo"><div class="loader-text">Carregando...</div>';
    document.body.appendChild(loader);
  }
  // Admin loader
  if (window.location.pathname.indexOf('/wp-admin') !== -1 && !document.getElementById('admin-loader-overlay')) {
    var loader = document.createElement('div');
    loader.id = 'admin-loader-overlay';
    loader.innerHTML = '<img class="loader-logo" src="/wp-content/themes/happyrentasfloridatheme/assets/images/logo-02-transp.png-455x236.png" alt="Logo"><div class="loader-text">Carregando admin...</div>';
    document.body.appendChild(loader);
  }
  // jQuery/ajax hooks
  function allAjaxDone() {
    // WPML/TranslatePress/Google Translate etc. hooks
    if (window.TranslateWidget && typeof window.TranslateWidget.isTranslating === 'function') {
      if (window.TranslateWidget.isTranslating()) return false;
    }
    if (window.tp && window.tp.isTranslating && window.tp.isTranslating()) return false;
    return true;
  }
  function tryHideAllLoaders() {
    if (allAjaxDone()) {
      hideLoader('site-loader-overlay');
      hideLoader('admin-loader-overlay');
    }
  }
  // jQuery AJAX global
  if (window.jQuery) {
    jQuery(document).ajaxStop(function(){
      setTimeout(tryHideAllLoaders, 1000);
    });
    jQuery(document).ajaxStart(function(){
      showLoader('site-loader-overlay');
      showLoader('admin-loader-overlay');
    });
  }
  // Fallback: hide after window load + translation
  window.addEventListener('load', function(){
    setTimeout(tryHideAllLoaders, 1000);
  });
  // For translation plugins that fire custom events
  document.addEventListener('translation-complete', function(){
    setTimeout(tryHideAllLoaders, 1000);
  });
})();
