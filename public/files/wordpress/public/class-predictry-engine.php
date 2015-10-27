<?php
/**
 * Predictry Engine.
 *
 * @package	PredictryEngine
 * @author 	Wong Ban Korh <me@bankorh.com>
 * @license 	GNU General Public License v3.0
 * @link 	http://www.predictry.com
 * @copyright 	2014 Predictry.com
 */

/**
 * PredictryEngine class.
 * This class is to be used to work with the public-facing side of WordPress site.
 *
 * @package	PredictryEngine
 * @author 	Wong Ban Korh <me@bankorh.com>
 */
class PredictryEngine
{

    /**
     * Plugin version, used for cache-busting of style and script file references.
     * @since 	1.0.0
     * @var 	string
     */
    const VERSION = '1.0.1';

    /**
     * Plugin slug, used as the text domain when internationalizing strings of text.
     * @since 	1.0.0
     * @var 	string
     */
    private static $plugin_slug = 'predictry-engine';

    /**
     * Instance of this class.
     * @since 	1.0.0
     * @var 	object
     */
    private static $instance = null;

    /**
     * Initialize the plugin by setting localization and loading public scripts and styles.
     * @since 	1.0.0
     */
    private function __construct()
    {

        // $this->api_url = "http://dashboard.predictry.com/api/v1/predictry";
        // $this->log_url = "http://dashboard.predictry.com/api/v1/cartlog";

        add_action('wpmu_new_blog', array($this, 'activate_new_site'));

        if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
            return;

        if (!$this->data = get_option('predictryengine'))
            if (empty($this->data['tenantid']) || empty($this->data['apikey']))
                return;

        add_action('wp_head', array($this, 'first_load'));
    }

    /**
     * Load the plugin JavaScript
     * @since 	1.0.1
     */
    public function first_load()
    {
        ?>
        <script type="text/javascript">
            var _predictry = _predictry || [];
            (function () {
                _predictry.push(['setTenantId', "<?php echo $this->data['tenantid']; ?>"], ['setApiKey', "<?php echo $this->data['apikey']; ?>"]);
                var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
                g.type = 'text/javascript';
                g.defer = true;
                g.async = true;
                g.src = '//d2gq0qsnoi5tbv.cloudfront.net/latest/p.min.js/sdk/v4/p.js';
                s.parentNode.insertBefore(g, s);
            })();
          
            _predictry.push(['track']); _predictry.push(['getWidget'])
        </script>
        <?php
    }

    /**
     * Return the plugin slug.
     * @since 	1.0.0
     * @return 	Plugin slug variable.
     */
    public static function get_plugin_slug()
    {

        return self::$plugin_slug;
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
     * Fired when the plugin is activated.
     * @since 	1.0.0
     * @param 	boolean - $network_wide
     * True if WPSU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
     */
    public static function activate($network_wide)
    {

        if (function_exists('is_multisite') && is_multisite()) {

            if ($network_wide) {
                $blog_ids = self::get_blog_ids();

                foreach ($blog_ids as $blog_id) {

                    switch_to_blog($blog_id);
                    self::single_activate();
                    restore_current_blog();
                }
            }
            else {

                self::single_activate();
            }
        }
        else {

            self::single_activate();
        }
    }

    /**
     * Fired when the plugin is deactivated.
     * @since 	1.0.0
     * @param 	boolean - $network_wide
     * True if WPSU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
     */
    public static function deactivate($network_wide)
    {

        if (function_exists('is_multisite') && is_multisite()) {

            if ($network_wide) {

                $blog_ids = self::get_blog_ids();

                foreach ($blog_ids as $blog_id) {

                    switch_to_blog($blog_id);
                    self::single_deactivate();
                    restore_current_blog();
                }
            }
            else {

                self::single_deactivate();
            }
        }
        else {

            self::single_deactivate();
        }
    }

    /**
     * Fired when a new site is activated with a WPMU environment.
     * @since 	1.0.0
     * @param 	int - $blog_id
     * ID of the new blog
     */
    public function activate_new_site($blog_id)
    {

        if (1 !== did_action('wpmu_new_blog'))
            return;

        switch_to_blog($blog_id);
        self::single_activate();
        restore_current_blog();
    }

    /**
     * Get all blog ids of blogs in the current network that are:
     * - Not archived
     * - Not spam
     * - Not deleted
     * @since 	1.0.0
     * @return 	array | false
     * The blog ids, false if no matches
     */
    private static function get_blog_ids()
    {

        global $wpdb;

        $sql = 'SELECT blog_id FROM $wpdb->blogs ';
        $sql .= 'WHERE archived = \'0\' ';
        $sql .= 'AND spam = \'0\' ';
        $sql .= 'AND deleted = \'0\'';

        return $wpdb->get_col($sql);
    }

    /**
     * Fired for each blog when the plugin is activated
     * @since 	1.0.0
     */
    private static function single_activate()
    {
        
    }

    /**
     * Fired for each blog when the plugin is deactivated
     * @since 	1.0.0
     */
    private static function single_deactivate()
    {
        
    }

    /**
     * Load the plugin text domain for translation
     * @since 	1.0.0
     */
    public function load_plugin_textdomain()
    {
        
    }

}
