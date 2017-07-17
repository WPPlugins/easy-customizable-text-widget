jQuery(document).ready(function($){

  
  
  $('#wpbody .wrap').prepend('<div class="wps-ectw-notice"><i class="dashicons dashicons-smiley"></i> <h2>Join our Premium List for FREE!</h2><a href="admin.php?page=wps-ectw-premium" class="button button-primary wps-ectw-subscribe-btn">Subscribe</a><hr/><p>Thanks for using our Easy Customizable Text Widget plugin! If you wish to receive our <strong>premium updates for free</strong> subscribe to our newsletter list as a sign of support!</p> <a href="#" class="wps-ectw-close"><i class="dashicons dashicons-no"></i></a></div>');
  
  
  $(document).on('click', '.wps-ectw-close', function(e){
    e.preventDefault();
    
    if (confirm('Are you sure you don\'t want our premium addons?')) {
      
      $.post(ajaxurl, {action:'close_subscribe_notice'}, function(response){
        $('.wps-ectw-notice').fadeOut(500);
      });
      
    } else {
      return false;
    }
    
  });
  
});