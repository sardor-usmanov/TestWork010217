<?php
function storefront_child_enqueue_styles()
{
    wp_enqueue_style('storefront-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('storefront-child-style', get_stylesheet_uri(), array('storefront-style'));
}

function storefront_child_enqueue_scripts()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('storefront-child-scripts', get_stylesheet_directory_uri() . '/js/script.js', array('jquery'), null, true);
    wp_localize_script('storefront-child-scripts', 'ajaxurl', admin_url('admin-ajax.php'));
}

add_action('wp_enqueue_scripts', 'storefront_child_enqueue_styles');
add_action('wp_enqueue_scripts', 'storefront_child_enqueue_scripts');


function create_cities_post_type()
{
    register_post_type('cities',
        array(
            'labels' => array(
                'name' => __('Cities'),
                'singular_name' => __('City')
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'custom-fields'),
            'taxonomies' => array('countries'),
        )
    );
}

add_action('init', 'create_cities_post_type');


function cities_add_meta_boxes()
{
    add_meta_box('cities_meta_box', 'City Details', 'cities_display_meta_box', 'cities', 'normal', 'high');
}

function cities_display_meta_box($post)
{
    $latitude = get_post_meta($post->ID, 'latitude', true);
    $longitude = get_post_meta($post->ID, 'longitude', true);
    ?>
    <label for="latitude">Latitude:</label>
    <input type="text" name="latitude" value="<?php echo esc_attr($latitude); ?>"/>
    <br/>
    <label for="longitude">Longitude:</label>
    <input type="text" name="longitude" value="<?php echo esc_attr($longitude); ?>"/>
    <?php
}

function cities_save_meta_box_data($post_id)
{
    if (array_key_exists('latitude', $_POST)) {
        update_post_meta($post_id, 'latitude', $_POST['latitude']);
    }
    if (array_key_exists('longitude', $_POST)) {
        update_post_meta($post_id, 'longitude', $_POST['longitude']);
    }
}

add_action('add_meta_boxes', 'cities_add_meta_boxes');
add_action('save_post', 'cities_save_meta_box_data');


function create_countries_taxonomy()
{
    register_taxonomy(
        'countries',
        'cities',
        array(
            'label' => __('Countries'),
            'rewrite' => array('slug' => 'countries'),
            'hierarchical' => true,
        )
    );
}

add_action('init', 'create_countries_taxonomy');


function ajax_city_search()
{
    global $wpdb;
    $search_query = esc_attr($_POST['search_query']);

    $results = $wpdb->get_results($wpdb->prepare("
        SELECT p.post_title, pm_lat.meta_value as latitude, pm_lon.meta_value as longitude, t.name as country
        FROM $wpdb->posts p
        JOIN $wpdb->postmeta pm_lat ON p.ID = pm_lat.post_id AND pm_lat.meta_key = 'latitude'
        JOIN $wpdb->postmeta pm_lon ON p.ID = pm_lon.post_id AND pm_lon.meta_key = 'longitude'
        JOIN $wpdb->term_relationships tr ON p.ID = tr.object_id
        JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
        JOIN $wpdb->terms t ON tt.term_id = t.term_id
        WHERE p.post_type = 'cities' AND p.post_title LIKE %s
    ", '%' . $search_query . '%'));

    wp_send_json($results);
}

add_action('wp_ajax_nopriv_city_search', 'ajax_city_search');
add_action('wp_ajax_city_search', 'ajax_city_search');
?>