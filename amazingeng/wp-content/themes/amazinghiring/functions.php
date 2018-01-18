<?php
	add_theme_support( 'post-thumbnails' ); // для всех типов постов

	add_theme_support('menus');

	/*
	//отключение расстановки тегов параграфов start 
	remove_filter('the_content', 'wpautop');     //записи
	remove_filter('the_excerpt', 'wpautop');     //цитаты
	remove_filter('comment_text', 'wpautop');    //комментарии
	//отключение расстановки тегов параграфов end
	*/

	remove_action('wp_head','adjacent_posts_rel_link_wp_head');
	remove_action('wp_head','feed_links_extra', 3);

	// обрезка анонсов
	function the_truncated_post($symbol_amount) {
		$filtered = strip_tags( preg_replace('@<style[^>]*?>.*?</style>@si', '', preg_replace('@<script[^>]*?>.*?</script>@si', '', apply_filters('the_content', get_the_content()))) );
		echo substr($filtered, 0, strrpos(substr($filtered, 0, $symbol_amount), ' ')) . '...';
	}

	function wp_corenavi() {
		global $wp_query;
		$pages = '';
		$max = $wp_query->max_num_pages;
		if (!$current = get_query_var('paged')) $current = 1;
		$a['base'] = str_replace(999999999, '%#%', get_pagenum_link(999999999));
		$a['total'] = $max;
		$a['current'] = $current;

		$total = 1; //1 - выводить текст "Страница N из N", 0 - не выводить
		$a['mid_size'] = 3; //сколько ссылок показывать слева и справа от текущей
		$a['end_size'] = 1; //сколько ссылок показывать в начале и в конце
		$a['prev_text'] = '<i class="fa fa-angle-double-left"></i>'; //текст ссылки "Предыдущая страница"
		$a['next_text'] = '<i class="fa fa-angle-double-right"></i>'; //текст ссылки "Следующая страница"

		if ($max > 1) echo '<div class="pager">';
		if ($total == 1 && $max > 1) $pages = '<span class="pages">Страница ' . $current . ' из ' . $max . '</span>'."\r\n";
		echo paginate_links($a);
		if ($max > 1) echo '</div>';
	}

	function my_category_order($orderby, $args)
	{
	    if($args['orderby'] == 'sort')
	        return 't.sort';
	    else
	        return $orderby;
	}

	function res_fromemail($email) { return 'no-reply@amazinghiring.com'; }
    function res_fromname($name){ return 'amazinghiring'; }
    add_filter('wp_mail_from', 'res_fromemail');
    add_filter('wp_mail_from_name', 'res_fromname');


	if ( ! function_exists( 'materials_section' ) ) {
 
		// Опишем требуемый функционал
	    function materials_section() {
	 
	        $labels = array(
	            'name'                => _x( 'Материалы', 'Post Type General Name', 'materials_web' ),
	            'singular_name'       => _x( 'Материалы', 'Post Type Singular Name', 'materials_web' ),
	            'menu_name'           => __( 'Материалы', 'materials_web' ),
	            'parent_item_colon'   => __( 'Родительский:', 'materials_web' ),
	            'all_items'           => __( 'Все записи', 'materials_web' ),
	            'view_item'           => __( 'Просмотреть', 'materials_web' ),
	            'add_new_item'        => __( 'Добавить новую запись', 'materials_web' ),
	            'add_new'             => __( 'Добавить', 'materials_web' ),
	            'edit_item'           => __( 'Редактировать запись', 'materials_web' ),
	            'update_item'         => __( 'Обновить запись', 'materials_web' ),
	            'search_items'        => __( 'Найти запись', 'materials_web' ),
	            'not_found'           => __( 'Не найдено', 'materials_web' ),
	            'not_found_in_trash'  => __( 'Не найдено в корзине', 'materials_web' ),
	        );
	        $args = array(
	            'labels'              => $labels,
	            'supports'            => array( 'title', 'editor', 'excerpt', ),
	            'taxonomies'          => array( 'materials_web_tax' ), // категории, которые мы создадим ниже
	            'public'              => true,
	            'menu_position'       => 5,
	            'menu_icon'           => 'dashicons-book',
	        );
	        register_post_type( 'materials_web', $args );
	 
	    }	    
	 
	    add_action( 'init', 'materials_section', 0 ); // инициализируем

	    function materials_meta_box() {  
		    add_meta_box(  
		        'materials_meta_box', // Идентификатор(id)
		        'Дополнительная информация', // Заголовок области с мета-полями(title)
		        'show_my_material_metabox', // Вызов(callback)
		        'materials_web', // Где будет отображаться наше поле
		        'normal',
		        'high'
	        );

	        
		}

		add_action('add_meta_boxes', 'materials_meta_box'); // Запускаем функцию

		$material_meta_fields = array(
		    array(  
		        'label' => 'ID файла',  
		        'desc'  => 'download_id="X"',
		        'id'    => 'ml_file_id', // даем идентификатор.
		        'type'  => 'text'  // Указываем тип поля.
		    ),
		    array(
			    'name'  => 'Image',
			    'label' => 'Изображение',
			    'desc'  => '',
			    'id'    => 'image',
			    'type'  => 'image'
			),
		);

		if(is_admin()) {		    
		    wp_enqueue_script('imagefield', get_template_directory_uri().'/imagefield.js');
		    wp_enqueue_style('jquery-ui-custom', get_template_directory_uri().'/inc/jquery-ui.css');
		}

		function show_my_material_metabox() {  
		global $material_meta_fields; // Обозначим наш массив с полями глобальным
		global $post;  // Глобальный $post для получения id создаваемого/редактируемого поста
		// Выводим скрытый input, для верификации. Безопасность прежде всего!
		echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';  
		 
		    // Начинаем выводить таблицу с полями через цикл
		    echo '<table class="form-table">';  
		    foreach ($material_meta_fields as $field) {  
		        // Получаем значение если оно есть для этого поля
		        $meta = get_post_meta($post->ID, $field['id'], true);  
		        // Начинаем выводить таблицу
		        echo '<tr>
		                <th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
		                <td>';  
		                switch($field['type']) {  
		                    // Текстовое поле
							case 'text':  
							    echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
							        <br /><span class="description">'.$field['desc'].'</span>';  
							break;
							// Список
							case 'select':  
							    echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';  
							    foreach ($field['options'] as $option) {  
							        echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';  
							    }  
							    echo '</select><br /><span class="description">'.$field['desc'].'</span>';  
							break;

							case 'image':
							    $image = get_template_directory_uri().'/images/image.png'; 
							    echo '<span class="custom_default_image" style="display:none">'.$image.'</span>';
							    if ($meta) { $image = wp_get_attachment_image_src($meta, 'medium'); $image = $image[0]; }              
							    echo    '<input name="'.$field['id'].'" type="hidden" class="custom_upload_image" value="'.$meta.'" />
							                <img src="'.$image.'" class="custom_preview_image" alt="" /><br />
							                    <input class="custom_upload_image_button button" type="button" value="Выберите изображение" />
							                    <small> <a href="#" class="custom_clear_image_button">Убрать изображение</a></small>
							                    <br clear="all" /><span class="description">'.$field['desc'].'</span>';
							break;
		                }
		        echo '</td></tr>';  
		    }  
		    echo '</table>';
		}				

		function save_my_material_meta_fields($post_id) {  
		    global $material_meta_fields;  // Массив с нашими полями
		 
		    // проверяем наш проверочный код
		    if (!wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__)))  
		        return $post_id;  
		    // Проверяем авто-сохранение
		    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)  
		        return $post_id;  
		    // Проверяем права доступа  
		    if ('materials_web' == $_POST['post_type']) {  
		        if (!current_user_can('edit_page', $post_id))  
		            return $post_id;  
		        } elseif (!current_user_can('edit_post', $post_id)) {  
		            return $post_id;  
		    }  
		 
		    // Если все отлично, прогоняем массив через foreach
		    foreach ($material_meta_fields as $field) {  
		        $old = get_post_meta($post_id, $field['id'], true); // Получаем старые данные (если они есть), для сверки
		        $new = $_POST[$field['id']];  
		        if ($new && $new != $old) {  // Если данные новые
		            update_post_meta($post_id, $field['id'], $new); // Обновляем данные
		        } elseif ('' == $new && $old) {  
		            delete_post_meta($post_id, $field['id'], $old); // Если данных нету, удаляем мету.
		        }  
		    } // end foreach  
		}  
		add_action('save_post', 'save_my_material_meta_fields'); // Запускаем функцию сохранения
 
	}

	/*if ( ! function_exists( 'materials_section_tax' ) ) {
 	
	    function materials_section_tax() {
	 
			$labels = array(
	            'name'                => _x( 'Категории', 'Taxonomy General Name', 'materials_web' ),
	            'singular_name'       => _x( 'Категория', 'Taxonomy Singular Name', 'materials_web' ),
	            'menu_name'           => __( 'Категории', 'materials_web' ),
	            'all_items'           => __( 'Категории', 'materials_web' ),
	            'parent_item'           => __( 'Родительская категория', 'materials_web' ),
	            'parent_item_colon'        => __( 'Родительская категория', 'materials_web' ),
	            'new_item_name' => __('Новая категория','materials_web'),
	            'add_new_item'             => __( 'Добавить новую категорию', 'materials_web' ),
	            'edit_item'           => __( 'Редактировать категорию', 'materials_web' ),
	            'update_item'         => __( 'Обновить категорию', 'materials_web' ),
	            'search_items'        => __( 'Найти категорию', 'materials_web' ),
	            'add_or_remove_items' => __( 'Добавить или удалить категорию', 'materials_web'),
	            'choose_from_most_used' => __('Поиск среди популярных', 'materials_web'),
	            'not_found'           => __( 'Не найдено', 'materials_web' ),	            
	        );
	        $args = array(
	            'labels'                     => $labels,
	            'hierarchical'               => true,
	            'public'                     => true,
	        );
	        register_taxonomy( 'materials_section_tax', array( 'materials_web' ), $args );
	 
	    }
	 
	    add_action( 'init', 'materials_section_tax', 0 ); // инициализируем
	 
	}*/


	if ( ! function_exists( 'webinars_section' ) ) {
 
		// Опишем требуемый функционал
	    function webinars_section() {
	 
	        $labels = array(
	            'name'                => _x( 'Вебинары', 'Post Type General Name', 'webinars' ),
	            'singular_name'       => _x( 'Вебинары', 'Post Type Singular Name', 'webinars' ),
	            'menu_name'           => __( 'Вебинары', 'webinars' ),
	            'parent_item_colon'   => __( 'Родительский:', 'webinars' ),
	            'all_items'           => __( 'Все записи', 'webinars' ),
	            'view_item'           => __( 'Просмотреть', 'webinars' ),
	            'add_new_item'        => __( 'Добавить новую запись', 'webinars' ),
	            'add_new'             => __( 'Добавить', 'webinars' ),
	            'edit_item'           => __( 'Редактировать запись', 'webinars' ),
	            'update_item'         => __( 'Обновить запись', 'webinars' ),
	            'search_items'        => __( 'Найти запись', 'webinars' ),
	            'not_found'           => __( 'Не найдено', 'webinars' ),
	            'not_found_in_trash'  => __( 'Не найдено в корзине', 'webinars' ),
	        );
	        $args = array(
	            'labels'              => $labels,
	            'supports'            => array( 'title', 'editor', 'excerpt', ),
	            'taxonomies'          => array( 'webinars_tax' ), // категории, которые мы создадим ниже
	            'public'              => true,
	            'menu_position'       => 6,
	            'menu_icon'           => 'dashicons-book',
	        );
	        register_post_type( 'webinars', $args );
	 
	    }

	    function webinars_meta_box() {  
		    add_meta_box(  
		        'webinars_meta_box', // Идентификатор(id)
		        'Дополнительная информация', // Заголовок области с мета-полями(title)
		        'show_my_webinars_metabox', // Вызов(callback)
		        'webinars', // Где будет отображаться наше поле
		        'normal',
		        'high'
	        );

	        
		}

		add_action('add_meta_boxes', 'webinars_meta_box'); // Запускаем функцию

		$webinars_meta_fields = array(
		    array(  
		        'label' => 'ID файла',  
		        'desc'  => 'download_id="X"',
		        'id'    => 'wb_file_id', // даем идентификатор.
		        'type'  => 'text'  // Указываем тип поля.
		    ),
                    array(  
                           'label' => 'Ссылка на ютуб',  
                            'desc'  => 'Введите ссылку на запись вебинара на ютубе',  
                            'id'    => 'youtube_link', // даем идентификатор.
                            'type'  => 'text'  // Указываем тип поля.
                    ),  
		    array(
			    'name'  => 'Image',
			    'label' => 'Изображение поста',
			    'desc'  => '',
			    'id'    => 'image',
			    'type'  => 'image'
			),
		);

		if(is_admin()) {		    
		    wp_enqueue_script('imagefield', get_template_directory_uri().'/imagefield.js');
		    wp_enqueue_style('jquery-ui-custom', get_template_directory_uri().'/inc/jquery-ui.css');
		}

		function show_my_webinars_metabox() {  
		global $webinars_meta_fields; // Обозначим наш массив с полями глобальным
		global $post;  // Глобальный $post для получения id создаваемого/редактируемого поста
		// Выводим скрытый input, для верификации. Безопасность прежде всего!
		echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';  
		 
		    // Начинаем выводить таблицу с полями через цикл
		    echo '<table class="form-table">';  
		    foreach ($webinars_meta_fields as $field) {  
		        // Получаем значение если оно есть для этого поля
		        $meta = get_post_meta($post->ID, $field['id'], true);  
		        // Начинаем выводить таблицу
		        echo '<tr>
		                <th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
		                <td>';  
		                switch($field['type']) {  
		                    // Текстовое поле
							case 'text':  
							    echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
							        <br /><span class="description">'.$field['desc'].'</span>';  
							break;
							// Список
							case 'select':  
							    echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';  
							    foreach ($field['options'] as $option) {  
							        echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';  
							    }  
							    echo '</select><br /><span class="description">'.$field['desc'].'</span>';  
							break;

							case 'image':
							    $image = get_template_directory_uri().'/images/image.png'; 
							    echo '<span class="custom_default_image" style="display:none">'.$image.'</span>';
							    if ($meta) { $image = wp_get_attachment_image_src($meta, 'medium'); $image = $image[0]; }              
							    echo    '<input name="'.$field['id'].'" type="hidden" class="custom_upload_image" value="'.$meta.'" />
							                <img src="'.$image.'" class="custom_preview_image" alt="" /><br />
							                    <input class="custom_upload_image_button button" type="button" value="Выберите изображение" />
							                    <small> <a href="#" class="custom_clear_image_button">Убрать изображение</a></small>
							                    <br clear="all" /><span class="description">'.$field['desc'].'</span>';
							break;
		                }
		        echo '</td></tr>';  
		    }  
		    echo '</table>';
		}				

		function save_my_webinars_meta_fields($post_id) {  
		    global $webinars_meta_fields;  // Массив с нашими полями
		 
		    // проверяем наш проверочный код
		    if (!wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__)))  
		        return $post_id;  
		    // Проверяем авто-сохранение
		    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)  
		        return $post_id;  
		    // Проверяем права доступа  
		    if ('webinars' == $_POST['post_type']) {  
		        if (!current_user_can('edit_page', $post_id))  
		            return $post_id;  
		        } elseif (!current_user_can('edit_post', $post_id)) {  
		            return $post_id;  
		    }  
		 
		    // Если все отлично, прогоняем массив через foreach
		    foreach ($webinars_meta_fields as $field) {  
		        $old = get_post_meta($post_id, $field['id'], true); // Получаем старые данные (если они есть), для сверки
		        $new = $_POST[$field['id']];  
		        if ($new && $new != $old) {  // Если данные новые
		            update_post_meta($post_id, $field['id'], $new); // Обновляем данные
		        } elseif ('' == $new && $old) {  
		            delete_post_meta($post_id, $field['id'], $old); // Если данных нету, удаляем мету.
		        }  
		    } // end foreach  
		}  
		add_action('save_post', 'save_my_webinars_meta_fields'); // Запускаем функцию сохранения
	 
	    add_action( 'init', 'webinars_section', 0 ); // инициализируем
 
	}	

	/**/
	 
	add_filter('get_terms_orderby', 'my_category_order', 10, 2);
?>