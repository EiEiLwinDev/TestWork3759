<?php
function my_theme_enqueue_styles() {
    $parent_style = 'storefront-style'; //parent theme stylesheet

    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');


/** custom post */
function city_init() {
	$args = array( 
		'labels' 		=> array(
			'name' 			=>'Cities',
			'singular_name' => 'City',
			'add_new' 		=> 'Add New City',
			'add_new_item' 	=> 'New City',
			'edit_item'		=> 'Edit City',
			'search_items'	=> 'Search',
		),
		'public' 		=> true, 
		'show_ui' 		=> true, 
		'capability_type' => 'post', 
		'hierarchical' 	=> false, 
		'rewrite' 		=> array('slug' => 'city'), 
		'query_var' 	=> true, 
		'menu_icon' 	=> 'dashicons-video-alt', 
		'supports' 		=> array('title') 	
	); 
	register_post_type( 'cities', $args ); 
} 
add_action( 'init', 'city_init' );

function add_cities_meta_boxes() {
    add_meta_box(
        'cities_location_meta_box',
        'City Location',
        'display_cities_meta_box',
        'cities',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_cities_meta_boxes');

function display_cities_meta_box($post) {
    $latitude = get_post_meta($post->ID, 'latitude', true);
    $longitude = get_post_meta($post->ID, 'longitude', true);
    
    wp_nonce_field('cities_meta_box_nonce', 'meta_box_nonce');
    ?>
    <p>
        <label for="latitude">Latitude:</label>
        <input type="text" id="latitude" name="latitude" value="<?php echo esc_attr($latitude); ?>" />
    </p>
    <p>
        <label for="longitude">Longitude:</label>
        <input type="text" id="longitude" name="longitude" value="<?php echo esc_attr($longitude); ?>" />
    </p>
    <?php
}

function save_cities_meta_box_data($post_id) {
    if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'cities_meta_box_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['latitude'])) {
        update_post_meta($post_id, 'latitude', sanitize_text_field($_POST['latitude']));
    }
    if (isset($_POST['longitude'])) {
        update_post_meta($post_id, 'longitude', sanitize_text_field($_POST['longitude']));
    }
}

add_action('save_post', 'save_cities_meta_box_data');

/** custom taxonomy */
function register_country_taxonomy()
{
    $labels = array(
        'name'              => 'Countries',
        'singular_name'     => 'Country',
        'search_items'      => 'Search',
        'all_items'         => 'All countries',
        'parent_item'       => 'Parent country',
        'parent_item_colon' => 'Parent country:',
        'edit_item'         => 'Edit country',
        'update_item'       => 'Update country',
        'add_new_item'      => 'Add new country',
        'new_item_name'     => 'New country name',
        'menu_name'         => 'Countries',
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest' => true,
        'rewrite'    => array('slug' => 'Country'),
    );

    register_taxonomy('Countries', 'cities', $args); 
}

add_action('init', 'register_country_taxonomy');

/** custom widget */
class City_Weather_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'city_weather_widget',
            'City Weather Widget',
            array('description' => 'Displays a city and its current temperature.')
    	);
	}

    public function widget( $args, $instance ) {
        $city_id = ! empty( $instance['city_id'] ) ? $instance['city_id'] : '';
		
        if ( ! empty( $city_id ) ) {
            $city = get_post( $city_id );
            $city_name = $city ? $city->post_title : 'City does not found';
			$latitude = get_post_meta($city_id, 'latitude', true);
    		$longitude = get_post_meta($city_id, 'longitude', true);
            $temperature = $this->get_current_temperature( $latitude, $longitude );

            echo $args['before_widget'];
            echo $args['before_title'] . esc_html( $city_name ) . $args['after_title'];
            echo '<p>Current Temperature: ' . esc_html( $temperature ) . ' °C</p>';
            echo $args['after_widget'];
        }
    }

    public function form( $instance ) {
        $city_id = ! empty( $instance['city_id'] ) ? $instance['city_id'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'city_id' ); ?>"><?php _e( 'City:' ); ?></label>
            <select id="<?php echo $this->get_field_id( 'city_id' ); ?>" name="<?php echo $this->get_field_name( 'city_id' ); ?>">
                <?php
                $cities = get_posts( array( 'post_type' => 'cities', 'posts_per_page' => -1 ) );
                foreach ( $cities as $city ) {
                    echo '<option value="' . esc_attr( $city->ID ) . '"' . selected( $city_id, $city->ID, false ) . '>' . esc_html( $city->post_title ) . '</option>';
                }
                ?>
            </select>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['city_id'] = ! empty( $new_instance['city_id'] ) ? sanitize_text_field( $new_instance['city_id'] ) : '';

        return $instance;
    }

    private function get_current_temperature( $lat, $lon ) {
        $api_key = 'd4297d696bd7ae6cbef4a1c9b068644f';
		$response = wp_remote_get("https://api.openweathermap.org/data/2.5/weather?lat=".$lat."&lon=".$lon."&appid=". $api_key."&units=metric");
        if ( is_wp_error( $response ) ) {
            return 'Error fetching data';
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body );

        if ( isset( $data->main->temp ) ) {
            return $data->main->temp;
        } else {
            return 'N/A';
        }
    }
}

function register_city_weather_widget() {
    register_widget( 'City_Weather_Widget' );
}
add_action( 'widgets_init', 'register_city_weather_widget' );

function enqueue_custom_scripts() {
    wp_enqueue_script('custom-ajax-script', get_stylesheet_directory_uri() . '/custom-data-table.js', array('jquery'), null, true);

    wp_localize_script('custom-ajax-script', 'customDataTable', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');

function handle_search_city() {
    global $wpdb;
    $query = sanitize_text_field($_GET['query']);
	
    $sql = $wpdb->prepare("select p.ID as post_id, p.post_title as city_name, t.name as country_name
        from {$wpdb->terms} t 
        join {$wpdb->term_taxonomy} tx on t.term_id = tx.term_id 
        join {$wpdb->term_relationships} tr on tx.term_taxonomy_id = tr.term_taxonomy_id 
        join {$wpdb->posts} p on p.ID = tr.object_id
        where p.post_type = 'cities' and p.post_status = 'publish'
		and p.post_title like %s", '%' . $wpdb->esc_like($query) . '%');

	$results = $wpdb->get_results($sql, ARRAY_A);
	 

    if (!empty($results)) {
        foreach ($results as $row) {
			$temperature = get_dynamic_temperature($row['post_id']);
            echo "<tr>
                    <td>" . esc_html($row['country_name']) . "</td>
                    <td>" . esc_html($row['city_name']) . "</td>
                    <td>" . esc_html($temperature) . "</td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No cities found.</td></tr>";
    }

    wp_die();
}
add_action('wp_ajax_search_city', 'handle_search_city');
add_action('wp_ajax_nopriv_search_city', 'handle_search_city');

function get_dynamic_temperature($postId) {
	global $wpdb;
	$api_key = 'd4297d696bd7ae6cbef4a1c9b068644f';
	$sql = $wpdb->prepare("
            select meta_key, meta_value
            from {$wpdb->postmeta}
            where post_id = %d
        ", $postId); 

	$results = $wpdb->get_results($sql, ARRAY_A); 

	$lat = '';
	$lon = '';

	foreach ($results as $row) {
		if ($row['meta_key'] === 'latitude') {
			$lat = $row['meta_value'];
		} elseif ($row['meta_key'] === 'longitude') {
			$lon = $row['meta_value'];
		}
	}

	if (empty($lat) || empty($lon)) {
		return 'Latitude or longitude not set';
	} 
	$response = wp_remote_get("https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$api_key}&units=metric");

	if (is_wp_error($response)) {
		return 'Error fetching data';
	}
	$body = wp_remote_retrieve_body($response);
	$data = json_decode($body);

	if (isset($data->main->temp)) {
		return $data->main->temp . '°C';
	} else {
		return 'N/A';
	}
}
 
?>
