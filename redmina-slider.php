<?php
/* Plugin Name: Redmina Slider 1.0 Plugin URI: https://redmina.esy.es/ 
 * Plugin URI: http://redmina.esy.es
 * Version: 1.0
 * Description: Slider  for WordPress , Version: 1.0
 * Author: Arsulescu Catalin Author URI: http://redmina.esy.es/ 
 * License: GPLv2 or later 
 */

function Redmina_enqueue_media_uploader_admin() {
    wp_register_script("redmina_media_uploader", plugin_dir_url(__FILE__) . 'js/redmina_media_uploader.js', array('jquery'), '1.0.0', 'true');
    wp_enqueue_script("redmina_media_uploader");
    wp_register_script("redmina_form_check", plugin_dir_url(__FILE__) . "js/redmina_form_check.js", array('jquery'), '1.0.0', true);
    wp_enqueue_script("redmina_form_check");
    wp_enqueue_media();
}

function Redmina_enqueue_scripts_frontend() {
    wp_enqueue_style('redmina-bootstrap', plugin_dir_url(__FILE__) . 'css/bootstrap.css');
    wp_enqueue_style("redmina-carousel-css", plugin_dir_url(__FILE__) . "css/carousel.css");
    wp_enqueue_style("redmina-animate", plugin_dir_url(__FILE__) . "css/animate.css");
    wp_register_style("redmina_slider_css", plugin_dir_url(__FILE__) . 'css/redmina-slider.css');
    wp_register_script("redmina-bootstrap", plugin_dir_url(__FILE__) . 'js/bootstrap.min.js', array('jquery'));
    wp_enqueue_script('jquery');
    wp_enqueue_script("redmina-bootstrap");
    wp_enqueue_style("redmina_slider_css");
}

function Redmina_slider() {
    $labels = array(
        'name' => _x('Sliders', 'post type general name', 'redmina.esy.es'),
        'singular_name' => _x('Slider', 'post type singular name', 'redmina.esy.es'),
        'menu_name' => _x('Redmina Slider', 'admin menu', 'redmina.esy.es'),
        'name_admin_bar' => _x('Slider', 'add new on admin bar', 'redmina.esy.es'),
        'add_new' => _x('Add New Slider', 'book', 'redmina.esy.es'),
        'add_new_item' => __('Add New Slider-title', 'redmina.esy.es'),
        'new_item' => __('New Slider', 'redmina.esy.es'),
        'edit_item' => __('Edit Slider', 'redmina.esy.es'),
        'view_item' => __('View Slider', 'redmina.esy.es'),
        'all_items' => __('All Sliders', 'redmina.esy.es'),
    );
    $args = array(
        'labels' => $labels,
        'label' => 'Redmina Slider',
        'public' => false,  /*otherwise makes a slug for this slider*/
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array('slug' => 'redmina_slider'),
        'query_var' => true,
        'menu_icon' => plugin_dir_url(__FILE__) . "/img/R.png",
        'supports' => array(
            'title',
            'author')
    );
    register_post_type('redmina_slider', $args);
}

//remove preview buitton from the top right of the page
function Redmina_posttype_admin_css() {
    global $post_type;
    $post_types = array(
        /* set post types */
        'redmina_slider',
    );
    if (in_array($post_type, $post_types)) {
        echo '<style type="text/css">#post-preview, #view-post-btn{display: none;}</style>';
    }
}

function Redmina_slider_box() {
    add_meta_box('redmina_slider', esc_html__('Redmina slider', 'redmina.esy.es'), 'Redmina_slider_fields', 'redmina_slider', 'normal', 'high');
    add_meta_box('redmina_slider_interval', esc_html__('Interval between slides,in  miliseconds'), 'Redmina_slider_interval_field', 'redmina_slider', 'normal', 'high');
}

function Redmina_slider_save() {

    $id = get_the_ID();

    if (!isset($_POST["Redmina_slider_content_nonce"]) || !wp_verify_nonce($_POST["Redmina_slider_content_nonce"], basename(__FILE__)))  //could be plugin_basename()
        return;
    if (!current_user_can("edit_posts", $id))
        return;

    //save the files
    $new_value = sanitize_text_field($_POST['redmina_slider']);
    update_post_meta($id, "redmina_slider", $new_value);

    //save the transition interval
    $transition_interval = sanitize_text_field($_POST['redmina_slider_interval']);

    if (is_numeric($transition_interval) && $transition_interval > 0) {

        update_post_meta($id, "redmina_slider_interval", $transition_interval);
    } else {
        //does nothing , jquery starts screaming realy loud :)
    }
}

function Redmina_slider_interval_field() {
    $id = get_the_ID();
    if (!current_user_can("edit_posts", $id)) {
        echo "<b style='color:red'>You don't have editor rights !</b>";
        return;
    }
    ?>
    <span  id="label_for_redmina_slider_interval"  style='color:red'></span><br>
    <input type="text"  name='redmina_slider_interval' id='redmina_slider_interval' value="<?php
    $interval = esc_html(get_post_meta($id, "redmina_slider_interval", true)); //is comming from our database , already cleaned, but anyway
    if (empty($interval)) {
        echo "7000";
    } else {
        echo $interval;
    }
    ?>">
    <?php
       }

       function Redmina_slider_fields($post) {

           $id = get_the_ID();
           if (!current_user_can("edit_posts", $id)) {
               echo "<b style='color:red'>You don't have editor rights !</b>";
               return;
           }


           $shortcode = "[redmina_slider_shortcode id=" . $id . "]";
           ?>
    <table  width='90%'>
        <!-- Form to handle the upload - The enctype value here is very important -->
        <form  method="post"  action="options.php" enctype="multipart/form-data">
    <?php wp_nonce_field(basename(__FILE__), 'Redmina_slider_content_nonce');  //could be plugin_basename()   ?>
            <tr>
                <td colspan="2" align='right'>
                    <div style="font-size:15px;">Rules :</div>
                    <ul>
                        <li style="color: #BDB76B;">Your images should have the same size.</li>
                        <li style="color: #BDB76B;">Basic documentation is here : <a  target='_blank' href='http:\\redmina.esy.es'>Here </a></li>
                </td>  
            </tr>
            <tr>
                <td colspan="2"><h2>Paste this in your theme :echo do_shortcode("<?php echo esc_html($shortcode); ?> ");</h2>
                    <br>Paste this in your post/page :
                </td>
            </tr>

            <tr><td><input type="text" value="<?php echo esc_html($shortcode); ?>"  size="50"></td><td>

                    <input type='hidden' class="upfile"  id="file1" name='redmina_slider'  value="<?php
    $img = esc_html(get_post_meta($id, "redmina_slider", true)); //is comming from our database , already clean , but esc_html anyway down

    echo $img;
    ?>">
                    <input type="button" class="upfile" value="Upload a picture"   ></td></tr>


        </form>
    </table>
    <table  border="0" style='border-collpase:collapse' cellpadding="10"  width="100%" >
    <?php
    $imgs = esc_html(get_post_meta($id, "redmina_slider", true));

    if (strlen(trim($imgs)) > 0) {


        $imgs_array = explode(";", $imgs);

        $i = 0;

        foreach ($imgs_array as $img_data) {

            $img_data_array = explode(',', $img_data);
            ?>
                <tr>
                    <td style=" border-bottom:1px dotted #BDB76B;background-color:#fff;">
                        <span style="background-color: orange;padding:2px;color:#fff;font-size:18;">
            <?php echo ($i + 1); ?> </span>
                        <img src="<?php echo esc_html($img_data_array[0]); ?> "   width="250" alt="missing picture ">
                    </td>
                    <td style=" border-bottom:1px dotted #BDB76B;background-color:#fff;">
                        <label for="text-over-slide">Text over slide</label>
                        <input type="text"   class="text-over-slide"  id="<?php echo "text-over-slide" . $i; ?>" data-bind="<?php echo $i; ?>"  value='<?php echo esc_textarea($img_data_array[1]); ?>'  >
                        <input type="checkbox" value="" class="save_text_over_slide" name="save_text_over_slide" data-bind="<?php echo $i; ?>">Modify the text(Update slider is also necesary)
                        <div>  Don't forget to push the update button<br> , after editing this textarea!</div>
                    </td>
                    <td style=" border-bottom:1px dotted #BDB76B;background-color:#fff;">
                        <span  style="float:right;color:#900;"><input type="checkbox"  data-bind="<?php echo $i; ?>"  class="delete_img" style="margin:10px;" >Delete this picture</span>
                    </td>
                </tr>

            <?php
            $i++;  //0 based indexed array in js
        }
    }
    ?>
    </table>
        <?php
    }

    function redmina_render_slider($atts) {

        shortcode_atts(array('id' => ''), $atts);

        $slide_images = esc_html(get_post_meta($atts['id'], 'redmina_slider', true));

        $slide_array = explode(';', $slide_images);
        $counter = count($slide_array);
        ?>

    <div id="myCarousel<?php echo $atts['id']; ?>" class="carousel slide carousel-fade" data-ride="carousel" data-interval='<?php echo esc_html(get_post_meta($atts['id'], "redmina_slider_interval", true)); ?>' data-pause="null">
        <!-- Indicators -->
        <div class="carousel-indicators">
    <?php
    for ($i = 0; $i < $counter; $i++) {
        ?>
                <div data-target="#myCarousel<?php echo $atts['id']; ?>" data-slide-to="<?php echo $i; ?>"  <?php
                if ($i == 0) {
                    echo "class='active'";
                }
                ?>  > <?php echo ""; ?></div>
                <?php
                 }
                 ?>
        </div>

        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
    <?php
    $i = 0;
    foreach ($slide_array as $slide) {
        $slide_url_and_text = explode(",", $slide);
        ?>
                <div class="item <?php
                if ($i == 0) {
                    echo "active";
                }
                ?>">

                    <img src="<?php echo $slide_url_and_text[0]; ?>" alt="picture missing" >
                    <div class="carousel-caption">

                        <p><?php echo $slide_url_and_text[1]; ?></p>
                    </div>      
                </div>
        <?php
        $i++;
    }
    ?>

        </div>

        <!-- Left and right controls -->
        <a class="left carousel-control" href="#myCarousel<?php echo $atts['id']; ?>" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#myCarousel<?php echo $atts['id']; ?>" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <?php
}

function redmina_slider_columns($columns) {

    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __('Slider name'),
        'shortcode' => __('Shortcode'),
        'author' => __('Author'),
        'date' => __('Date')
    );

    return $columns;
}

function redmina_slider_manage_shortcode_column($column, $post_id) {

    global $post;

    switch ($column) {
        case 'shortcode': echo "[redmina_slider_shortcode" . " id=" . $post_id . "]";
            break;
        default:break;
    }
}

add_action("admin_enqueue_scripts", "Redmina_enqueue_media_uploader_admin");
add_action("wp_enqueue_scripts", "Redmina_enqueue_scripts_frontend");
add_action('init', 'Redmina_slider');
add_action('add_meta_boxes', 'Redmina_slider_box');
add_action('save_post', 'Redmina_slider_save');
add_filter('manage_edit-redmina_slider_columns', 'redmina_slider_columns');
add_action('manage_redmina_slider_posts_custom_column', 'redmina_slider_manage_shortcode_column', 10, 2);
add_shortcode("redmina_slider_shortcode", "redmina_render_slider");
//get rid of preview button from top right of the page , since is showing error page
add_action('admin_head-post-new.php', 'Redmina_posttype_admin_css');
add_action('admin_head-post.php', 'Redmina_posttype_admin_css');
