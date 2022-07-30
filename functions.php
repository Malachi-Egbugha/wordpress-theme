<?php
function university_files() {
  wp_enqueue_script('main-university-js', get_theme_file_uri('/js/scripts-bundled.js'), NULL, '1.0', true);
  wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
  wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
  wp_enqueue_style('university_main_styles', get_stylesheet_uri());
}

add_action('wp_enqueue_scripts', 'university_files');
function university_features(){
  register_nav_menu('headerMenuLocation', 'Header Menu Location');
  add_theme_support('title-tag');

}
add_action('after_setup_theme', 'university_features');
function university_adjust_queries($query){
  if(!is_admin() AND is_post_type_archive('program') AND is_main_query()){
    $query->set('orderby', 'title');
    $query->set('order', 'ASC');
    $query->set('posts_per_page', -1);

  }
  if(!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()){
  $today = date('Ymd');
  $query->set('meta_key','event_date');
  $query->set('order_by','meta_value_num');
  $query->set('order','ASC');
  $query->set('meta_query',array(
    array(
      'key' => 'event_date',
      'compare' => '>=',
      'value' => $today,
      'type' => 'numeric'
    )
  ));
  }

}
add_action('pre_get_posts','university_adjust_queries');
//redirect subscriber acccounts out of admin and unto home page
add_action('admin_init','redirectSubsToFrontend');
function redirectSubsToFrontend(){
  $ourCurrentUser= wp_get_current_user();
  if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber'){
    wp_redirect(site_url('/'));
    exit;

  }
}

add_action('wp_loaded','noSubsAdminBar');
//remove sub admin bar
function noSubsAdminBar(){
  $ourCurrentUser= wp_get_current_user();
  if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber'){
    show_admin_bar(false);
    

  }
}
//customize login screen
add_filter('login_headerurl', 'ourHeaderUrl');
function ourHeaderUrl(){
  return esc_url(site_url('/'));
}
add_filter('login_headertitle','ourLoginTitle');
function ourLoginTitle(){
  return 'Enet Resources';

}
add_action('login_head', 'ourLoginCss' );
function ourLoginCss(){
  echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('stylesheet_directory') . '/login/custom-login-style.css" />';

}


//create short code for member regustration
function vicode_registration_form(){
  //if user is not logged in
  if(is_user_logged_in()){
    //check if registration is enables
    if(get_option('users_can_register')){
      $output = vicode_registration_fields();
    }
    else{
      $output = __('User registration is not enabled');
    }
    return $output;
  }
};

add_shortcode('register_form', 'vicode_registration_form');
function vicode_registration_fields(){
  ob_start();?>
  <?php

  //show reg errors
  vicode_register_message();
  ?>
  <form id="vicode_registartion_form" class="vicode_form" action="" method="POST">
    <fieldset>
    <p>
      <labl for="vicode_user_login"><?php _e("Username") ?></lable>
      <input type="text" name="vicode_user_login" id="vicode_user_login" class="vicode_user_login"/>
    </p>
    <p>
      <labl for="vicode_user_email"><?php _e("Email") ?></lable>
      <input type="text" name="vicode_user_email" id="vicode_user_email" class="vicode_user_email"/>
    </p>
    <p>
      <labl for="vicode_user_first"><?php _e("First Name") ?></lable>
      <input type="text" name="vicode_user_first" id="vicode_user_first" class="vicode_user_first"/>
    </p>
    <p>
      <labl for="vicode_user_last"><?php _e("Last Name") ?></lable>
      <input type="text" name="vicode_user_last" id="vicode_user_last" class="vicode_user_last"/>
    </p>
    <p>
      <labl for="password"><?php _e("Password") ?></lable>
      <input type="password" name="pass" id="password" class="password"/>
    </p>
    <p>
      <labl for="password_again"><?php _e("Password Again") ?></lable>
      <input type="password" name="vicode_user_pass_confirm" id="password_again" class="password_again"/>
    </p>
    <p>
      <input type="hidden" name="vicode_csrf" value="<?php echo wp_create_nonce('vicode-csrf') ?>"/>
      <input type="submit" value="<?php _e('Register your Account'); ?>" />
</p>
</fieldset>
</form>
  <?php
  return ob_get_clean();
}
//register new user
function vicode_add_new_user(){
  if(isset($_POST['vicode_user_login']) && wp_verify_nonce($_POST['vicode_csrf'],'vicode-csrf')){

$user_login =$_POST['vicode_user_login'];
$user_email =$_POST['vicode_user_email'];
$user_first =$_POST['vicode_user_first'];
$user_last =$_POST['vicode_user_last'];
$user_pass =$_POST['pass'];
$user_pass_confirm =$_POST['vicode_user_pass_confirm'];
require_once(ABSPATH . WPINC  . '/registration.php');

if(username_exists($user_login)){
  vicode_errors()->add('username_unavailable', _('Username already taken'));
}
  if(!validate_username($user_login)){
    vicode_errors()->add('username_empty', _('Invalid Username'));

  };
  if($user_login == ''){
    vicode_errors()->add('username_empty', _('Please enter a username'));

  }
  if(!is_email($user_email)){
    vicode_errors()->add('email_invalid', _('Invalid email'));
  }
  if(email_exists($user_email)){
    vicode_errors()->add('email_used',_('Email already exist'));

  }
  if($user_pass == ''){
    vicode_errors()->add('password_empty',_('Please enter a password!'));
  }
  if($user_pass !== $user_pass_confirm){
    vicode_errors()->add('password_mismatch',_('Passwords do not match '));

  }
  
  
  $errors = vicode_errors()->get_error_message();
  if(empty($errors)){
    $new_user_id = wp_insert_user(array( 
      'user_login' => $user_login,
      'user_pass' => $user_pass,
      'user_email' => $user_email,
      'first_name' => $user_first,
      'last_name' => $user_last,
      'user_registered' => date('Y-m-d H:i:s'),
      'role' => 'subscriber'
    ));
    if($new_user_id){
      //send an email to the admin
      wp_new_user_notification($new_user_id);
      //log the user in
      wp_setcookie($user_login,$user_pass, true);
      wp_set_current_user($new_user_id, $user_login);
      do_action('wp_login', $user_login);
      // send user to home page
      wp_redirect(home_url());
      exit;

    }
  }
}

 }

add_action('init', 'vicode_add_new_user');

//function for tracking error messages
function vicode_errors(){
  static $wp_error;//global variable handle
  return isset($wp_error) ? $wp_error:($wp_error= new WP_Error(null, null, null));
}
//display error message
function vicode_register_message(){
  if($codes = vicode_errors()->get_error_codes()){
    echo '<div class="vicode_errors">';
    //loop error codes and display errors
    foreach($codes as $code){
      $message = vicode_errors()->get_error_message($code);
      echo '<span class="error"><strong>'. _('Error') . '</strong>:' . $message . '</span></br>';

    }
    echo '</div>';
  }
}