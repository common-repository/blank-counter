<?php
/*
  Plugin Name: Themerev's Blank Counter
  Plugin URI: http://themerev.net/blank-counter-a-wordpress-plugin/
  Description: Plugin that makes it possible to track outgoing links.
  Version: 1.0.3
  Author: Byzantine Media Ltd
  Author URI: http://byzantine.no
  License: GPLv2
 */

add_action('wp_head', 'blankcounter_ajaxurl');

function blankcounter_ajaxurl() {
            ?>
            <script type="text/javascript">
                        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
            </script>
            <?php
}

wp_enqueue_script("jquery");
wp_enqueue_script('byzantine_blankcounter_script', plugins_url('/script.js', __FILE__));

add_action('admin_menu', 'byzantine_blankcounter_plugin_menu');

add_action('admin_menu', 'byzantine_blankcounter_admin_menu');

function byzantine_blankcounter_admin_menu() {
            if (isset($_GET['page']) && $_GET['page'] == 'byzantine_blank_counter') {
                        wp_register_style('admin-style', plugins_url('/css/admin.css', __FILE__));
                        wp_enqueue_style('admin-style');
                        wp_register_style('jquery-ui-blankcounter', plugins_url('/css/ui-lightness/jquery-ui-1.8.21.custom.css', __FILE__));
                        wp_enqueue_style('jquery-ui-blankcounter');
                        wp_enqueue_script('byzantine_blankcounter_jquery-ui', plugins_url('js/jquery-ui-1.8.21.custom.min.js', __FILE__), array('jquery'));
                        wp_enqueue_script('byzantine_blankcounter_tabs', plugins_url('/js/admin.js', __FILE__));
            }
}

function byzantine_blankcounter_plugin_menu() {
            add_options_page('Blank Counter', 'Blank Counter', 'manage_options', 'byzantine_blank_counter', 'byzantine_blankcounter_plugin_options');
}

function byzantine_blankcounter_plugin_options() {

            $form_actions = "";

            if (isset($_POST['delete-statistics-button'])) {
                        byzantine_reset_statistics();
                        $form_actions = "Statistics have been wiped clean.";
            }

            $hideshow_statistics = $_POST['hideshow-nostatistics-button'];
            if (isset($hideshow_statistics)) {
                        if ($hideshow_statistics == 'Hide') {
                                    update_option('bc_hideshow_nostatistics', 0);
                                    $form_actions = "Pages/posts that have no statistics are now hidden";
                        } else {
                                    update_option('bc_hideshow_nostatistics', 1);
                                    $form_actions = "Pages/posts that have no statistics are now visible";
                        }
            }


            $all = array();
            ?>
            <h1>Blank Counter</h1>
            <p>This plugin will count all clicks on links that has target set to _blank. No modification of links are needed.</p>
            <p>This plugin is given to you by <a href="http://themerev.net/blank-counter-a-wordpress-plugin/" target="_blank">Themerev.net - Blank Counter, a Wordpress plugin</a></p>

            <div id="blankcounter-tabs">
                        <ul>
                                    <li><a href="#bc_post_page">Post/page</a></li>
                                    <li><a href="#bc_overall">Overall</a></li>
                                    <li><a href="#bc_settings">Settings</a></li>
                        </ul>



                        <div id="bc_post_page">

            <?php
            $meta_values = unserialize(get_post_meta(1, 'link', true));

            if (is_array($meta_values) && count($meta_values) > 0) {
                        ?>

                                                <h2 class="post_pahe_header">None Post/Page Sites</h2>

                        <?php
                        $it = 0;
                        foreach ($meta_values as $meta_value) {
                                    $it++;
                                    ?>
                                                            <div class="link-item<?php echo (($it % 2 == 0) ? ' even' : ''); ?>"><div class="link-url"><?php echo $meta_value['link']; ?></div><div class="link-count"><?php echo $meta_value['count']; ?></div><div class="link-object-actions"></div></div>

                                                            <?php
                                                            $all[$meta_value['link']] = $meta_value['count'];
                                                }
                                    } else {
                                                if (get_option('bc_hideshow_nostatistics', 1) == 1) {
                                                            ?>
                                                            <h2 class="post_pahe_header">None Post/Page Sites</h2>
                                                            <span class="no-statistics">No links clicked yet.</span><br>
                                                            <?php
                                                }
                                    }

                                    $args = array(
                                        'numberposts' => 1000,
                                        'offset' => 0,
                                        'orderby' => 'post_date',
                                        'order' => 'DESC',
                                        'post_type' => 'post',
                                        'post_status' => 'publish');

                                    $posts_array = get_posts($args);

                                    foreach ($posts_array as $post) {


                                                $meta_values = unserialize(get_post_meta($post->ID, 'link', true));

                                                if (is_array($meta_values) && count($meta_values) > 0) {
                                                            ?>
                                                            <h2 class="post_pahe_header"><?php echo $post->post_title; ?> ( <a href="<?php echo $post->guid; ?>" target="_blank">Link</a> )</h2>
                                                            <?php
                                                            $it = 0;
                                                            foreach ($meta_values as $meta_value) {
                                                                        $it++;
                                                                        ?>
                                                                        <div class="link-item<?php echo (($it % 2 == 0) ? ' even' : ''); ?>"><div class="link-url"><?php echo $meta_value['link']; ?></div><div class="link-count"><?php echo $meta_value['count']; ?></div><div class="link-object-actions"></div></div>
                                                                        <?php
                                                                        if ($all[$meta_value['link']]) {
                                                                                    $count = $all[$meta_value['link']] + $meta_value['count'];
                                                                        } else {
                                                                                    $count = $meta_value['count'];
                                                                        }

                                                                        $all[$meta_value['link']] = $count;
                                                            }
                                                } else {
                                                            if (get_option('bc_hideshow_nostatistics', 1) == 1) {
                                                                        ?>
                                                                        <h2 class="post_pahe_header"><?php echo $post->post_title; ?>' ( <a href="<?php echo $post->guid; ?>" target="_blank">Link</a> )</h2>
                                                                        '<span class="no-statistics">No links clicked yet.</span><br>
                                                                        <?php
                                                            }
                                                }
                                    }
                                    ?>
                        </div>

                        <div id="bc_overall">
                                    <?php
                                    $it = 0;
                                    foreach ($all as $link => $count) {
                                                $it++;
                                                ?>
                                                <div class="link-item<?php echo (($it % 2 == 0) ? ' even' : ''); ?>"><div class="link-url"><?php echo $link ?></div><div class="link-count"><?php echo $count; ?></div><div class="link-object-actions"></div></div>
                                                <?php
                                    }
                                    ?>
                        </div>

                        <div id="bc_settings">

                                    <?php if (!empty($form_actions)): ?>
                                                <div class="bc-form-action-performed"><?php echo $form_actions; ?></div>         
                                    <?php endif; ?>

                                    <form method="POST" action="<?php echo get_admin_url() . 'options-general.php?page=byzantine_blank_counter#bc_settings' ?>" name="blankcounter_settings-delete-statistics">

                                                <div class="bc-setting"><div class="bc-settings-name">Delete statistics</div><div class="bc-settings-form"><input type="submit" value="Delete" name="delete-statistics-button"></div></div>

                                    </form>

                                    <form method="POST" action="<?php echo get_admin_url() . 'options-general.php?page=byzantine_blank_counter#bc_settings' ?>" name="blankcounter_settings-hide-nostatistics">

                                                <div class="bc-setting"><div class="bc-settings-name">Hide/Show pages/posts with no statistics</div><div class="bc-settings-form"><input type="submit" value="<?php echo ((get_option('bc_hideshow_nostatistics', 1) == 1) ? 'Hide' : 'Show'); ?>" name="hideshow-nostatistics-button"></div></div>

                                    </form>   

                        </div>    
            <?php
}

function byzantine_blankcounter_modify_content_links($content) {
            $content = str_replace('target="_blank"', 'target="_blank" class="outgoing"', $content);
            return $content;
}

add_filter('the_content', 'byzantine_blankcounter_modify_content_links');

add_action('wp_ajax_outgoing_count', 'byzantine_blankcounter_outgoing_callback');

function byzantine_blankcounter_outgoing_callback() {

            if (is_not_post_nor_page($_POST['page'])) {
                        $page = 1;
            } else {
                        $page = url_to_postid($_POST['page']);
            }

            $esc_link = esc_url($_POST['link']);

            $links = unserialize(get_post_meta($page, 'link', true));

            print_r($links);

            $count = 0;
            $i = 0;

            foreach ($links as $link) {

                        if ($link['link'] == $esc_link) {
                                    $count = $link['count'];
                                    break;
                        }
                        $i++;
            }

            if ($count == 0) {
                        $links[] = array('link' => $esc_link, 'count' => 1);
            } else {
                        $count = $count + 1;
                        $links[$i] = array('link' => $esc_link, 'count' => $count);
            }


            update_post_meta($page, 'link', serialize($links));
}

function byzantine_reset_statistics() {
            $args = array(
                'numberposts' => 1000,
                'offset' => 0,
                'orderby' => 'post_date',
                'order' => 'DESC',
                'post_type' => 'post',
                'post_status' => 'publish');

            $posts_array = get_posts($args);

            foreach ($posts_array as $post) {
                        delete_post_meta($post->ID, 'link');
            }

            delete_post_meta(1, 'link');
}

function is_not_post_nor_page($url) {
            $post_page_id = url_to_postid($url);
            if (is_numeric($post_page_id) && $post_page_id > 0)
                        return false;
            return true;
}
?>