<?php
/*
Plugin Name: Videos Category Filter
Description: Plugin to filter video posts by category with AJAX.
Version: 1.0
Author: https://github.com/Zenithcoder/
*/

// Enqueue plugin scripts and styles
function tdn_enqueue_scripts()
{
    // Enqueue jQuery
    wp_enqueue_script('jquery');

    // Enqueue custom JavaScript
    wp_enqueue_script(
        'tdn-custom-scripts',
        plugin_dir_url(__FILE__) . 'js/custom-scripts.js',
        array('jquery'),
        '1.0',
        true
    );

    // Localize the AJAX URL
    wp_localize_script('tdn-custom-scripts', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

    // Enqueue custom CSS
    wp_enqueue_style('tdn-custom-styles', plugin_dir_url(__FILE__) . 'css/custom-styles.css');
}

add_action('wp_enqueue_scripts', 'tdn_enqueue_scripts');

// AJAX category filter handler
function tdn_filter_posts_ajax()
{
    try {
        $category = isset($_POST['category']) ? $_POST['category'] : 'all';

        $args = array(
            'post_type' => 'video',
            'posts_per_page' => -1
        );

        // Check if it's the initial load (category is 'all')
        if ($category != 'all') {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'video_cat', // Adjusted to match your taxonomy key
                    'field' => 'slug',
                    'terms' => $category
                )
            );
        }

        $posts = new WP_Query($args);

        ob_start(); // Start output buffering

        if ($posts->have_posts()) :
            ?>
            <div class="tdn-custom-posts-grid">
                <?php
                while ($posts->have_posts()) : $posts->the_post();
                    ?>
                    <div class="tdn-custom-post">
                        <div class="tdn-custom-post-thumbnail">
                            <?php
                            // Check if the featured image is set
                            if (has_post_thumbnail()) {
                                echo '<a href="' . get_the_permalink() . '">' . get_the_post_thumbnail(
                                        null,
                                        'thumbnail',
                                        array('class' => 'tdn-custom-thumbnail-image')
                                    ) . '</a>'; // Wrap the thumbnail with anchor tag
                            } else {
                                // Get the image from the ACF custom field
                                $featured_image = get_field('featured_image');
                                if (!empty($featured_image)) {
                                    echo '<a href="' . get_the_permalink() . '"><img src="' . esc_url(
                                            $featured_image['url']
                                        ) . '" alt="' . esc_attr(
                                            $featured_image['alt']
                                        ) . '" class="tdn-custom-thumbnail-image"></a>'; // Wrap the thumbnail with anchor tag
                                }
                            }
                            ?>
                        </div>
                        <div class="tdn-custom-post-content">
                            <span class="tdn-custom-post-title"><a href="<?php
                                the_permalink(); ?>"><?php
                                    the_title(); ?></a></span>
                            <a href="<?php
                            the_permalink(); ?>" class="tdn-custom-post-link">
                                <?php
                                // Get the short description from the ACF custom field
                                $short_description = get_field('short_description');
                                if (!empty($short_description)) {
                                    echo '<p class="tdn-custom-post-description">' . esc_html(
                                            $short_description
                                        ) . '</p>';
                                }
                                ?>
                            </a>
                        </div>
                    </div>
                <?php
                endwhile;
                ?>
            </div>
        <?php
        else :
            echo 'No posts found for category: ' . $category;
        endif;

        wp_reset_postdata();

        echo ob_get_clean(); // Output the buffered content and clean the buffer
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }

    die();
}

add_action('wp_ajax_filter_posts', 'tdn_filter_posts_ajax');
add_action('wp_ajax_nopriv_filter_posts', 'tdn_filter_posts_ajax');

// Shortcode for displaying the category filter and posts
function tdn_filter_posts_shortcode($atts)
{
    $atts = shortcode_atts(array(
        'post_type' => 'video',
    ), $atts);

    $args = array(
        'taxonomy' => 'video_cat',
        'hide_empty' => false,
    );

    $categories = get_categories($args);

    ob_start(); // Start output buffering
    ?>
    <div class="tdn-custom-category-buttons">
        <?php
        foreach ($categories as $category) {
            echo '<button class="tdn-custom-category-button" data-category="' . $category->slug . '">' . $category->name . '</button>';
        }
        ?>
    </div>
    <div class="tdn-custom-posts-container">
        <?php
        // Display posts on first load
        $initial_posts_args = array(
            'post_type' => 'video',
            'posts_per_page' => -1
        );
        $initial_posts_query = new WP_Query($initial_posts_args);
        if ($initial_posts_query->have_posts()) : ?>
            <div class="tdn-custom-posts-grid">
                <?php
                while ($initial_posts_query->have_posts()) : $initial_posts_query->the_post(); ?>
                    <div class="tdn-custom-post">
                        <div class="tdn-custom-post-thumbnail">
                            <?php
                            // Check if the featured image is set
                            if (has_post_thumbnail()) {
                                echo '<a href="' . get_the_permalink() . '">' . get_the_post_thumbnail(
                                        null,
                                        'thumbnail',
                                        array('class' => 'tdn-custom-thumbnail-image')
                                    ) . '</a>'; // Wrap the thumbnail with anchor tag
                            } else {
                                // Get the image from the ACF custom field
                                $featured_image = get_field('featured_image');
                                if (!empty($featured_image)) {
                                    echo '<a href="' . get_the_permalink() . '"><img src="' . esc_url(
                                            $featured_image['url']
                                        ) . '" alt="' . esc_attr(
                                            $featured_image['alt']
                                        ) . '" class="tdn-custom-thumbnail-image"></a>'; // Wrap the thumbnail with anchor tag
                                }
                            }
                            ?>
                        </div>
                        <div class="tdn-custom-post-content">
                            <span class="tdn-custom-post-title"><a href="<?php
                                the_permalink(); ?>"><?php
                                    the_title(); ?></a></span>
                            <a href="<?php
                            the_permalink(); ?>" class="tdn-custom-post-link">
                                <?php
                                // Get the short description from the ACF custom field
                                $short_description = get_field('short_description');
                                if (!empty($short_description)) {
                                    echo '<p class="tdn-custom-post-description">' . esc_html(
                                            $short_description
                                        ) . '</p>';
                                }
                                ?>
                            </a>
                        </div>
                    </div>
                <?php
                endwhile; ?>
            </div>
        <?php
        else : ?>
            <p>No posts found</p>
        <?php
        endif;
        wp_reset_postdata(); ?>
    </div>
    <?php
    return ob_get_clean(); // Return the buffered output
}

add_shortcode('videos_category_filter', 'tdn_filter_posts_shortcode');
?>