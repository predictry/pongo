<?php
/**
 * Related Products
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */
global $wp_rewrite;

if ($wp_rewrite->permalink_structure == '')
    $connector = '&';
else
    $connector = '?';

$classes = array('product' => 'product', 'first' => 'first');
$count   = 1;

if ($products->have_posts()) :
    ?>

    <div class="related products">
        <h2><?php _e($title); ?></h2>
        <?php woocommerce_product_loop_start(); ?>
        <?php while ($products->have_posts()) : $products->the_post(); ?>
            <?php if ($count !== 1) unset($classes['first']); ?>
            <li <?php post_class($classes); ?>>
                <?php do_action('woocommerce_before_shop_loop_item'); ?>
                <a href="
                <?php
                the_permalink();
                echo $connector . "p_id={$item_id}&p_len={$len}&p_seq={$count}&p_algo={$algo}";
                ?>">
                       <?php do_action('woocommerce_before_shop_loop_item_title'); ?>
                    <h3>
                        <?php the_title(); ?>
                    </h3>
                    <?php wc_get_template('loop/rating.php'); ?>
                    <?php woocommerce_get_template('loop/price.php'); ?>
                </a>
            </li>
            <?php $count++; ?>
        <?php endwhile; // end of the loop. ?>
        <?php woocommerce_product_loop_end(); ?>
    </div>

    <?php
endif;

wp_reset_postdata();
