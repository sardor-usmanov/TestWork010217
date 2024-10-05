<?php
/*
Template Name: Cities Table
*/

get_header();

global $wpdb;

$cities_data = $wpdb->get_results("
    SELECT p.ID, p.post_title, pm_lat.meta_value as latitude, pm_lon.meta_value as longitude, t.name as country
    FROM $wpdb->posts p
    JOIN $wpdb->postmeta pm_lat ON p.ID = pm_lat.post_id AND pm_lat.meta_key = 'latitude'
    JOIN $wpdb->postmeta pm_lon ON p.ID = pm_lon.post_id AND pm_lon.meta_key = 'longitude'
    JOIN $wpdb->term_relationships tr ON p.ID = tr.object_id
    JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
    JOIN $wpdb->terms t ON tt.term_id = t.term_id
    WHERE p.post_type = 'cities'
");

?>

<div>
    <h2>City Search</h2>
    <input type="text" id="city-search" placeholder="Search cities...">
    <div id="cities-table">
        <table>
            <thead>
            <tr>
                <th>City</th>
                <th>Country</th>
                <th>Latitude</th>
                <th>Longitude</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($cities_data as $city): ?>
                <tr>
                    <td><?php echo esc_html($city->post_title); ?></td>
                    <td><?php echo esc_html($city->country); ?></td>
                    <td><?php echo esc_html($city->latitude); ?></td>
                    <td><?php echo esc_html($city->longitude); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
get_footer();
?>
