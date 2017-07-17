jQuery(document).ready(function($){

  var image_src = '';

  // Upload background image
  $(document).on('click', '#wps-upload-image', function(e) {
    e.preventDefault();
    
    image_src = $(this).prev('input');
    var image = wp.media({ 
      title: 'Upload Image',
      // mutiple: true if you want to upload multiple files at once
      multiple: false
    }).open()
    .on('select', function(e){
      // This will return the selected image from the Media Uploader, the result is an object
      var uploaded_image = image.state().get('selection').first();
      // We convert uploaded_image to a JSON object to make accessing it easier
      var image_url = uploaded_image.toJSON().url;
      // Let's assign the url value to the input field
      $(image_src).val(image_url);
    });
    
  });
  
  
  // Select predefined imageccccc
  $(document).on('click', '.wps-gfw-bg-sample', function(e){
    var obj = $(this);
    var img_src = $(this).data('bg-image');
    var form = $(this).parents('form');
    $('.wps-custom-bg-image', form).val(img_src);
    
    $('.wps-predefined-bg-samples div.selected').removeClass('selected');
    $(obj).addClass('selected');
  });
  

  // Tabs init
  $('.wps-ectw-tabs:not(".ui-tabs")', '#widgets-right').tabs();

  $(document).on('widget-added widget-updated', function(event, widget){
    $('.wps-ectw-tabs:not(".ui-tabs")', widget).tabs();
  });  
  
  // Colorpicker init
  $('.wps-gfw-colorpicker:not(".color-picker")', '#widgets-right').wpColorPicker();

  $(document).on('widget-added widget-updated', function(event, widget){
    $('.wps-gfw-colorpicker:not(".color-picker")', widget).wpColorPicker();
  });

  // Preview Font Button
  $(document).on('click', '.wps-gfw-preview-font-button', function(e){
    e.preventDefault();
    var parent = $(this).parents('.widget-content');
    var font = $('.wps-gfw-selected-font', parent).val();
    var font_size = $('.wps-gfw-selected-font-size', parent).val();

    WebFontConfig = {
      google: { families: [ font ] }
    };


    var wps = document.createElement('script');
    wps.src = ('https:' == document.location.protocol ? 'https' : 'http') +
    '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
    wps.type = 'text/javascript';
    wps.async = 'true';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(wps, s);

    $.post(ajaxurl, {action:'get_font_preview', font:font}, function(response){
      if (response.success) {
        $('.wps-gfw-preview-font', parent).html('<strong>Preview text:</strong><br/><p style="font-family: \'' + response.data.family + '\', ' + response.data.variant + ';font-size:' + font_size + ';">Lorem ipsum dolor sit amet, mea ei oporteat laboramus, cu salutandi voluptatibus interpretaris sea. No putant iudicabit sed, nisl aliquam et pro, no nam eros affert alterum. In eum dictas antiopam efficiendi, choro putant salutandi eos ad.</p>');
      }
    });

  });

});