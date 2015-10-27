<?php
/**
 * Predictry Engine.
 *
 * @package		PredictryEngine
 * @author 		Wong Ban Korh <me@bankorh.com>
 * @license 	GNU General Public License v3.0
 * @link 		http://www.predictry.com
 * @copyright 	2014 Predictry.com
 */

/**
 * PredictryEngineAdmin class.
 * This class is to be used to work with the adminstrative side of the WordPress site.
 *
 * @package		PredictryEngineAdmin
 * @author 		Wong Ban Korh <me@bankorh.com>
 */
class PredictryEngineAdmin
{

    /**
     * Instance of this class.
     * @since 	1.0.0
     * @var 	object
     */
    protected static $instance = null;

    /**
     * Slug of the plugin screen.
     * @since 	1.0.0
     * @var 	string
     */
    protected $plugin_screen_hook_suffix = null;

    /**
     * Initialize empty message
     * @since 	1.0.0
     * @var 	string | boolean
     */
    var $message_return = false;

    /**
     * Initialize empty error
     * @since 	1.0.0
     * @var 	string | boolean
     */
    var $error_return = false;

    /**
     * Initialize the plugin by setting localization and loading public scripts and styles.
     * @since 	1.0.0
     */
    private function __construct()
    {

        $plugin             = PredictryEngine::get_instance();
        $this->plugin_slug  = $plugin->get_plugin_slug();
        $this->thissettings = get_option('predictryengine');
        $this->widget       = get_option('predictryengine_widget');
        $this->check_woocommerce();

        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
        add_action('admin_init', array($this, 'define_settings_form'));
        add_action('admin_init', array($this, 'data_submission'));

        $plugin_basename = plugin_basename(plugin_dir_path(realpath(dirname(__FILE__))) . $this->plugin_slug . '.php');

        add_filter('plugin_action_links_' . $plugin_basename, array($this, 'add_action_links'));
    }

    /**
     * Return an instance of this class
     * @since 	1.0.0
     * @return 	Object. A single instance of this class
     */
    public static function get_instance()
    {

        if (null == self::$instance)
            self::$instance = new self;

        return self::$instance;
    }

    /**
     * Register the administration menu
     * @since 	1.0.0
     */
    public function add_plugin_admin_menu()
    {

        $this->plugin_screen_hook_suffix = add_menu_page(
                __('Predictry', $this->plugin_slug), __('Predictry', $this->plugin_slug), 'manage_options', $this->plugin_slug, array($this, 'display_plugin_admin_page'), plugin_dir_url(__FILE__) . 'views/images/predictry.png', 58
        );
    }

    /**
     * Render the settings page for this plugin.
     * @since 	1.0.0
     */
    public function display_plugin_admin_page()
    {

        include_once( 'views/admin.php' );
    }

    /**
     * Add links to the plugin page.
     * @since 1.0.0
     */
    public function add_action_links($links)
    {

        $links['settings']  = '<a href="' . admin_url('admin.php?page=' . $this->plugin_slug) . '">' . __('Settings', $this->plugin_slug) . '</a>';
        $links['predictry'] = '<a href="http://www.predictry.com" target="_blank">' . 'Predictry' . '</a>';

        return $links;
    }

    /**
     * Initialize and set up the settings form
     * @since 1.0.0
     */
    public function define_settings_form()
    {

        register_setting(
                $this->plugin_slug, $this->plugin_slug
        );

        add_settings_section(
                $this->plugin_slug, 'Predictry Configuration', array($this, 'print_info'), $this->plugin_slug
        );

        $field_args = array(
            'type'      => 'text',
            'id'        => 'tenantid',
            'name'      => 'tenantid',
            'desc'      => __('Tenant ID created from Predictry.com', $this->plugin_slug),
            'title'     => __('Tenant ID', $this->plugin_slug),
            'style'     => 'margin: 0; width: 350px;',
            'label_for' => 'tenantid',
            'place'     => 'Eg. LIVE_SITENAME'
        );

        add_settings_field(
                'tenantid', __('Tenant ID', $this->plugin_slug), array($this, 'display_text_field'), $this->plugin_slug, $this->plugin_slug, $field_args
        );

        $field_args = array(
            'type'      => 'text',
            'id'        => 'apikey',
            'name'      => 'apikey',
            'desc'      => __('API Key created from Predictry.com', $this->plugin_slug),
            'title'     => __('API Key', $this->plugin_slug),
            'style'     => 'margin: 0; width: 350px;',
            'label_for' => 'apikey',
            'place'     => 'Eg. ml12m4l21m5l1m1245o12hg4fvu12ff1'
        );

        add_settings_field(
                'addtocartclass', __('Shop List \'Add to cart\'s class\'', $this->plugin_slug), array($this, 'display_text_field'), $this->plugin_slug, $this->plugin_slug, $field_args
        );
    }

    /**
     * Display the text field
     * @since 	1.0.0
     */
    public function display_text_field($args)
    {

        extract($args);

        echo '<p><input type="', $type, '" id="', $id, '" name="', $name, '" value="', (!empty($_POST[$name]) ? ( $_POST[$name] ) : $this->thissettings[$name] ), '" placeholder="', $place, '" title="', $title, '" style="', $style, '"></p>';

        if ($desc != '')
            echo '<p><span class="description">', $desc, '</span></p>';
    }

    /**
     * Display the description text of this plugin
     * @since 1.0.0
     */
    public function print_info()
    {

        echo '<p>Predictry is experts in predictive behaviour, machine learning and artificial intelligence to help make sense of your site data.</p>';
        echo '<p>To get your account, kindly proceed to <a href="http://www.predictry.com/" target="_blank">Predictry.com</a></p>';
        echo '<p>To change the templates of showing recommendation products, kindly copy the files \'predictry.php\' inside public/templates folder into your themes/woocommerce folders.</p>';
    }

    /**
     * Generate setting form
     * @since 1.0.0
     */
    public function predictry_settings()
    {
        ?>
        <form method="post" action="admin.php?page=<?php echo $this->plugin_slug ?>">
        <?php
        settings_fields($this->plugin_slug);
        do_settings_sections($this->plugin_slug);
        ?>
            <div style="border-top: 1px solid #ccc; margin-top:20px; padding-top:20px;">
                <h3>Show Widget After Single Product</h3>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="widgettitle">Widget Title</label>
                            </th>
                            <td>
                                <p><input type="text" id="widgettitle" name="after_single_product[widgettitle]" value="<?php echo (!empty($_POST['after_single_product']['widgettitle']) ? ( $_POST['after_single_product']['widgettitle'] ) : $this->widget['after_single_product']['widgettitle'] ) ?>" placeholder="Eg. Recommendation Products" title="Widget Title to be shown" style="margin: 0; width: 350px;"></p><p><span class="description">Title to be shown for the widget</span></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <label for="products">Number of Products</label>
                            </th>
                            <td>
                                <p><input type="text" id="showproducts" name="after_single_product[showproducts]" value="<?php echo (!empty($_POST['after_single_product']['showproducts']) ? ( $_POST['after_single_product']['showproducts'] ) : $this->widget['after_single_product']['showproducts'] ) ?>" placeholder="Eg. 4" title="Widget Title to be shown" style="margin: 0; width: 350px;"></p><p><span class="description">Number of products to be shown for the widget</span></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
              </div>
        <?php
        submit_button('Save');
        ?>
        </form>
        <?php
    }

    /**
     * Process submitted data
     * @since 1.0.1
     */
    public function data_submission()
    {

        if (isset($_POST['submit']) && $_POST['submit'] == 'Save') {

            $data = array(
                'tenantid'       => $_POST['tenantid'],
                'apikey'         => $_POST['apikey'],
                'addtocartclass' => $_POST['addtocartclass']
            );

            $widget = array(
                'after_single_product' => array(
                    'id'           => $_POST['after_single_product']['id'],
                    'widgettitle'  => $_POST['after_single_product']['widgettitle'],
                    'showproducts' => $_POST['after_single_product']['showproducts']
                ),
                'after_shop_loop'      => array(
                    'id'           => $_POST['after_shop_loop']['id'],
                    'widgettitle'  => $_POST['after_shop_loop']['widgettitle'],
                    'showproducts' => $_POST['after_shop_loop']['showproducts']
                ),
            );

            update_option('predictryengine', $data);
            update_option('predictryengine_widget', $widget);

            $this->message_return .= 'Settings saved.';

            if (empty($_POST['tenantid']) || empty($_POST['apikey']))
                $this->message_return .= ' But Tenant ID or API Key or both are empty.';
        }
    }

    /**
     * Return message if needed
     * @since 1.0.0
     */
    public function print_message()
    {

        return $this->message_return;
    }

    /**
     * Return error if needed
     * @since 1.0.0
     */
    public function print_error()
    {

        return $this->error_return;
    }

    /**
     * Check if WooCommerce is installed
     * @since 1.0.0
     */
    public function check_woocommerce()
    {

        if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
            $this->error_return = 'You have to install WooCommerce plugin before you can use this plugin.<br>Please visit to <a href="http://www.woothemes.com/" target="_blank">WooThemes.com</a> to download the plugin.';
    }

}
