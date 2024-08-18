<?php
/*
Template Name: CountriesTable
*/

defined('ABSPATH') || exit;
get_header();

do_action('before_custom_data_table');

global $wpdb;

$sql = "select p.ID as post_id, p.post_title as city_name, t.name as country_name
        from {$wpdb->terms} t 
        join {$wpdb->term_taxonomy} tx on t.term_id = tx.term_id 
        join {$wpdb->term_relationships} tr on tx.term_taxonomy_id = tr.term_taxonomy_id 
        join {$wpdb->posts} p on p.ID = tr.object_id
        where p.post_type = 'cities' and p.post_status = 'publish'";

$results = $wpdb->get_results($sql, ARRAY_A);
 
?>

<div id="custom-data-table-container">
    <input type="text" id="city-search" placeholder="Search for a city...">
    <table id="custom-data-table">
        <thead>
            <tr>
                <th>Country</th>
                <th>City</th>
                <th>Temperature</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($results)) : ?>
                <?php foreach ($results as $row) : ?>
                    <tr>
                        <td><?php echo esc_html($row['country_name']); ?></td>
                        <td><?php echo esc_html($row['city_name']); ?></td>
                        <td><?php echo esc_html(get_current_temperature($row['post_id'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr><td colspan="3">No cities found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
    function get_current_temperature($postId) {
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
    do_action('after_custom_data_table');

    get_footer();
?>
