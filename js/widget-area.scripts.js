jQuery(document).ready(function($){
  
  
  $('div[id*="wps_gfwidget"]', '#widget-list').each(function(i, item){
    $(item).addClass('wps-gfw-widget-highlight');
  });  
  
  $('div[id*="wps_gfwidget"]', '#widgets-right').each(function(i, item){
    $(item).addClass('wps-gfw-widget-highlight');
  });
  
  /*
  Sortable table
  $('.widgets-sortables').sortable({
    update: function( event, ui ) {
      
      console.log(ui.item);
      console.log('da');
      $('.wps-ectw-tabs', ui.item).tabs();
      $('.wps-gfw-colorpicker', ui.item).wpColorPicker();
      
    }
  });*/
  
  
});