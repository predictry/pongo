<?php

/**
 * Predictry Engine Data Collector
 *
 * @package	PredictryEngine
 * @author 	Wong Ban Korh <me@bankorh.com>
 * @license 	GNU General Public License v3.0
 * @link 	http://www.predictry.com
 * @copyright 	2014 Predictry.com
 */

/**
 * PredictryDataCollector class.
 * This class is to be used to collect data from the woocommerce customer of WordPress site.
 *
 * @package		PredictryEngine
 * @author 		Wong Ban Korh <me@bankorh.com>
 */
class PredictryDataCollector
{

    /**
     * Instance of this class.
     * @since 	1.0.1
     * @var 	object
     */
    private static $instance = null;

    /**
     * Initialize the plugin by setting localization and loading public scripts and styles.
     * @since 	1.0.0
     */
    private function __construct()
    {

        if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
            return;

        if (!$this->data = get_option('predictryengine'))
            if (empty($this->data['tenantid']) || empty($this->data['apikey']))
                return;

        add_action('woocommerce_after_single_product', array($this, 'view_product'));
        add_action('woocommerce_after_checkout_form', array($this, 'started_checkout'));
        add_action('woocommerce_thankyou', array($this, 'complete_buy'), 10, 1);
        add_action('wp_footer', array($this, 'embed_add_to_cart'));
    }

    /**
     * Return an instance of this class
     * @since 	1.0.1
     * @return 	Object. A single instance of this class
     */
    public static function get_instance()
    {

        // Set it now if the single instance hasn't been set
        if (null == self::$instance)
            self::$instance = new self;

        return self::$instance;
    }

    /**
     * Embed Javascript to page to track 'add_to_cart' button
     * @since   1.0.1
     */
    public function embed_add_to_cart()
    {

        $output = '<script type="text/javascript"> ';
        // track product list
        $output .= 'jQuery( document ).on( "click", "';
        $output .= '.add_to_cart_button, .add_to_cart';

        if ($this->data['addtocartclass'] != null | $this->data['addtocartclass'] != '')
            $output .= ', ' . $this->data['addtocartclass'];

        $output .= '", function() { ';
        $output .= 'var quantity = jQuery( this ).attr( "data-quantity" ); ';
        $output .= 'var itemID = jQuery( this ).attr( "data-product_id" ); ';
        $output .= 'send_data( itemID, quantity ); ';
        $output .= '} ); ';

        // track single product page
        if (is_product()) {

            $output .= 'jQuery( \'form.cart\' ).on( "click", ".single_add_to_cart_button", function( event ) {';
            $output .= 'var quantity = jQuery( \'input[name=quantity]\' ).val(); ';
            $output .= 'var itemID = jQuery( \'input[name=add-to-cart]\' ).val(); ';
            $output .= 'send_data( itemID, quantity ); ';
            $output .= '} ); ';
        }

        $output .= 'function send_data( itemID, quantity ) { ';
        $output .= 'if ( typeof quantity == "undefined" || quantity == null ) ';
        $output .= 'quantity = 1; ';
        $output .= 'var add_to_cart_data = {';
        $output .= 'action: { name: "add_to_cart" }, ';

        if (is_user_logged_in()) {
            $output .= 'user: { user_id: "' . get_current_user_id() . '" }, ';
        }

        $output .= 'items: [ { ';
        $output .= 'item_id: itemID, ';
        $output .= 'qty: quantity ';
        $output .= '} ] ';
        $output .= '}; ';
        $output .= '_predictry.push( [ \'track\', add_to_cart_data ] ); ';
        $output .= '} ';
        $output .= '</script>';

        echo $output;
    }

    /**
     * Track the view action
     * @since 	1.0.1
     */
    public function view_product()
    {

        global $post, $product;

        $id         = $post->ID;
        $image_link = wp_get_attachment_url(get_post_thumbnail_id());
        $quantity   = ( null != $product->get_stock_quantity() ? $product->get_stock_quantity() : '0' );
        $link       = get_permalink($id);
        $price      = $product->get_price();

        $terms      = get_the_terms($post->ID, 'product_cat');
        $category   = array();
        foreach ($terms as $term)
            $category[] = $this->addslashes_extended($term->name);

        $category_size = sizeof($category);
        $main_category = $category[0];
        unset($category[0]);

        $variable = wp_get_object_terms($post->ID, 'product_tag');
        $tags     = array();
        foreach ($variable as $value)
            $tags[]   = $this->addslashes_extended($value->name);

        $output = '<script type="text/javascript">';
        $output .= 'var view_data = { action: { name: "view" }, ';

        if (is_user_logged_in()) {
          // $output .= 'user: { user_id: "' . get_current_user_id() . '" }, ';
          // $output .= 'user_flag: true'; // if the system user_id is there, we put this flag true or flase
        }
         
        $output .= 'items: [ { ';
        $output .= 'item_id: "' . $id . '", ';
        $output .= 'name: "' . $this->addslashes_extended(get_the_title()) . '", ';
        $output .= 'price: ' . $price . ', ';
        $output .= 'img_url: "' . $image_link . '", ';
        $output .= 'item_url: "' . $link . '", ';
      
        if ($product->sale_price != 0)
            $output .= 'discount: "' . ( $product->regular_price - $price ) . '", ';

        $output .= 'description: "' . substr($this->addslashes_extended($product->post->post_content), 0, 150) . '", ';
        $output .= 'inventory_qty: "' . $quantity . '", ';
        $output .= 'category: "' . $main_category . '", ';

        if ($category_size > 1)
            $output .= 'sub_category: [ "' . implode('", "', $category) . '" ], ';

        $output .= 'tags: [ "' . implode('", "', $tags) . '" ], ';
        $output .= ' } ] }; ';
        $output .= '_predictry.push( [ \'track\', view_data ] );';
        $output .= '</script>';

        echo $output;
    }

    /**
     * Track the started checkout action
     * @since 	1.0.1
     */
    public function started_checkout()
    {

        global $woocommerce;

        $cart = $woocommerce->cart->get_cart();

        $output = '<script type="text/javascript">';
        $output .= 'var started_checkout_data = { ';
        $output .= 'action: { name: "started_checkout" }, ';

        if (is_user_logged_in())
            $output .= 'user: { user_id: "' . get_current_user_id() . '" }, ';

        $output .= 'items: [ ';

        foreach ($cart as $cart_item) {

            $output .= '{ item_id: "' . $cart_item['product_id'] . '" }';

            if (end($cart) == $cart_item)
                $output .= '';
            else
                $output .= ', ';
        }

        $output .= ' ] }; ';
        $output .= '_predictry.push( [ \'track\', started_checkout_data ] );';
        $output .= '</script>';

        echo $output;
    }

    /**
     * Track the buy complete action
     * @since 	1.0.1
     */
    public function complete_buy($id)
    {

        global $woocommerce;

        $order = new WC_Order($id);

        if (!$order)
            return;

        $items = $order->get_items();
        $total = 0;

        $output = '<script type="text/javascript">';
        $output .= 'var buy_data = { action: { name: "buy", total: ' . $total . ' }, ';
        $output .= 'user: { ';

        if (is_user_logged_in())
            $output .= 'user_id: "' . get_current_user_id() . '"';
        else
            $output .= 'email: "' . $order->billing_email . '"';

        $output .= ' }, ';
        $output .= 'items: [ ';

        foreach ($items as $item) {

            $output .= '{ item_id: "' . $item['product_id'] . '", ';
            $output .= 'qty: ' . $item['qty'] . ', ';
            $output .= 'sub_total: ' . $item['line_subtotal'] . ' }';

            if (end($items) == $item)
                $output .= '';
            else
                $output .= ', ';
        }

        $output .= ' ] }; ';
        $output .= '_predictry.push( [ \'track\', buy_data ] );';
        $output .= '</script>';

        echo $output;

        return $id;
    }

    /**
     * Checks HTTP referer to see if request was a page reload
     * Prevents duplication of tracking events when user reloads page or submits a form
     * @since 	1.0.0
     */
    private function not_page_reload()
    {

        if (isset($_SERVER['HTTP_REFERER'])) {
            //return portion before query string
            $request_uri = str_replace(strstr($_SERVER['REQUEST_URI'], '?'), '', $_SERVER['REQUEST_URI']);

            if (stripos($_SERVER['HTTP_REFERER'], $request_uri) === false)
                return true;
        }

        return false;
    }

    /**
     * Add slashes to Javascript string
     * Prevents printing error
     * @since 	1.0.0
     */


    public static function addslashes_extended(&$mixed)
    {
        if (is_array($mixed) || is_object($mixed)) {

            array_walk($mixed, 'PredictryEngine::addslashes_extended');
        }
        elseif (is_string($mixed)) {
          $mixed = addslashes($mixed);
          $mixed = htmlspecialchars($mixed, ENT_COMPAT); 
          $mixed = str_replace(array("\r\n", "\r", "\n"), "", $mixed);
        }

        return $mixed;
    }

}
