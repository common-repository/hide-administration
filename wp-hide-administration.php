<?php
/**
 * Plugin Name: Hide admin area & admin bar
 * Description: Hides admin area & admin bar
 * Version: 0.1
 * Author: Antonio Gil Espinosa
 * Author URI: https://www.linkedin.com/in/antoniogilespinosa/
 * License: Apache License 2.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//require_once(get_template_directory().'core/load.php');
 
if ( !class_exists( 'Hide_Administration_Addon' ) ) {
    
    class Hide_Administration_Addon
    {

        public function current_user_has_allowed_administration()
        {
            $user = wp_get_current_user();
            if(!$user)
                return false;

            $role = $user->roles ? $user->roles[0] : false;
            if(!$role)
                return false;

            $opts = get_option('hide_admin');
            if(!opts)
                return true;

            $disallowed = $opts[$role] == 'disallowed';
         

            return !$disallowed;
        }

        public function redirect_from_administration_area() {
            
            $redirect = home_url( '/' );
            if (!$this->current_user_has_allowed_administration())
                exit( wp_redirect( $redirect ) );
        }




        public function settings_page() {

            $this->settings_page_inner(array(),get_editable_roles());
        }

        private function settings_page_inner($selectedRoles,$allRoles) {
            ?>
    <div class="wrap">
        <h1>Hide admin area & admin bar</h1>

        <form method="post" action="options.php">

            <?php settings_fields( 'hide-administration-group' ); ?>
        
            <div class="postbox ">
                <div class="inside">
                    Hide admin area and admin bar to users having this roles:
                    <table class="form-table">
                        <?php

                                foreach( $allRoles as $role => $role_info) { ?>

                            <tr valign="top">
                                <th scope="row">
                                    <?php echo $role_info["name"]; ?>
                                </th>
                                <td>
                           
                                    <input type="checkbox" name="hide_admin[<?php echo $role; ?>]" value="disallowed" <?php echo (get_option('hide_admin')[$role]  == 'disallowed') ? 'checked="checked"' : "" ?>/>
            
                                </td>
                                <?php 
                                } 
                                ?>
                    </table>


                </div>
            </div>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php 
        }

        public function add_settings_page(){

            $slug =  "hide_administration";
            add_menu_page('Hide admin bar & area settings', 'Hide admin bar & area ', 'administrator', $slug, array($this,"settings_page") , plugins_url('/images/icon.png', __FILE__) );
            remove_menu_page(  $slug);
        }

        public function add_action_links($links)
        {
            return array_merge( $links, array('<a href="' . admin_url( 'options-general.php?page=hide_administration' ) . '">'.'Settings'.'</a>'  ));
        }

        public function register_settings($links)
        {
            register_setting( 'hide-administration-group', 'hide_admin' );
        }

        public function run() {
            
            add_action( 'admin_init', array($this,'redirect_from_administration_area'), 100 );
            add_action( 'admin_menu', array($this,'add_settings_page'), 100 );
            add_filter('show_admin_bar', array($this,'current_user_has_allowed_administration'),99999);
            add_action( 'admin_init', array($this,'register_settings'));
            add_filter('plugin_action_links_'. plugin_basename(__FILE__),array($this,"add_action_links"));
 
        
        }

    }
 

        $hide_admin_addon = new Hide_Administration_Addon();
        $hide_admin_addon ->run();

   
}