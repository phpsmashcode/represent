<?php
/**
 * @package Represent
 * @version 1.5
 */
/*
Plugin Name: Represent
Plugin URI: http://represent.cc/
Description: Plugin developed for http://represent.cc/
Author: Hariprasad Vijayan
Version: 1.5
Author URI: http://phpsmashcode.com/
*/

/* Add plugin menu to admin dashboard */

add_action( 'admin_menu', 'register__represent_cc_menu' );
function register__represent_cc_menu(){
	add_menu_page( 'Represent API Settings', 'represent', 'manage_options', 'represent_cc', '__represent_cc', plugins_url( 'represent/images/replogo.png' ), 6 ); 
}
/* Callback function for displaying Represent settings page. */
function __represent_cc(){
	if($_POST)
	{
		if (!wp_verify_nonce( $_POST['_wpnonce'], 'r.cc' ) ) {
			print 'Sorry, your nonce did not verify.';
			exit;
		} else {
			$rcc_key = esc_attr($_POST['rcc_key']);
			$rcc_secret = esc_attr($_POST['rcc_secret']);
			/*$params = array(
				"email" => $rcc_login,
				"password" => $rcc_pwd,
			);*/
			$params = array(
				"key" => $rcc_key,
				"secret" => $rcc_secret,
			);
			
			//$login_status = represent_cc_login("https://represent.cc/auth/mobile/",$params);
			$login_status = represent_cc_login("https://represent.cc/auth/api",$params);
			$login_status = json_decode($login_status);
			//echo "<pre>";
			//print_r($login_status);
			//echo "</pre>";
			if($login_status->success == true)
			{
				$API = $login_status->access_token;
				$rcc_settings['api'] = $API;
			}
			$rcc_settings['key'] = $rcc_key;
			$rcc_settings['secret'] = $rcc_secret;
			update_option( 'rcc_settings', serialize($rcc_settings));
		}
	}
	$rcc_settings = get_option( 'rcc_settings');
	if($rcc_settings) { $rcc_settings = unserialize($rcc_settings); }
	($rcc_settings['key'])?$key=$rcc_settings['key']:$key='';
	($rcc_settings['secret'])?$secret=$rcc_settings['secret']:$secret='';
?>

<div class="wrap" id="rcc-page">
  <h2> Represent API Settings</h2>
  <form id="frm_rcc_settings" action="" method="post">
	<?php wp_nonce_field( 'r.cc', '_wpnonce' ); ?>
    <h3>API Information</h3>
    <span class="description">You can find your API information on the Settings tab on <a href="https://represent.cc/profile">https://represent.cc/profile</a>.</span>
    <table class="form-table">
      <tbody>
        <tr>
          <th><label for="api_key">API Key</label></th>
          <td><input type="text" name="rcc_key" id="rcc_key" value="<?php echo $key; ?>" class="regular-text">
            <span class="description">Represent API Key.</span></td>
        </tr>
        <tr>
          <th><label for="api_secret">API Secret</label></th>
          <td><input type="password" name="rcc_secret" id="rcc_secret" value="<?php echo $secret; ?>" class="regular-text">
            <span class="description">Represent API secret.</span></td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" name="submit" id="submit" class="button button-primary" value="Save">
    </p>
  </form>
</div>
<?php }
/*
// Shortcode call back function
function __represent_cc_sc( $atts ){
	if($_POST)
	{
		if (!wp_verify_nonce( $_POST['_wpnonce'], 'r.cc' ) ) {
			print 'Sorry, your nonce did not verify.';
			exit;
		} else {
			$rcc_question = esc_attr($_POST['txt__rcc_question']);
			$rcc_description = esc_attr($_POST['txt__rcc_description']);
			$rcc_settings = get_option( 'rcc_settings');
			if($rcc_settings && !empty($rcc_question)) {
				$rcc_settings = unserialize($rcc_settings); 
				($rcc_settings['user_email'])?$user_email=$rcc_settings['user_email']:$user_email='';
				($rcc_settings['user_password'])?$user_password=$rcc_settings['user_password']:$user_password='';
				$params = array(
					"email" => $user_email,
					"password" => $user_password,
				);
				
				$login_status = represent_cc_login("https://represent.cc/auth/mobile/",$params);
				$login_status = json_decode($login_status);
				if($login_status->success == true)
				{
					$API = $login_status->access_token;
					$get_data = array (
						'access_token' => $API,
					);
					$post_data = array (
						'question' => $rcc_question,
						'description' => $rcc_description
					);
					represent_cc_create_question($get_data, $post_data);
				}
			}
		}
	}
	?>
    <form action="" name="frm_rcc_question" method="post">
    <input type="text" name="txt__rcc_question" value="" placeholder="Enter question here" />
	<?php
		$settings = array( 'media_buttons' => false, 'textarea_name' => 'txt__rcc_description' );
		wp_editor( '', 'txt__rcc_description', $settings );
	?>
    <?php wp_nonce_field( 'r.cc', '_wpnonce' ); ?>
    <input type="submit" name="submit" id="submit" class="button button-primary" value="Add Question">
    </form>
    <?php
}
// Creates short code represent_cc
add_shortcode( 'represent_cc', '__represent_cc_sc' );
*/
function represent_cc_login($url,$params)
{
	$postData = '';
	//create name value pairs seperated by &
	foreach($params as $k => $v) 
	{ 
		$postData .= $k . '='.$v.'&'; 
	}
	rtrim($postData, '&');
	
	$ch = curl_init();  
	
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_HEADER, false); 
	curl_setopt($ch, CURLOPT_POST, count($postData));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    
	
	$output=curl_exec($ch);
	
	if($output === false)
	{
		echo "Error Number:".curl_errno($ch)."<br>";
		echo "Error String:".curl_error($ch);
	}
	curl_close($ch);
	return $output;
}
function represent_cc_create_question($get_data, $post_data)
{
	$url = 'https://represent.cc/question';
	
	$params = '';
    
	foreach($get_data as $key=>$value)
                $params .= $key.'='.$value.'&';
         
        $params = trim($params, '&');
	
	$postData = '';
	//create name value pairs seperated by &
	foreach($post_data as $k => $v) 
	{ 
		$postData .= $k . '='.$v.'&'; 
	}
	rtrim($postData, '&');
	
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url.'?'.$params ); //Url together with parameters
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Return data instead printing directly in Browser
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 7); //Timeout after 7 seconds
    curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
    curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POST, count($postData));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    
	$result = curl_exec($ch);
    
	curl_close($ch);

	
	if($result)
	{
		echo $result;
	}
	else
	{
		echo '';
	}
				/*if(curl_errno($ch))  //catch if curl error exists and show it
	  echo 'Curl error: ' . curl_error($ch);
	else
	  echo $result;*/
}
function gavickpro_tc_css() {
    wp_enqueue_style('gavickpro-tc', plugins_url('/css/style.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'gavickpro_tc_css');
add_action('admin_head', 'gavickpro_add_my_tc_button');
function gavickpro_add_my_tc_button() {
    global $typenow;
    // check user permissions
    if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) {
    return;
    }
    // verify the post type
    if( ! in_array( $typenow, array( 'post', 'page' ) ) )
        return;
    // check if WYSIWYG is enabled
    if ( get_user_option('rich_editing') == 'true') {
        add_filter("mce_external_plugins", "gavickpro_add_tinymce_plugin");
        add_filter('mce_buttons', 'gavickpro_register_my_tc_button');
    }
}
function gavickpro_add_tinymce_plugin($plugin_array) {
    $plugin_array['gavickpro_tc_button'] = plugins_url( '/js/editor-button.js', __FILE__ ); // CHANGE THE BUTTON SCRIPT HERE
    return $plugin_array;
}
function gavickpro_register_my_tc_button($buttons) {
   array_push($buttons, "gavickpro_tc_button");
   return $buttons;
}

add_action( 'wp_ajax_rcc_shortcode_gen', '__callback__rcc_shortcode_gen' );
function __callback__rcc_shortcode_gen() {
    if($_POST)
	{
		$rcc_question = $_POST['txt__rcc_question'];
		$rcc_description = $_POST['txt__rcc_description'];
		$rcc_settings = get_option( 'rcc_settings');
		if($rcc_settings && !empty($rcc_question)) {
			$rcc_settings = unserialize($rcc_settings); 
			($rcc_settings['api'])?$API=$rcc_settings['api']:$API='';
			if(!empty($API))
			{
				$get_data = array (
					'access_token' => $API,
				);
				$post_data = array (
					'question' => $rcc_question,
					'description' => $rcc_description
				);
				represent_cc_create_question($get_data, $post_data);
			}
		}
	}
	die();
}
// Creates short code represent_cc
add_shortcode( 'represent_cc', '__represent_cc_sc' );
// Shortcode call back function
function __represent_cc_sc( $atts ){
	switch ($atts["next"]) {
		case "nothing":
			$data_flow = '';
			break;
		case "results":
			$data_flow = '';
			break;
		case "random_question":
			$data_flow = 'random';
			break;
		case "one_of_my_question":
			$data_flow = 'single';
			break;
		case "topic_question":
			$data_flow = 'group';
			break;
		default:
			$data_flow = 'single';
	}
	if($atts["type"] == 'link')
	{
		if($atts["text"])
		{
			$display_text = $atts["text"];
			$question_text = $atts["question"];
		}
		else
		{
			$display_text = $atts["question"];
			$question_text = 'false';
		}
	$html = '<span class="represent_question" data-question="'.$question_text.'" data-flow="'.$data_flow.'">'.$display_text.'</span>';
	
	$html .= "<style type=\"text/css\">
		.represent_question { border-bottom: 1px dashed red; /*background: rgba(255,0,0,0.1);*/ padding: 2px; cursor: help;}
		.represent_question:before { display: inline-block; content: ' '; background-image: url('https://represent.cc/img/cube_inline_1515.png'); background-size: 14px 14px; height: 16px; width: 16px; background-position: 0px 2px; background-repeat: no-repeat; }
		</style>";
	}
	elseif($atts["type"] == 'box')
	{
		//$html = '<iframe src="https://represent.cc/'.$atts["id"].'/'.$data_flow.'" allowfullscreen style="border:0; width:100%; height: 600px;"></iframe>';
		$html = '<iframe src="https://represent.cc/'.$atts["id"].'" allowfullscreen style="border:0; width:100%; height: 600px;"></iframe>';
	}
	return $html;
}
function r_cc_scripts_api()
{
	$rcc_settings = get_option( 'rcc_settings');
	if($rcc_settings) {
		$rcc_settings = unserialize($rcc_settings); 
		($rcc_settings['api'])?$API=$rcc_settings['api']:$API='';
		if(!empty($API))
		{	
			// Register Represent api
			echo '<script type="text/javascript" src="https://represent.cc/libs/sc.js?'.$API.'"></script>';
		}
	}
	
}
add_action( 'wp_footer', 'r_cc_scripts_api' );
function r_cc_ui_scripts() {
	/*wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-autocomplete' );
	wp_register_style( 'jquery-ui-styles','http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );
	wp_enqueue_style( 'jquery-ui-styles' );*/
	//if ( 'edit.php' != $hook ) {
    //    return;
    //}
    wp_enqueue_script( 'represent-script', plugin_dir_url( __FILE__ ) . 'js/represent.js', array('jquery'), '1.0.0' );
}
add_action('admin_enqueue_scripts', 'r_cc_ui_scripts');
add_action( 'wp_ajax_rcc_searchapi', '__callback__rcc_searchapi' );
function __callback__rcc_searchapi() {
    if($_POST)
	{
		$rcc_question = $_POST['txt__rcc_question'];
		$rcc_settings = get_option( 'rcc_settings');
		if($rcc_settings && !empty($rcc_question)) {
			$rcc_settings = unserialize($rcc_settings); 
			($rcc_settings['api'])?$API=$rcc_settings['api']:$API='';
			if(!empty($API))
			{
				$get_data = array (
					'access_token' => $API,
					'count' => '10',
					'question' => $rcc_question,
				);
				/*$post_data = array (
					'question' => $rcc_question,
				);*/
				$url = 'https://represent.cc/question/questions';	
				$params = '';
				
				foreach($get_data as $key=>$value)
							$params .= $key.'='.$value.'&';
					 
				$params = trim($params, '&');
				
				/*$postData = '';
				//create name value pairs seperated by &
				foreach($post_data as $k => $v) 
				{ 
					$postData .= $k . '='.$v.'&'; 
				}
				rtrim($postData, '&');*/
				
				$ch = curl_init();
			
				curl_setopt($ch, CURLOPT_URL, $url.'?'.$params ); //Url together with parameters
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Return data instead printing directly in Browser
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 7); //Timeout after 7 seconds
				curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_POST, 0);
				//curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
				
				$result = curl_exec($ch);
				
				curl_close($ch);
				if($result)
				{
					echo $result;
				}
				else
				{
					echo '';
				}
				/*if(curl_errno($ch))  //catch if curl error exists and show it
				  echo 'Curl error: ' . curl_error($ch);
				else
				  echo $result;
				  */
			}
		}
	}
	die();
}