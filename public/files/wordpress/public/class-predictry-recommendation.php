<?php

/**
 * Predictry Recommendation.
 *
 * @package	PredictryEngine
 * @author 	Wong Ban Korh <me@bankorh.com>
 * @license 	GNU General Public License v3.0
 * @link 	http://www.predictry.com
 * @copyright 	2014 Predictry.com
 */

/**
 * PredictryRecommendation class.
 * This class is to be used to grab recommendation products from predictry server.
 *
 * @package		PredictryEngine
 * @author 		Wong Ban Korh <me@bankorh.com>
 */
class PredictryRecommendation
{

    /**
     * Instance of this class.
     * @since 	1.0.0
     * @var 	object
     */
    protected static $instance = null;

    /**
     * Initialize the plugin by setting localization and loading public scripts and styles.
     * @since 	1.0.1
     */
    private function __construct()
    {

        $this->plugin_slug = PredictryEngine::get_plugin_slug();

        add_action('wpmu_new_blog', array($this, 'activate_new_site'));

        if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
            return;

        if (!$this->data = get_option('predictryengine'))
            if (empty($this->data['tenantid']) || empty($this->data['apikey']))
                return;

        if (!$this->widget = get_option('predictryengine_widget'))
            if (empty($this->widget['widgetid']))
                return;

        add_action('wp_head', array($this, 'recommendation'));
        add_action('wp_ajax_get_predictry', array($this, 'get_predictry'));
        add_action('wp_ajax_nopriv_get_predictry', array($this, 'get_predictry'));
    }

    /**
     * Return an instance of this class
     * @since 	1.0.0
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
     * Add action to specific hook for displaying the widget
     * @since 	1.0.0
     */
    public function recommendation()
    {
        add_action('woocommerce_after_single_product', array($this, 'after_single_product'));
        add_filter('woocommerce_related_products_args', array($this, 'wc_remove_related_products')); 
        
        // add_action('woocommerce_after_shop_loop', array($this, 'after_shop_loop'));
    }

    function wc_remove_related_products($args)
    {

        return array();
    }

    /**
     * Helper function to show the widget
     * @since 	1.0.1
     */
    private function recommendation_list($widgetid, $showproducts, $widgettitle)
    {

        global $post;

        $currency_code = get_woocommerce_currency_symbol();

        $data = array(
            'user_id'   => $user_id,
            'item_id'   => $post->ID,
            'widget_id' => $widgetid,
        );

        // Mock
        // $data[ 'item_id' ] = 8291;

        $api_url = $api_url . '?' . http_build_query($data);

        $limit = ( null != $showproducts ? $showproducts : 4 );
        $limit = !is_null($limit) ? $limit : 6;
        $title = ( null != $widgettitle ? $widgettitle : 'Recommendation Products' );


        $title_output = "<h2 class='title'><span>{$title}</span></h2>";

        $output = '<div class="predictry-rec" style="clear:both;width:100%;"></div> ';
        $output .= '<script type="text/javascript"> ';
        $output .= 'jQuery(".predictry-rec").append( "' . $title_output . '" ); '; //add title
        $output .= 'function predictry( response ) { ';
        $output .= 'obj = JSON.parse( response );';
        $output .= 'var ajax_url = \'' . admin_url('admin-ajax.php') . '\'; ';
        $output .= 'var list = []; var algo = ""; ';
        $output .= 'if(typeof obj === "object"){'; //check if obj is an object
        $output .= 'var limit = (' . $limit . ' > obj.items.length) ? obj.items.length  : ' . $limit . ';'; //compare limit with results count
        $output .= 'algo = obj.algo;';
        $output .= 'for ( i = 0; i < limit ; i++ ) ';
        $output .= 'list.push( obj.items[ i ] ); ';

        $output .= 'var data = { ';
        $output .= '\'action\': \'get_predictry\', ';
        $output .= '\'algo\': obj.algo, ';
        $output .= '\'len\': limit, ';
        $output .= '\'product_ids\': list, ';
        $output .= '\'this_id\': \'' . $data['item_id'] . '\', ';
        $output .= '\'this_title\': \'' . $title . '\', ';
        $output .= '\'widget_id\': \'' . $data['widget_id'] . '\' }; ';
        $output .= 'jQuery.post( ajax_url, data, function( response ) { ';

        $output .= 'jQuery(".predictry-rec").append( response ); ';

        $output .= '} ); } ';
        $output .= '} '; //end of check if obj is an object
        $output .= '</script>';
        $output .= '<ins class="predictry" data-predictry-widget-id="' . $data['widget_id'] . '" ';

        if (is_user_logged_in())
            $output .= 'data-predictry-user-id="' . get_current_user_id() . '" ';

        $output .= 'data-predictry-item-id="' . $data['item_id'] . ''
                . '" data-predictry-currency="' . $currency_code . ''
                . '" data-predictry-limit="' . $limit . ''
                . '"';

        $output.='></ins>';
        
        $output .= '<ins class="predictry-recent predictryTyped" data-predictry-widget-id="' . $data['widget_id'] . '" ';

        if (is_user_logged_in())
            $output .= 'data-predictry-user-id="' . get_current_user_id() . '" ';

        $output .= 'data-predictry-item-id="' . $data['item_id'] . ''
                . '" data-predictry-currency="' . $currency_code . ''
                . '" data-predictry-limit="' . $limit . ''
                . '"';

        $output.='></ins>';
        
        $output .= '<ins class="predictry-similar predictryTyped" data-predictry-widget-id="' . $data['widget_id'] . '" ';

        if (is_user_logged_in())
            $output .= 'data-predictry-user-id="' . get_current_user_id() . '" ';

        $output .= 'data-predictry-item-id="' . $data['item_id'] . ''
                . '" data-predictry-currency="' . $currency_code . ''
                . '" data-predictry-limit="' . $limit . ''
                . '" data-predictry-type="similar'
                . '"';

        $output.='></ins>';
        
        $output .= '<ins class="predictry-oivt predictryTyped" data-predictry-widget-id="' . $data['widget_id'] . '" ';

        if (is_user_logged_in())
            $output .= 'data-predictry-user-id="' . get_current_user_id() . '" ';

        $output .= 'data-predictry-item-id="' . $data['item_id'] . ''
                . '" data-predictry-currency="' . $currency_code . ''
                . '" data-predictry-limit="' . $limit . ''
                . '" data-predictry-type="oip'
                . '"';

        $output.='></ins>';
        
        $output .= '<ins class="predictry-oipt predictryTyped" data-predictry-widget-id="' . $data['widget_id'] . '" ';

        if (is_user_logged_in())
            $output .= 'data-predictry-user-id="' . get_current_user_id() . '" ';

        $output .= 'data-predictry-item-id="' . $data['item_id'] . ''
                . '" data-predictry-currency="' . $currency_code . ''
                . '" data-predictry-limit="' . $limit . ''
                . '" data-predictry-type="oiv'
                . '"';

        $output.='></ins>';
        $output .= '<script type="text/javascript"> ';
        $output .= '</script>';
        $output .= '<script type="text/javascript"> _predictry.push( [ \'getWidget\' ] );</script>';

        echo $output;
    }

    public function get_predictry()
    {

        global $wpdb, $woocommerce_loop;

        $product_ids = $_POST['product_ids'];
        $item_id     = intval($_POST['this_id']);
        $widget_id   = intval($_POST['widget_id']);
        $title       = $_POST['this_title'];

        $algo = $_POST['algo']; //algo that produce the recommenation
        $len  = $_POST['len']; //length of recommendation results

        $product_id = array();

        foreach ($product_ids as $id)
            $product_id[] = intval($id);

        // Mock
        // $product_id = array(90, 87, 83, 79);

        if (empty($product_id))
            return;

        $args = apply_filters('woocommerce_related_products_args', array(
            'post_type'           => 'product',
            'ignore_sticky_posts' => 1,
            'no_found_rows'       => 1,
            'orderby'             => $orderby,
            'post__in'            => $product_id,
            'post__not_in'        => array($item_id)
        ));

        $products                    = new WP_Query($args);
        $woocommerce_loop['columns'] = $columns;

        if (file_exists(get_template_directory() . '/woocommerce/predictry.php'))
            require( get_template_directory() . '/woocommerce/predictry.php' );
        else
            require( plugin_dir_path(__FILE__) . 'templates/predictry.php' );

        die();
    }

    /**
     * Show widget after single product summary
     * @since 	1.0.1
     */
    public function after_single_product()
    {
        
        $this->recommendation_list(
                $this->widget['after_single_product']['id'], $this->widget['after_single_product']['showproducts'], $this->widget['after_single_product']['widgettitle']
        );
    
    }

    /**
     * Show widget after product list
     * @since 	1.0.1
     */
    public function after_shop_loop()
    {

        $this->recommendation_list(
                $this->widget['after_shop_loop']['id'], $this->widget['after_shop_loop']['showproducts'], $this->widget['after_shop_loop']['widgettitle']
        );
    }

}
