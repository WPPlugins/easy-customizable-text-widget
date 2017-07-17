<?php
/* 
  WPS Easy Customizable Text Widget
  Copyright © 2016 PremiumWPSuite
*/

class wps_gfwidget extends WP_Widget {

  static $fonts_used = array();

  function __construct() {
    // Instantiate the parent object
    parent::__construct( false, 'Easy Customizable Text Widget' );
  } // __construct


  static function after_page_load() {
    echo self::set_google_fonts_js(self::$fonts_used);
  } // after_page_load


  function widget($args, $instance) {
    $title = apply_filters('widget_title', $instance['title']);

    // Set Options
    $font = trim($instance['font']);
    if ($font == 'regular') $instance['font'] = '';
    if (!empty($font) && $font != 'regular') {
      $font = explode(':', $font);

      $font_family = str_replace('+', ' ', $font[0]);
      $variant = $font[1];

      $find_font = wps_gfwidget::find_font($font_family);
      self::$fonts_used[] = $find_font['name'] . ':' . $variant;
    }

    // 
    $css = 'style="';
    $overlay_css = 'style="';

    if (!empty($instance['font-size'])) {
      $css .= 'font-size:' . $instance['font-size'] . ';';
    } 

    if (!empty($instance['font'])) {
      $css .= 'font-family:\'' . $font_family . '\', ' . $find_font['category'] . ';font-weight:' . $variant . ';';
    } 

    if (!empty($instance['line-height'])) {
      $css .= 'line-height:' . $instance['line-height'] . ';';
    } 

    if (!empty($instance['font-color'])) {
      $css .= 'color:' . $instance['font-color'] . ';';
    }    

    if (!empty($instance['background-color']) && empty($instance['background-image'])) {
      $overlay_css .= 'background-color:' . self::hex2rgba($instance['background-color'], ($instance['background-color-opacity']/100)) . ';';
    } else if (!empty($instance['background-image'])) {
      if ($instance['background-image-type'] == 'cover') {
        $css .= 'background:url(\'' . $instance['background-image'] . '\') ' . $instance['background-image-position'] . ' no-repeat;';
        $css .= 'background-size:cover;';
      } else {
        $css .= 'background:url(\'' . $instance['background-image'] . '\') ' . $instance['background-image-position'] . ' ' . $instance['background-image-type'] . ';';
      }

      if (!empty($instance['background-color'])) {
        $overlay_css .= 'background-color:' . self::hex2rgba($instance['background-color'], ($instance['background-color-opacity']/100)) . ';';
      }

    } 

    if (!empty($instance['border-radius'])) {
      $css .= 'border-radius:' . $instance['border-radius'] . ';';
    } 

    if (!empty($instance['content-paddings'])) {
      $css .= 'padding:';
      foreach ($instance['content-paddings'] as $k => $padding) {
        $css .= $padding . ' ';
      }
      $css .= ';';
    }

    if (!empty($instance['content-align'])) {
      $css .= 'text-align:';
      $css .= $instance['content-align'];
      $css .= ';';
    }

    $overlay_css .= '"';
    $css .= '"';

    // Sticky Tape, pins...
    $sticky = '';
    if (!empty($instance['pin-icon'])) {

      if (strpos($instance['pin-icon'], 'tape')) {
        $sticky .= '<div class="sticky sticky-tape ' . $instance['pin-position'] . '"><img src="' . plugins_url('assets/images/pins/' . $instance['pin-position'] . '-' . $instance['pin-icon'], __FILE__) . '" /></div>';
      } else {
        $sticky .= '<div class="sticky ' . $instance['pin-position'] . '"><img src="' . plugins_url('assets/images/pins/' . $instance['pin-position'] . '-' . $instance['pin-icon'], __FILE__) . '" /></div>';
      }

    }

    // before and after widget arguments are defined by themes
    echo $args['before_widget'];

    if (!empty($title)) {
      echo $args['before_title'] . $title . $args['after_title']; 
    }

    echo '<div class="wps-gfwidget-output" ' . $css . '>';
    echo '<div class="wps-gfwidget-overlay" ' . $overlay_css . '></div>';
    echo $sticky;
    echo '<div class="wps-gfwidget-content">';

    if (!empty($instance['paragraphs'])) {
      echo wpautop($instance['content']);
    } else {
      echo $instance['content'];
    }

    if (!empty($instance['button-enabled'])) {
      $btn_css = 'style="';

      if (!empty($instance['button-text-size'])) {
        $btn_css .= 'font-size:' . $instance['button-text-size'] . ';';
      }      

      if (!empty($instance['button-background-color'])) {
        $btn_css .= 'background:' . $instance['button-background-color'] . ';';
      }      

      if (!empty($instance['button-text-color'])) {
        $btn_css .= 'color:' . $instance['button-text-color'] . ';';
      }

      $btn_css .= '"';

      echo '<a href="' . $instance['button-link'] . '" class="wps-gfw-btn" ' . $btn_css . '>' . $instance['button-text'] . '</a>';
    }

    echo '</div>';
    echo '</div>';

    if (!empty($instance['fold-corners']) && $instance['fold-corners'] != 'none') {
      echo '<div class="wps-gfwidget-footer">';

      if ($instance['fold-corners'] == 'both') {
        echo '<div class="wps-gfwidget-footer-left"></div>';
        echo '<div class="wps-gfwidget-footer-right"></div>';
      } else {
        if ($instance['fold-corners'] == 'left') {
          echo '<div class="wps-gfwidget-footer-left"></div>';
        } 

        if ($instance['fold-corners'] == 'right') {
          echo '<div class="wps-gfwidget-footer-right"></div>';
        }
      }

      echo '</div>';
    }

    echo $args['after_widget'];
  } // widget


  function update($new_instance, $old_instance) {
    // Save widget options
    $instance = array();

    // Title
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['content'] = $new_instance['content'];
    $instance['paragraphs'] = $new_instance['paragraphs'];
    $instance['font-color'] = $new_instance['font-color'];
    $instance['font-size'] = $new_instance['font-size'];
    $instance['font'] = $new_instance['font'];
    $instance['line-height'] = $new_instance['line-height'];
    $instance['background-color'] = $new_instance['background-color'];
    $instance['background-color-opacity'] = $new_instance['background-color-opacity'];
    $instance['content-paddings'] = $new_instance['content-paddings'];
    $instance['fold-corners'] = $new_instance['fold-corners'];
    $instance['border-radius'] = $new_instance['border-radius'];
    $instance['background-image'] = $new_instance['background-image'];
    $instance['background-image-type'] = $new_instance['background-image-type'];
    $instance['background-image-position'] = $new_instance['background-image-position'];
    $instance['pin-icon'] = $new_instance['pin-icon'];
    $instance['pin-position'] = $new_instance['pin-position'];
    $instance['content-align'] = $new_instance['content-align'];
    $instance['button-enabled'] = $new_instance['button-enabled'];
    $instance['button-background-color'] = $new_instance['button-background-color'];
    $instance['button-text'] = $new_instance['button-text'];
    $instance['button-link'] = $new_instance['button-link'];
    $instance['button-text-size'] = $new_instance['button-text-size'];
    $instance['button-text-color'] = $new_instance['button-text-color'];

    return $instance;
  } // update


  function is_checked($value, $setting) {
    if (!empty($setting) && $value == $setting) {
      return 'checked="checked"';
    } else {
      return '';
    }
  } // is_checked


  function form($instance) {
    // Widget Setup Form
    $title = 'Awesome Widget';
    $content = 'Your widget text can go here...';
    $content_align = 'left';
    $font_color = '#333333';
    $background_color = '#f6f6f6';
    $background_color_opacity = '0.5';
    $paragraphs = '';
    $font_size = '14px';
    $font = '';
    $paddings = array('left' => '15px', 'top' => '15px', 'right' => '15px', 'bottom' => '15px');
    $boder_radius = '0px';
    $background_image = '';
    $background_image_type = '';
    $background_image_position = '';
    $pin_icon = '';
    $pin_position = '';
    $fold_corners = 'both';
    $line_height = 'auto';
    // Button 
    $button_link = '#';
    $button_text = 'Click Here';
    $button_text_size = '14px';
    $button_text_color = '#333';
    $button_background_color = '';
    $button_enabled = '';

    // Various Options
    $line_height_sizes['auto'] = 'Auto';
    for ($i=1;$i<=65;$i++) {
      $line_height_sizes[$i . 'px'] = $i . 'px';
    }

    for ($i=10;$i<=100;$i+=10) {
      $background_color_opacities[$i] = $i . '%';
    }


    $content_align_opts['left'] = 'Left';
    $content_align_opts['center'] = 'Center';
    $content_align_opts['right'] = 'Right';

    $bg_type['cover'] = 'Cover';
    $bg_type['repeat'] = 'Repeat';
    $bg_type['repeat-x'] = 'Repeat by X';
    $bg_type['repeat-y'] = 'Repeat by Y';

    $bg_position['center top'] = 'Center Top';
    $bg_position['center center'] = 'Center Center';
    $bg_position['center bottom'] = 'Center Bottom';
    $bg_position['left top'] = 'Left Top';
    $bg_position['left center'] = 'Left Center';
    $bg_position['left bottom'] = 'Left Bottom';
    $bg_position['right top'] = 'Right Top';
    $bg_position['right center'] = 'Right Center';
    $bg_position['right bottom'] = 'Right Bottom';

    // Pin Icons
    $pin_icons['black.png'] = 'Classic Pin Black';
    $pin_icons['blue.png'] = 'Classic Pin Blue';
    $pin_icons['gray.png'] = 'Classic Pin Gray';
    $pin_icons['orange.png'] = 'Classic Pin Orange';
    $pin_icons['white.png'] = 'Classic Pin White';
    // Tape Icons
    $tape_icons['sticky-tape.png'] = 'Sticky Tape';
    // Pin Positions
    $pin_positions['top-left'] = 'Top Left';
    $pin_positions['top-center'] = 'Top Center';
    $pin_positions['top-right'] = 'Top Right';
    // Fold Corners
    $fold_corners_opt['none'] = 'None';
    $fold_corners_opt['both'] = 'Both';
    $fold_corners_opt['left'] = 'Left Side Only';
    $fold_corners_opt['right'] = 'Right Side Only';

    if (isset($instance['title'])) {
      $title = $instance['title'];
    }

    if (isset($instance['content'])) {
      $content = $instance['content'];
    }  

    if (isset($instance['content-align'])) {
      $content_align = $instance['content-align'];
    }    

    if (isset($instance['paragraphs'])) {
      $paragraphs = $instance['paragraphs'];
    }

    if (isset($instance['font-color'])) {
      $font_color = $instance['font-color'];
    }

    if (isset($instance['background-color'])) {
      $background_color = $instance['background-color'];
    }

    if (isset($instance['background-color-opacity'])) {
      $background_color_opacity = $instance['background-color-opacity'];
    }

    if (isset($instance['font-size'])) {
      $font_size = $instance['font-size'];
    }    

    if (isset($instance['font'])) {
      $font = $instance['font'];
    }

    if (isset($instance['content-paddings'])) {
      $paddings = $instance['content-paddings'];
    }

    if (isset($instance['border-radius'])) {
      $boder_radius = $instance['border-radius'];
    }    

    if (isset($instance['background-image'])) {
      $background_image = $instance['background-image'];
    }

    if (isset($instance['background-image-type'])) {
      $background_image_type = $instance['background-image-type'];
    }

    if (isset($instance['background-image-position'])) {
      $background_image_position = $instance['background-image-position'];
    }    

    if (isset($instance['pin-icon'])) {
      $pin_icon = $instance['pin-icon'];
    }    

    if (isset($instance['pin-position'])) {
      $pin_position = $instance['pin-position'];
    }    

    if (isset($instance['fold-corners'])) {
      $fold_corners = $instance['fold-corners'];
    }

    if (isset($instance['line-height'])) {
      $line_height = $instance['line-height'];
    }

    if (isset($instance['button-text'])) {
      $button_text = $instance['button-text'];
    }   

    if (isset($instance['button-text-color'])) {
      $button_text_color = $instance['button-text-color'];
    }   

    if (isset($instance['button-text-size'])) {
      $button_text_size = $instance['button-text-size'];
    }   

    if (isset($instance['button-link'])) {
      $button_link = $instance['button-link'];
    }     

    if (isset($instance['button-background-color'])) {
      $button_background_color = $instance['button-background-color'];
    }    

    if (isset($instance['button-enabled'])) {
      $button_enabled = $instance['button-enabled'];
    }

    echo '<p>
    <label for="' . $this->get_field_id('title') . '">Title:</label>
    <input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . esc_attr($title) . '" />
    </p>';   

    echo '<p>
    <label for="' . $this->get_field_id('content') . '">Content:</label><br/>
    <textarea class="widefat" id="' . $this->get_field_id('content') . '" rows="10" name="' . $this->get_field_name('content') . '">' . esc_attr($content) . '</textarea>
    </p>';

    echo '<p>
    <label for="' . $this->get_field_id('paragraphs') . '">Automatically add paragraphs:</label>
    <input type="checkbox" id="' . $this->get_field_id('paragraphs') . '" name="' . $this->get_field_name('paragraphs') . '" value="1" ' . self::is_checked('1', $paragraphs) . ' style="margin-left:8px;" />
    </p>';

    echo '<hr/>';
    
    // Tabs Start
    echo '<div class="wps-ectw-tabs">';
    
    // Tab Headers
    echo '<ul>';
    echo '<li><a href="#button-setup">Button Setup</a></li>';
    echo '<li><a href="#content-setup">Content Setup</a></li>';
    echo '<li><a href="#background-setup">Background Setup</a></li>';
    echo '<li><a href="#font-setup">Font Setup</a></li>';
    echo '</ul>';
    
    
    
    echo '<div id="button-setup">';
    echo '<h3>Button Setup</h3>';

    echo '<label for="' . $this->get_field_id('button-enabled') . '">Use button?</label>';
    echo '<input id="' . $this->get_field_id('button-enabled') . '" name="' . $this->get_field_name('button-enabled') . '" type="checkbox" value="1" ' . self::is_checked('1', $button_enabled) . ' style="margin-left:8px;" />';

    echo '<p>
    <label for="' . $this->get_field_id('button-text') . '">Button Text:</label>
    <input class="widefat" id="' . $this->get_field_id('button-text') . '" name="' . $this->get_field_name('button-text') . '" type="text" value="' . esc_attr($button_text) . '" />
    </p>';  

    echo '<p>
    <label for="' . $this->get_field_id('button-text-color') . '">Button Text Color:</label>
    <input class="wps-gfw-colorpicker" id="' . $this->get_field_id('button-text-color') . '" name="' . $this->get_field_name('button-text-color') . '" type="text" value="' . esc_attr($button_text_color) . '" />
    </p>';  

    echo '<p>
    <label for="' . $this->get_field_id('button-text-size') . '"><strong>Button Text Size:</strong></label><br/>
    <select class="widefat" name="' . $this->get_field_name('button-text-size') . '" id="' . $this->get_field_id('button-text-size') . '">
    ' . self::list_font_sizes($button_text_size) . '
    </select>
    </p>';

    echo '<p>
    <label for="' . $this->get_field_id('button-link') . '">Button Link:</label>
    <input class="widefat" id="' . $this->get_field_id('button-link') . '" name="' . $this->get_field_name('button-link') . '" type="text" value="' . esc_attr($button_link) . '" />
    </p>';  

    echo '<p>
    <label for="' . $this->get_field_id('button-background-color') . '">Button Background Color:</label>
    <input class="wps-gfw-colorpicker" id="' . $this->get_field_id('button-background-color') . '" name="' . $this->get_field_name('button-background-color') . '" type="text" value="' . esc_attr($button_background_color) . '" />
    </p>';

    echo '</div>';
    echo '<div id="content-setup">';
    echo '<h3>Content Setup</h3>';

    echo '<p>
    <label for="' . $this->get_field_id('content-paddings') . '"><strong>Content Paddings:</strong></label><br/>
    <span class="padding-label">Top:</span><input class="padding-input" id="' . $this->get_field_id('content-paddings-top') . '" name="' . $this->get_field_name('content-paddings[top]') . '" type="text" value="' . esc_attr($paddings['top']) . '" />
    <span class="padding-label">Right:</span><input class="padding-input" id="' . $this->get_field_id('content-paddings-right') . '" name="' . $this->get_field_name('content-paddings[right]') . '" type="text" value="' . esc_attr($paddings['right']) . '" />
    <span class="padding-label">Bottom:</span><input class="padding-input" id="' . $this->get_field_id('content-paddings-bottom') . '" name="' . $this->get_field_name('content-paddings[bottom]') . '" type="text" value="' . esc_attr($paddings['bottom']) . '" />
    <span class="padding-label">Left:</span><input class="padding-input" id="' . $this->get_field_id('content-paddings-left') . '" name="' . $this->get_field_name('content-paddings[left]') . '" type="text" value="' . esc_attr($paddings['left']) . '" />
    </p>';

    echo '<p>
    <label for="' . $this->get_field_id('content-align') . '"><strong>Content Alignement:</strong></label><br/>
    <select class="widefat" name="' . $this->get_field_name('content-align') . '" id="' . $this->get_field_id('content-align') . '">
    ' . self::list_options($content_align_opts, $content_align) . '
    </select>
    </p>';

    echo '<p>
    <label for="' . $this->get_field_id('border-radius') . '"><strong>Border Radius:</strong></label><br/>
    <select class="widefat" name="' . $this->get_field_name('border-radius') . '" id="' . $this->get_field_id('border-radius') . '">
    ' . self::list_sizes($boder_radius) . '
    </select>
    </p>';

    echo '<p>
    <label for="' . $this->get_field_id('fold-corners') . '"><strong>Fold Corners:</strong></label><br/>
    <select class="widefat" name="' . $this->get_field_name('fold-corners') . '" id="' . $this->get_field_id('fold-corners') . '">
    ' . self::list_options($fold_corners_opt, $fold_corners) . '
    </select>
    </p>';

    echo '</div>';
    echo '<div id="background-setup">';
    echo '<h3>Background Setup</h3>';

    echo '<p>
    <label for="' . $this->get_field_id('background-color') . '">Background Color:</label>
    <input class="wps-gfw-colorpicker" id="' . $this->get_field_id('background-color') . '" name="' . $this->get_field_name('background-color') . '" type="text" value="' . esc_attr($background_color) . '" />
    </p>';

    echo '<p>
    <label for="' . $this->get_field_id('background-color-opacity') . '">Background Color Opacity:</label>
    <select class="widefat" name="' . $this->get_field_name('background-color-opacity') . '" id="' . $this->get_field_id('background-color-opacity') . '">
    ' . self::list_options($background_color_opacities, $background_color_opacity) . '
    </select>
    </p>';

    echo '<p>
    <label for="' . $this->get_field_id('background-image') . '">Background Image:</label><br/>
    <div class="wps-predefined-bg-samples">
    ' . self::predefined_backgrounds($background_image) . '
    </div>
    <br/>
    <strong>Custom Image:</strong><br/>
    <input type="text" class="wps-custom-bg-image" id="' .  $this->get_field_id('background-image') . '" name="' . $this->get_field_name('background-image') . '" value="' . $background_image . '" />
    <input id="wps-upload-image" type="button" class="button" value="Set custom image" />
    </p>';

    echo '<p>
    <label for="' . $this->get_field_id('background-image-type') . '">Background Image Type:</label><br/>
    <select class="widefat" name="' . $this->get_field_name('background-image-type') . '" id="' . $this->get_field_id('background-image-type') . '">
    ' . self::list_options($bg_type, $background_image_type) . '
    </select>
    </p>';

    echo '<p>
    <label for="' . $this->get_field_id('background-image-position') . '">Background Image Position:</label><br/>
    <select class="widefat" name="' . $this->get_field_name('background-image-position') . '" id="' . $this->get_field_id('background-image-position') . '">
    ' . self::list_options($bg_position, $background_image_position) . '
    </select>
    </p>';

    echo '<hr/>';
    echo '<h3>Pin Setup</h3>';

    echo '<p>
    <label for="' . $this->get_field_id('pin-icon') . '">Pin Icon:</label><br/>
    <select class="widefat" name="' . $this->get_field_name('pin-icon') . '" id="' . $this->get_field_id('pin-icon') . '">
    ' . self::list_options($pin_icons, $pin_icon, 'Classic Icons') . '
    ' . self::list_options($tape_icons, $pin_icon, 'Tape Icons') . '
    </select>
    </p>';

    echo '<p>
    <label for="' . $this->get_field_id('pin-position') . '">Pin Position:</label><br/>
    <select class="widefat" name="' . $this->get_field_name('pin-position') . '" id="' . $this->get_field_id('pin-position') . '">
    ' . self::list_options($pin_positions, $pin_position) . '
    </select>
    </p>';

    echo '</div>';
    echo '<div id="font-setup">';
    
    echo '<h3>Font Setup</h3>';

    echo '<p>
    <label for="' . $this->get_field_id('font-color') . '">Font Color:</label>
    <input class="wps-gfw-colorpicker" id="' . $this->get_field_id('font-color') . '" name="' . $this->get_field_name('font-color') . '" type="text" value="' . esc_attr($font_color) . '" />
    </p>';

    echo '<p>
    <label for="' . $this->get_field_id('font-size') . '">Font Size:</label><br/>
    <select class="widefat wps-gfw-selected-font-size" name="' . $this->get_field_name('font-size') . '" id="' . $this->get_field_id('font-size') . '">
    ' . self::list_font_sizes($font_size) . '
    </select>
    </p>';

    echo '<p>
    <label for="' . $this->get_field_id('line-height') . '">Line Height:</label><br/>
    <select class="widefat" name="' . $this->get_field_name('line-height') . '" id="' . $this->get_field_id('line-height') . '">
    ' . self::list_options($line_height_sizes, $line_height) . '
    </select>
    </p>';

    echo '<p>
    <label for="' . $this->get_field_id('font') . '">Google Fonts:</label><br/>
    <select class="widefat wps-gfw-selected-font" name="' . $this->get_field_name('font') . '" id="' . $this->get_field_id('font') . '">
    ' . self::list_fonts($font) . '
    </select>
    </p>';

    echo '<div class="wps-gfw-preview-font">
    </div>';
    
    echo '<input type="button" class="wps-gfw-preview-font-button button button-primary" value="Preview Font" />';
    echo '</div>';
    
    // Tab End
    echo '</div>';
    echo '<br/>';
    echo '<br/>';

    /*
    echo '<script type="text/javascript">';
    echo 'jQuery(document).ready(function($){';
    echo 'jQuery(\'.wps-gfw-colorpicker\').wpColorPicker();';
    echo '});';
    echo '</script>';
    */
  } // form


  static function list_options($options, $selected, $group = '') {
    $output = '';

    if (is_array($options)) {

      if (!empty($group)) {
        $output .= '<optgroup label="' . $group . '">';
      }

      foreach ($options as $key => $option) {
        if ($key == $selected) {
          $output .= '<option value="' . $key . '" selected="selected">' . $option . '</option>';
        } else {
          $output .= '<option value="' . $key . '">' . $option . '</option>';
        }
      }

      if (!empty($group)) {
        $output .= '</optgroup>';
      }
    }

    return $output;
  } // $bg_type


  static function list_font_sizes($font_size = '') {
    $output = '';

    for ($i=12;$i<=52;$i++) {
      if ($i . 'px' == $font_size) {
        $output .= '<option value="' . $i . 'px" selected="selected">' . $i . 'px</option>';
      } else {
        $output .= '<option value="' . $i . 'px">' . $i . 'px</option>';
      }
    }

    return $output;
  } // list_font_sizes


  static function list_sizes($size = '') {
    $output = '';

    for ($i=0;$i<=50;$i++) {
      if ($i . 'px' == $size) {
        $output .= '<option value="' . $i . 'px" selected="selected">' . $i . 'px</option>';
      } else {
        $output .= '<option value="' . $i . 'px">' . $i . 'px</option>';
      }
    }

    return $output;
  } // list_font_sizes


  static function list_fonts($font_family = '') {
    $output = '';
    $fonts = get_option(WPS_GFW_FONTS);

    $output .= '<option value="regular">Use web page default font-family.</option>';

    if ($fonts) {
      foreach ($fonts as $font) {

        if (is_array($font['variants'])) {
          foreach ($font['variants'] as $index => $variant) {
            $font_slug = str_replace(' ', '+', $font['name']);
            if ($font_slug . ':' . $variant == $font_family) {
              $output .= '<option value="' . $font_slug . ':' . $variant . '" selected="selected">' . $font['name'] . ' (' . $variant . ')</option>';
            } else {
              $output .= '<option value="' . $font_slug . ':' . $variant . '">' . $font['name'] . ' (' . $variant . ')</option>';
            }
          }
        }

      }
    } else {
      $output .= '<option value="error">Error: No google fonts loaded - contact support.</option>';
    }

    return $output; 
  } // list_fonts


  static function find_font($font) {
    $font = sanitize_title($font);
    $fonts = get_option(WPS_GFW_FONTS);

    if (!empty($fonts[$font])) {
      return $fonts[$font];
    } else {
      return false;
    }

  } // find_font


  static function set_google_fonts_js($instance) {
    $output = '';
    $fonts = '';

    if (!empty($instance) && is_array($instance)) {
      foreach ($instance as $key => $font) {
        $fonts .= "'" . $font . "',";
      }
      $fonts = rtrim($fonts,',');

      $rnd = rand(285,999)+rand(5,55);
      $output .= '<script type="text/javascript">';
      #$output .= '$("head").append("<link href=\'https://fonts.googleapis.com/css?family=' . $instance . '\' rel=\'stylesheet\' type=\'text/css\'>");';

      $output .= "WebFontConfig = {
      google: { families: [ " . $fonts . " ] }
      };

      (function() {
      var wps_" . $rnd . " = document.createElement('script');
      wps_" . $rnd . ".src = ('https:' == document.location.protocol ? 'https' : 'http') +
      '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
      wps_" . $rnd . ".type = 'text/javascript';
      wps_" . $rnd . ".async = 'true';
      var s" . $rnd . " = document.getElementsByTagName('script')[0];
      s" . $rnd . ".parentNode.insertBefore(wps_" . $rnd . ", s" . $rnd . ");
      })();";

      $output .= '</script>';

    }

    return $output;
  } // set_google_fonts_js


  static function predefined_backgrounds($selected = '') {
    $output = '';

    $bg_images = array();
    $bg_images['crumpled-paper'] = 'Crumpled Paper';
    $bg_images['lines-paper'] = 'Lines Paper';
    $bg_images['symphony'] = 'Symphony';
    $bg_images['congruent_pentagon'] = 'Congruent Pentagon';


    foreach ($bg_images as $bg => $name) {
      $img_src = plugins_url('assets/images/' . $bg . '.png', __FILE__);
      if ($img_src == $selected) {
        $output .= '<div class="wps-gfw-bg-sample selected" data-bg-image="' . $img_src . '" style="background:url(\'' . $img_src . '\') top left repeat;">&nbsp;</div>';
      } else {
        $output .= '<div class="wps-gfw-bg-sample" data-bg-image="' . $img_src . '" style="background:url(\'' . $img_src . '\') top left repeat;">&nbsp;</div>';
      }
    }

    return $output;
  } // predefined_backgrounds


  static function hex2rgba($color, $opacity = false) {

    $default = 'rgb(0,0,0)';

    //Return default if no color provided
    if(empty($color))
      return $default; 

    //Sanitize $color if "#" is provided 
    if ($color[0] == '#' ) {
      $color = substr( $color, 1 );
    }

    //Check if color has 6 or 3 characters and get values
    if (strlen($color) == 6) {
      $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
    } elseif ( strlen( $color ) == 3 ) {
      $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
    } else {
      return $default;
    }

    //Convert hexadec to rgb
    $rgb =  array_map('hexdec', $hex);

    //Check if opacity is set(rgba or rgb)
    if($opacity){
      if(abs($opacity) > 1)
        $opacity = 1.0;
      $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
    } else {
      $output = 'rgb('.implode(",",$rgb).')';
    }

    //Return rgb(a) color string
    return $output;
  } // hex2rgba


} // wps_gfwidget

function wps_gfwidget_register_widgets() {
  register_widget('wps_gfwidget');
} // wps_gfwidget_register_widgets

add_action('widgets_init', 'wps_gfwidget_register_widgets');