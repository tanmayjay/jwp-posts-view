<?php

namespace Jay\JPV\Frontend;

class Shortcode {

    /**
     * Shortcode class constructor
     */
    function __construct() {
        add_shortcode( 'jpv-sc', [ $this, 'render_shortcode' ] );
    }

    /**
     * Renders shortcode
     *
     * @param array $atts
     * @param string $content
     * 
     * @return string
     */
    public function render_shortcode( $atts, $content = '' ) {
        global $wp;
        $url          = home_url( $wp->request );
        $number_posts = 10;
        $category     = [];
        $order        = '';
        $order_by     = '';
        $meta_key     = '';

        $terms = get_terms( array(
            'taxonomy'   => 'category',
            'hide_empty' => false,
        ) );

        if ( isset( $atts['category'] ) ) {
            $cats = explode( ",", $atts['category'] );
            foreach ( $cats as $cat ) {
                $term       = get_term_by( 'name', $cat, 'category' );
                $category[] = $term->term_id;
            }
        }

        if ( isset( $atts['order'] ) ) {
            $order    = $atts['order'];
            $order_by = 'meta_value_num';
            $meta_key = 'post_views_count';
        } 

        if ( isset( $_POST['jpv-submit'] ) ) {
            $number_posts = isset( $_POST['numberposts'] ) ? $_POST['numberposts']: $number_posts;
            $order        = isset( $_POST['order'] ) ? $_POST['order']            : $order;
            $order_by     = isset( $_POST['order'] ) ? 'meta_value_num'           : $order_by;
            $meta_key     = isset( $_POST['order'] ) ? 'post_views_count'         : $meta_key;
            $category     = isset( $_POST['category'] ) ? $_POST['category']      : $category;
        }
        
        $defaults = array(
            'numberposts'  => $number_posts,
            'category__in' => $category,
            'orderby'      => $order_by,
            'order'        => $order,
            'meta_key'     => $meta_key,
        );

        $args       = shortcode_atts( $defaults, $atts );
        $posts      = get_posts( $args );
        $marked_ids = [];

        if ( isset( $atts['ids'] ) ) {
            $marked_ids = explode( ",", $atts['ids'] );
            $marked_ids = array_map( function( $value ) {
                return (int) $value;
            }, $marked_ids );

        }

        $total_post = wp_count_posts()->publish;

        ob_start();
        
        if ( $posts ) {
            ?>
            <div class="wrap">
                <form action="<?php echo $url ?>" method="post">
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="numberposts">Number of Posts: </label>
                            </th>
                            <th scope="row">
                                <select name="numberposts" id="numberposts">
                                    <option disabled="true" selected>Select</option> 
                                <?php for ( $i = 1; $i < $total_post; $i++ ) { ?>
                                    <option value="<?php echo $i ?>"><?php echo $i ?></option>
                                <?php } ?>
                                </select>
                            </th>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="order">Order posts by Views: </label>
                            </th>
                            <th scope="row">
                                <select name="order" id="order">
                                    <option disabled="true" selected>select</option>
                                    <option value="ASC">Ascending</option>
                                    <option value="DESC">Descending</option>
                                </select>
                            </th>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="category">View posts by Category: </label>
                            </th>
                            <th scope="row">
                            <?php foreach ( $terms as $term ) { ?>
                                <input type="checkbox" name="category[]" id="category" value="<?php echo $term->term_id; ?>"> <?php echo $term->name; ?><br/>
                            <?php } ?>
                            </th>
                        </tr>
                    </table>
                    <button type="submit" class="submit" id="submit" name="jpv-submit">Submit</button>
                </form>
            </div>
            <hr>
            
            <?php
            foreach ( $posts as $post ) {
                $view_count = jpv_emphasize_text( jpv_get_post_view_count( $post ), 'Views' );
                $excerpt = '';
                if ( in_array( $post->ID, $marked_ids ) ) {
                    if ( has_excerpt( $post ) ) {
                        $excerpt = get_the_excerpt( $post );
                    } else {
                        $excerpt = jpv_custom_excerpt( $post, 200 );
                    }
                }
            ?>
                <div class="jpv-box">
                    <h2>
                        <a class ="jpv-a" href="<?php echo get_permalink( $post ); ?>" target="_blank" rel="noopener noreferrer">
                            <?php echo $post->post_title; ?>
                        </a>
                    </h2>
                    <span><?php echo $view_count; ?></span>
                </div>
                <p><?php echo $excerpt; ?></p>
                <hr>
                <?php
            }
        }

        $content = ob_get_clean();
        return $content;
    }
}