<?php
/*
 * @clickmeting-plugin
 * Plugin Name:       Clickmeeting Registration to webinar
 * Plugin URI:        Red-shadow.ru
 * Description:       ClickMeeting  Registration to webinar
 * Version:           1.0.0
 * Author:            Bajex
 * Author URI:        Red-shadow.ru
 */


if ( ! defined( 'WPINC' ) ) {
	die;
}
include_once ( plugin_dir_path( __FILE__ ) . 'ClickMeetingRestClient.php' );
include_once ( plugin_dir_path( __FILE__ ) . 'ClickMeetmetabox.php' );
/*
* Добавляем настройки в меню
*/
add_action('admin_menu', 'my_admin_menu');

function my_admin_menu () {
  add_management_page('ClickMeeting Registration', 'Clickmeeting Reg', 'manage_options', __FILE__, 'Clickmeeting_admin');
}
/*
* Страница с настройками
*/
function Clickmeeting_admin() {
    $room_id = get_option('Clickmeeting_room_id', 'room_id');
    $header= get_option('Clickmeeting_header', 'header');
    $time= get_option('Clickmeeting_time', 'time');
    $data= get_option('Clickmeeting_data', 'data');
    $from= get_option('Clickmeeting_from', 'from');
    $subject= get_option('Clickmeeting_subject', 'subject');
    $body_mail = get_option('Clickmeeting_body_mail', 'body_mail');
  if (isset($_POST['change-clicked'])) {
    update_option( 'Clickmeeting_room_id', $_POST['room_id'] );
	    $time =  update_option('Clickmeeting_time', $_POST['time']);
	    $data = update_option('Clickmeeting_data', $_POST['data']);
	    $from = update_option('Clickmeeting_from', $_POST['from']);
	    $subject = update_option('Clickmeeting_subject', $_POST['subject']);
    update_option( 'Clickmeeting_header', stripcslashes($_POST['header']) );
    update_option('Clickmeeting_body_mail', stripcslashes($_POST['body_mail']));
    $room_id = get_option('Clickmeeting_room_id', 'room_id');
    $header = get_option('Clickmeeting_header', 'header');
    $time = get_option('Clickmeeting_time', 'time');
    $data = get_option('Clickmeeting_data', 'data');
    $from = get_option('Clickmeeting_from', 'from');
    $subject = get_option('Clickmeeting_subject', 'subject');
    $body_mail = get_option('Clickmeeting_body_mail', 'body_mail');
  }


	?>
	<div class="wrap">
  <h1>ClickMeeting Registration</h1>
  
  <form action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="post">
  <table class="form-table">
<tbody>
<th scope="row"><label for="blogname2">Почта отправителя:</label></th>
<td><input type="text" value="<?php echo $from; ?>" id="blogname2" name="from" ></td>
</tr>
<tr>
<th scope="row"><label for="blogname3">Тема письма:</label></th>
<td><input type="text" value="<?php echo $subject; ?>" id="blogname3" name="subject" ></td>
</tr>

  <input name="change-clicked" type="hidden" value="1" /><br />
<tr>
<th scope="row"><label for="blogname5">Письмо на почту: Ссылка добавляеться в конец.</label></th>
<td><p>Ф.И.<b>[user_name]</b> Время<b>[time]</b> Название<b>[title]</b> ссылка<b>[url]</b></p>  <textarea name="body_mail" cols="100" rows="10" id="blogname5"><?php echo stripcslashes($body_mail); ?></textarea></td>
</tr>
<tr>
<th scope="row"></th>
<td>  <input type="submit" value="Сохранить" /></td>
</tr>
</tbody>
</table>
  </form>
</div>
<?php
}

function wptuts_get_the_ip() {
	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		return $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
		return $_SERVER["HTTP_CLIENT_IP"];
	}
	else {
		return $_SERVER["REMOTE_ADDR"];
	}
}
/**
 * Include plugin files
 */


function Webinar_registration_func($atts = false) {
	
	if($_POST['send']) {
		$error = '';
		if(!$_POST['rules_yes']) {
			$errors['down'] = 'Согласитесь с рассылкой.';
			return form($errors, 'yes');
		}
		if(!$error AND !$errors) {
			include_once ( plugin_dir_path( __FILE__ ) . 'ClickMeetingRestClient.php' );
			$api_key = 'us328e18c21541e65d819e2c738dd7f6b7356a5b89';
				$room_id = get_option('Clickmeeting_room_id');
					try {
					    $client = new ClickMeetingRestClient(array('api_key' => $api_key));
					    $conference = $client->conference($room_id);
					    if ($conference->registration->fields) {
						    foreach ($conference->registration->fields as $value) {
						    	if($value->id > 3) {
						    		$company_id = $value->id;
						    	}
						    }
						}
						    $params = array(
							    'registration' => array(
							        1 => $_POST['Name'],
							        2 => $_POST['famely'],
							        3 => $_POST['mail'],
							        $company_id => $_POST['company']
							    ),
						);
					    $registration = $client->addConferenceRegistration($room_id, $params);
					   
					}
					catch (Exception $e)
					{
					   $error = json_decode($e->getMessage());
					   
					   $errors = '';
					   foreach ($error->errors as $value) {
					   		if($value->name == 1) {
					   			$errors['name'] = 'Введите пожалуйста Имя';
					   		} else 	if($value->name == 2) {
					   			$errors['famely'] = 'Введите пожалуйста Фамилию';
					   		} else 	if($value->name == 3) {
					   			$errors['mail'] = 'Введите пожалуйста Почту';
					   		} else if ($value->name == 'VALIDATION_ERRORS') {
					   			$errors['mail'] = 'Адрес введен не верно';
					   		} else if($value->name == 'REGISTRATION_DISABLED')	 {
					   			$errors['down'] = 'Регистрация на вебинар закончена.';
					   		} else {
					   			$errors['company'] = 'Введите пожалуйста Компанию';
					   		}
					   }
					   //Начало
		     			return form($errors, 'yes');
					}

					if(!$errors) {
    $body_mail = get_option('Clickmeeting_body_mail');
    $from = get_option('Clickmeeting_from', 'from');
    $timess = get_option('Clickmeeting_time_post');
    $subject = get_option('Clickmeeting_subject');
    $namess = get_option('Clickmeeting_title');
    $urls = get_option('Clickmeeting_urls_post');
$headers = 'From: AmazingHiring <'.$from.'>' . "\r\n";
$healthy = array("[user_name]", "[time]", "[title]", "[url]");
$yummy   = array($_POST['famely'].' '.$_POST['Name'], $timess, $namess, $urls);
$body = str_replace($healthy, $yummy, $body_mail);
						wp_mail($_POST['mail'], $subject, $body, $headers);
						return good();
					}
		}
	} else {
		     return form($atts, 'yes');
	}
}

function good() {
	$time =  get_option('Clickmeeting_time_post');
	$name = get_option('Clickmeeting_name_post');
	$img = get_option('Clickmeeting_image_post');
	 $title = get_option('Clickmeeting_title');
	 if($_POST['post'] == 'yes') {
	 	$form = '<div class="reg-form">
			<div class="down-part"><div class="wpcf7-response-output wpcf7-display-none wpcf7-mail-sent-ok" style="display: block;" role="alert">Спасибо за вашу регистрацию! Ссылка на вебинар придет вам на почту.</div></div>';

		$form .= '</div>';
		return $form;
	}
	$form = '<div class="reg-form">
			<div class="up-part">
				<span class="form-name"><img src="http://amazinghiring.com/blog/wp-content/themes/amazinghiring/img/comp.jpg" alt=""> ВЕБИНАР</span>';
				$form .= '<p>'.$title.'<br></p>
				<div class="date">                                        
                    <div class="time">
    					<span>'.$time.'</span>
                    </div>
				</div>
<div class="person">
<div class="person-name">'.$name.'</div>
<div class="person-photo"><img src="'.$img .'" alt=""></div>
</div></div>';
				
	$form .= '
			<div class="down-part"><div class="wpcf7-response-output wpcf7-display-none wpcf7-mail-sent-ok" style="display: block;" role="alert">Спасибо за вашу регистрацию! Ссылка на вебинар придет вам на почту.</div>';

		$form .= '</div></div>';
		return $form;
}

function form($error, $post = false) {
	if(isset($error['post']) OR $_POST['post'] == 'yes') {
		$form = '<div class="form-wrap">
<form action="#" method="post" >
	<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <span class="wpcf7-form-control-wrap text-434">
        <input type="text" name="Name" value="'.$_POST['Name'].'" size="40" class="wpcf7-form-control wpcf7-text mdl-textfield__input" aria-invalid="false">
        <span role="alert" class="wpcf7-not-valid-tip">'.$error['name'].'</span>
    </span>
    <label class="mdl-textfield__label" for="sample3">Имя</label>
</div>
<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <span class="wpcf7-form-control-wrap text-434"><input type="text" name="famely" value="'.$_POST['famely'].'" size="40" class="wpcf7-form-control wpcf7-text mdl-textfield__input" aria-invalid="false">
                            <span role="alert" class="wpcf7-not-valid-tip">'.$error['famely'].'</span></span>
    <label class="mdl-textfield__label" for="sample3">Фамилия</label>
</div>
<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <span class="wpcf7-form-control-wrap email-896"><input type="email" name="mail" value="'.$_POST['mail'].'" size="40" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-email mdl-textfield__input" aria-invalid="false"><span role="alert" class="wpcf7-not-valid-tip">'.$error['mail'].'</span></span>
    <label class="mdl-textfield__label" for="sample3">Email</label>
</div>
<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
    <span class="wpcf7-form-control-wrap text-436"><input type="text" name="company" value="'.$_POST['company'].'" size="40" class="wpcf7-form-control wpcf7-text mdl-textfield__input" aria-invalid="false"><span role="alert" class="wpcf7-not-valid-tip">'.$error['company'].'</span></span>
    <label class="mdl-textfield__label" for="sample3">Название компании</label>
</div>
<div class="text-center">
    <span class="wpcf7-form-control-wrap checkbox-528"><span class="wpcf7-form-control wpcf7-checkbox fontawesome-checkbox"><span class="wpcf7-list-item first last"><input type="checkbox" name="rules_yes" value="Я согласен получить полезную рассылку от AmazingHiring" value="1" checked="checked">&nbsp;<span class="wpcf7-list-item-label">Я согласен получить полезную рассылку от AmazingHiring</span></span>
    </span>
    </span>
    <span role="alert" class="wpcf7-not-valid-tip">'.$error['down'].'</span>
    <input type="hidden" name="room_id" value="'.$room_id.'">
    <input type="hidden" name="post" value="yes">
    <input type="submit" name="send" value="Записаться" class="btn wpcf7-form-control wpcf7-submit mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent">
</div>
<div class="wpcf7-response-output wpcf7-display-none"></div>
</form></div>';
	} else {
			$args = array(
				'numberposts' => 1,
				'category'    => 0,
				'meta_key'    => '_active_zap',
				'meta_value'  => '1',
				'post_type'   => 'webinars',
			);

			$posts = get_posts( $args );
			
	if($posts) {
			$room_id = get_option('Clickmeeting_room_id', 'room_id');
			$time =  get_option('Clickmeeting_time_post');
			$name = get_option('Clickmeeting_name_post');
			$img = get_option('Clickmeeting_image_post');
			$title = get_option('Clickmeeting_title');
			$form = '<div class="reg-form">
					<div class="up-part">
						<span class="form-name">
							<div class="icon"><i class="material-icons">laptop</i></div>
							<div class="letters">ВЕБИНАР</div>
						</span>';
						$form .= '<p>'.$title.'<br></p>
						<div class="person">
							<div class="person-name">'.$name.'</div>
							<div class="person-photo"><img src="'.$img .'" alt=""></div>
						</div>
						<div class="date">		                                        
                            <div class="time">
	        					<span>'.$time.'</span>
                            </div>
						</div>';
			$form .= '</div>
					<div class="down-part">';
						

			$form .= '<form action="#" method="post">
					<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is-upgraded">
						<span class="wpcf7-form-control-wrap">
							<input type="text" class="wpcf7-form-control wpcf7-text mdl-textfield__input" id="name" name="Name" value="'.$_POST['Name'].'">
								<span role="alert" class="wpcf7-not-valid-tip">'.$error['name'].'</span>
						</span>
							<label class="mdl-textfield__label" for="id">Имя</label>
					</div>
					<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is-upgraded">
						<span class="wpcf7-form-control-wrap">
							<input type="text" class="wpcf7-form-control wpcf7-text mdl-textfield__input" name="famely" id="famely" value="'.$_POST['famely'].'">
								<span role="alert" class="wpcf7-not-valid-tip">'.$error['famely'].'</span>
						</span>
							<label class="mdl-textfield__label" for="famely">Фамилия</label>
					</div>
					<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is-upgraded">
						<span class="wpcf7-form-control-wrap">
							<input type="text" class="wpcf7-form-control wpcf7-text mdl-textfield__input" id="mail" name="mail" value="'.$_POST['mail'].'">
								<span role="alert" class="wpcf7-not-valid-tip">'.$error['mail'].'</span>
						</span>
							<label class="mdl-textfield__label" for="mail">Почта</label>
					</div>
					<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label is-upgraded">
						<span class="wpcf7-form-control-wrap">
							<input type="text" class="wpcf7-form-control wpcf7-text mdl-textfield__input" id="company" name="company" value="'.$_POST['company'].'">
								<span role="alert" class="wpcf7-not-valid-tip">'.$error['company'].'</span>
						</span>
							<label class="mdl-textfield__label" for="company">Компания</label>
					</div>
					    <span class="wpcf7-form-control-wrap checkbox-528"><span class="wpcf7-form-control wpcf7-checkbox fontawesome-checkbox"><span class="wpcf7-list-item first last"><input type="checkbox" name="rules_yes" value="Я согласен получить полезную рассылку от AmazingHiring" value="1" checked="checked">&nbsp;<span class="wpcf7-list-item-label">Я согласен получить полезную рассылку от AmazingHiring</span></span>
		    </span>
		    </span>
					<span role="alert" class="wpcf7-not-valid-tip">'.$error['down'].'</span>
				<input type="hidden" name="room_id" value="'.$room_id.'">
					<div>
					<input type="submit" name="send" value="Записаться" class="wpcf7-form-control wpcf7-submit btn btn-reg">
					</div>
				</form>';
				$form .= '</div></div>';
		}

		}
				return $form;
	
	
}

add_shortcode('Webinar_registration', 'Webinar_registration_func');
?>