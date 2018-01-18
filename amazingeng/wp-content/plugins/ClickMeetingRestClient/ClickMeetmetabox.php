<?php

/* Добавляем блоки в основную колонку на страницах постов и пост. страниц */
function myplugin_add_custom_box() {
		add_meta_box( 'myplugin_sectionid', 'Вебинар поста', 'myplugin_meta_box_callback', 'webinars' );
}
add_action('add_meta_boxes', 'myplugin_add_custom_box');

/* HTML код блока */
function myplugin_meta_box_callback() {
	// Используем nonce для верификации
	wp_nonce_field( plugin_basename(__FILE__), 'myplugin_noncename' );

	$api_key = 'us328e18c21541e65d819e2c738dd7f6b7356a5b89';
	$room_id = get_post_meta( get_the_ID(), '_webinar', true);
					try {
					    $client = new ClickMeetingRestClient(array('api_key' => $api_key));
						   $conferences = $client->conferences('active');
						   
						   $all_conf = '<option value="0">Записи нет</option>';
						   foreach ($conferences as $value) {
						   		if($value->id == $room_id) {
						   			$all_conf .= '<option selected value="'.$value->id.'">'.$value->name.'</option>';
						   		} else {
						   			$all_conf .= '<option value="'.$value->id.'">'.$value->name.'</option>';
						   		}
						   }
					   
					}
					catch (Exception $e)
					{
					   $error = json_decode($e->getMessage());
					   //Начало
		    	}
	// Поля формы для введения данных
	echo '<label for="myplugin_new_field">Выберите вебинар</label> ';
	echo '<select id="room_id" name="room_id">'.$all_conf.'</select>';
		if($room_id == 0) {
			$class = 'display:none;';
		$time =  '';
	    $name = '';
	    $img = '';
		} else {
		$time =  get_option('Clickmeeting_time_post');
	    $name = get_option('Clickmeeting_name_post');
	    $img = get_option('Clickmeeting_image_post');
	        $urls = get_option('Clickmeeting_urls_post');
		}
	echo '<div id="dop_web" style="'.$class.'">';
	echo '<label for="myplugin_new_field2">Время вебинара</label> ';
	echo '<input name="change-clicked2" id="myplugin_new_field2" type="text" style="    width: 350px;" value="'.$time.'" /><br>';
	echo '<label for="myplugin_new_field3">Имена</label> ';
	echo '<input name="change-clicked3" id="myplugin_new_field3" type="text" style="    width: 350px;" value="'.$name.'" /><br>';
	echo '<label for="myplugin_new_field4">Ссылка на картинку</label> ';
	echo '<input name="change-clicked4" id="myplugin_new_field4" type="text" style="    width: 350px;" value="'.$img.'" /><br>';
	echo '<label for="myplugin_new_field5">Ссылка на Вебинар</label> ';
	echo '<input name="change-clicked5" id="myplugin_new_field5" type="text" style="    width: 350px;" value="'.$urls.'" /><br>';
	echo "</div>";
	?>
<script type="text/javascript">
	jQuery('#room_id').change(function() {
		if(jQuery(this).find('option:selected').val() > 0) {
			jQuery('#dop_web').fadeIn();
		} else {
			jQuery('#dop_web').fadeOut();
		}
	});
</script>
	<?php
}

/* Сохраняем данные, когда пост сохраняется */
function myplugin_save_postdata( $post_id ) {
	// проверяем, если это автосохранение ничего не делаем с данными нашей формы.
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
		return $post_id;

	// проверяем разрешено ли пользователю указывать эти данные
	if ( 'page' == $_POST['post_type'] && ! current_user_can( 'edit_page', $post_id ) ) {
		  return $post_id;
	} elseif( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	// Убедимся что поле установлено.
	if ( ! isset( $_POST['room_id'] ) )
		return;

	// Все ОК. Теперь, нужно найти и сохранить данные
	// Очищаем значение поля input.
	$my_data = sanitize_text_field( $_POST['room_id'] );
	// Обновляем данные в базе данных.
	if($my_data != 0) {
$args = array(
	'numberposts' => 1,
	'category'    => 0,
	'meta_key'    => '_active_zap',
	'meta_value'  => '1',
	'post_type'   => 'webinars',
);

$posts = get_posts( $args );

foreach($posts as $post){ setup_postdata($post);
	update_post_meta( $post->ID, '_active_zap', '0' );
	update_post_meta( $post->ID, '_webinar', '0' );
}
		wp_reset_postdata(); // сброс
		update_post_meta( $post_id, '_active_zap', '1' );
		update_post_meta( $post_id, '_webinar', $my_data );
		update_option('Clickmeeting_room_id', $my_data);
	    update_option('Clickmeeting_time_post', $_POST['change-clicked2']);
	    update_option('Clickmeeting_name_post', $_POST['change-clicked3']);
	    update_option('Clickmeeting_image_post', $_POST['change-clicked4']);
	    update_option('Clickmeeting_title', $_POST['post_title']);
	   update_option('Clickmeeting_urls_post', $_POST['change-clicked5']);
	} else {
		update_post_meta( $post_id, '_active_zap', '0' );
		update_post_meta( $post_id, '_webinar', '0' );
	}
	
}
add_action( 'save_post', 'myplugin_save_postdata' );



function Webinar_post_top() {
$args = array(
	'numberposts' => 1,
	'category'    => 0,
	'meta_key'    => '_active_zap',
	'meta_value'  => '1',
	'post_type'   => 'webinars',
);

$posts = get_posts( $args );
foreach($posts as $post){ setup_postdata($post);
	$post_good = $post;
}
if($post_good->post_title) {
		$time =  get_option('Clickmeeting_time_post');
	    $name = get_option('Clickmeeting_name_post');
	    $img = get_option('Clickmeeting_image_post');
	    
	    //$url = the_permalink( $post_good->ID);
	return '<div class="reg-form same-div" style="min-height: 473px;">
				<div class="up-part">
					<span class="form-name"><img src="http://amazinghiring.com/blog/wp-content/themes/amazinghiring/img/comp.jpg" alt=""> вебинар</span><p> '.$post_good->post_title.' <br></p>
					<div class="date-time-wrapper">
						<div class="date">		                    
		                    <div class="time">
		    					<span>'.$time.'</span>
		                    </div>
						</div>
						<div class="person">
							<div class="person-name">'.$name.'</div>
							<div class="person-photo"><img src="'.$img.'" alt=""></div>
						</div>						
					</div>
					<div class="link">
						<a href="'.get_permalink($post_good->ID).'" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--accent">Записаться</a>
					</div>
				</div>
			</div>';
		} else {
			return '';
		}
}

add_shortcode('Webinar_post_top', 'Webinar_post_top');
?>