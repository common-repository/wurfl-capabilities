<?php

define('WURFL_PLUGIN_DIR',      plugin_dir_path(__FILE__));
define('WURFL_BASE_DIR',        WP_CONTENT_DIR      . '/wurfl');
define('WURFL_RESOURCES_DIR',   WURFL_BASE_DIR      . '/resources');
define('WURFL_PERSISTENCE_DIR', WURFL_RESOURCES_DIR . '/storage/persistence');
define('WURFL_CACHE_DIR',       WURFL_RESOURCES_DIR . '/storage/cache');
define('WURFL_DIR',             WURFL_PLUGIN_DIR . '/WURFL');


class WURFLCap {
    var $base;
    var $requestingDevice;

    function __construct  ($base) {
        $this->base = $base;

        // Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
        register_activation_hook( $this->base, array( $this, 'activate' ) );
        register_deactivation_hook( $this->base, array( $this, 'deactivate' ) );
        register_uninstall_hook( $this->base, array( $this, 'uninstall' ) );
        
        add_action( 'setup_theme', array( $this, 'action_setup_theme') );
    } // end constructor

    /**
     * Fired when the plugin is activated.
     *
     * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
     */
    public function activate( $network_wide ) {
        $new_file_path = WURFL_BASE_DIR;
        
        if (!file_exists(WURFL_PLUGIN_DIR.'/wurfl.xml')){
            wp_die("wurfl.zip file not found, please read the readme file for installation instructions.");
        }

        if (!file_exists($new_file_path)) {
            if (!mkdir($new_file_path, 0755, true)){
                wp_die("Permission denied, make sure you have write permission to wp-content folder.");
                // return array(
                    // 'error' => 'Permission denied, make sure you have write permission to wp-content folder.'
                // );
            }
        }
        
        $persistence = WURFL_PERSISTENCE_DIR;
        $cache       = WURFL_CACHE_DIR;
        
        if (!file_exists($persistence)) {
            if (!mkdir($persistence, 0755, true)) {
                wp_die('Permission denied, make sure you have write permission to '.$persistence.' folder.');
                // return array(
                    // 'error' => 'Permission denied, make sure you have write permission to '.$new_file_path.' folder.'
                // );
            }
        }
        
        if (!file_exists($cache)) {
            if (!mkdir($cache, 0755, true)) {
                wp_die('Permission denied, make sure you have write permission to '.$cache.' folder.');
                // return array(
                    // 'error' => 'Permission denied, make sure you have write permission to '.$new_file_path.' folder.'
                // );
            }
        }
    } // end activate

    /**
     * Fired when the plugin is deactivated.
     *
     * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
     */
    public function deactivate( $network_wide ) {
    } // end deactivate

    /**
     * Fired when the plugin is uninstalled.
     *
     * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog 
     */
    public function uninstall( $network_wide ) {
        $new_file_path = WURFL_BASE_DIR;
        $this->recursive_remove_directory($new_file_path);
    } // end uninstall

    
    // code borrowed from http://lixlpixel.org/recursive_function/php/recursive_directory_delete/
    public function recursive_remove_directory($directory, $empty=FALSE) {
        // if the path has a slash at the end we remove it here
        if (substr($directory,-1) == '/') {
            $directory = substr($directory,0,-1);
        }

        // if the path is not valid or is not a directory ...
        if (!file_exists($directory) || !is_dir($directory)) {
            // ... we return false and exit the function
            return FALSE;

        // ... if the path is not readable
        } elseif(!is_readable($directory)) {
            // ... we return false and exit the function
            return FALSE;
        // ... else if the path is readable
        }
        else {
            // we open the directory
            $handle = opendir($directory);

            // and scan through the items inside
            while (FALSE !== ($item = readdir($handle))) {
                // if the filepointer is not the current directory
                // or the parent directory
                if ($item != '.' && $item != '..') {
                    // we build the new path to delete
                    $path = $directory.'/'.$item;

                    // if the new path is a directory
                    if(is_dir($path)) {
                        // we call this function with the new path
                        $this->recursive_remove_directory($path);

                    // if the new path is a file
                    }
                    else{
                        // we remove the file
                        unlink($path);
                    }
                }
            }
            // close the directory
            closedir($handle);

            // if the option to empty is not set to true
            if ($empty == FALSE) {
                // try to delete the now empty directory
                if (!rmdir($directory)) {
                    // return false if not possible
                    return FALSE;
                }
            }
            // return success
            return TRUE;
        }
    }
    
    public function action_setup_theme () {
        require_once WURFL_DIR .'/Application.php';

        $persistenceDir = WURFL_PERSISTENCE_DIR;
        $cacheDir = WURFL_CACHE_DIR;

        // Create WURFL Configuration
        $wurflConfig = new WURFL_Configuration_InMemoryConfig();

        // Set location of the WURFL File
        $wurflConfig->wurflFile(WURFL_PLUGIN_DIR.'/wurfl.xml');

        // Set the match mode for the API ('performance' or 'accuracy')
        $wurflConfig->matchMode('performance');

        // Automatically reload the WURFL data if it changes
        $wurflConfig->allowReload(true);

        // Setup WURFL Persistence
        $wurflConfig->persistence('file', array('dir' => $persistenceDir));

        // Setup Caching
        $wurflConfig->cache('file', array('dir' => $cacheDir, 'expiration' => 36000));

        // Create a WURFL Manager Factory from the WURFL Configuration
        $wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);

        // Create a WURFL Manager
        /* @var $wurflManager WURFL_WURFLManager */
        $wurflManager = $wurflManagerFactory->create();
        
        $this->requestingDevice = $wurflManager->getDeviceForHttpRequest($_SERVER);
    }
    
    public function getCapability ($cap = false) {
        return $this->requestingDevice->getCapability($cap);
    }
    
    public function is_wireless_device () {
        return $this->getCapability('is_wireless_device');
    }
    
    public function is_tablet () {
        return $this->getCapability('is_tablet');
    }
    
    public function is_touch () {
        return $this->pointing_method() == "touchscreen" ? "true" : "false";
    }
    
    public function supports_borderradius () {
        return $this->getCapability('css_rounded_corners') == "css3";
    }
    
    public function supports_gradient () {
        return $this->getCapability('css_gradient') == "css3";
    }
    
    public function pointing_method () {
        return $this->getCapability('pointing_method');
    }
}