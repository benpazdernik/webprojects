<?php

/*
Plugin Name: Responsive Menu
Plugin URI: http://www.peterfeatherstone.com/wordpress/responsive-menu/
Description: Highly Customisable Responsive Menu Plugin Created By Peter Featherstone @ Network Intellect.
Version: 1.9
Author: Peter Featherstone
Text Domain: responsive-menu
Author URI: http://www.peterfeatherstone.com/wordpress/responsive-menu/
License: GPL2
Tags: responsive, menu, responsive menu

    Copyright 2014  Peter Featherstone <hello@peterfeatherstone.com>

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

/* ====================
   1. Initial Setup
   =================== */

/* 1.1 Include Main Class File ============= */
require_once( 'classes/class.responsiveMenu.php' );

/* 1.2 Define Our Plugin Constants ============= */
define( 'RM_BASE', plugin_dir_url( __FILE__ ) );
define( 'RM_PLUGIN', dirname( plugin_dir_path( __FILE__ ) ) );
define( 'RM_PATH', plugin_dir_path( __FILE__ ) );

define( 'RM_IMAGES', RM_BASE . 'imgs/' );

define( 'RM_DATA', RM_PLUGIN . '/responsive-menu-data/' );
define( 'RM_JS', RM_DATA . 'js/' );
define( 'RM_CSS', RM_DATA . 'css/' );

define( 'RM_DATA_URL', plugin_dir_url( dirname( __FILE__ ) ) . 'responsive-menu-data/' );
define( 'RM_JS_URL', RM_DATA_URL . 'js/' );
define( 'RM_CSS_URL', RM_DATA_URL . 'css/' );

define( 'RM_V', 1.9 );

$options = ResponsiveMenu::getOptions();

/* 1.3 Make Sure We Have jQuery ============= */
add_action( 'wp_enqueue_scripts', array( 'ResponsiveMenu', 'jQuery' ) );

/* ====================
   2. Installation
   =================== */

register_activation_hook( __FILE__, array( 'ResponsiveMenu', 'install' ) );

/* ====================
   3. Admin Menu Registrations
   =================== */

/* 3.1 Main Menu ============= */
add_action( 'admin_menu', array( 'ResponsiveMenu', 'menus' ) );

/* ====================
   4. Display
   =================== */

/* 4.1 Display Responsive Menu on Site ============= */
if( !is_admin() ) :

    add_action( 'wp_footer', array( 'ResponsiveMenu', 'displayMenuHtml' ) );

    if( isset( $options['RMExternal'] ) && $options['RMExternal'] == 'external' ) :
        
        add_action( 'wp_enqueue_scripts', array( 'ResponsiveMenu', 'ExternalScripts' ) );
    
    else :
        
        $inFooter = isset( $options['RMFooter'] ) && $options['RMFooter'] == 'footer' ? 'wp_footer' : 'wp_head';
    
        add_action( 'wp_head', array( 'ResponsiveMenu', 'InlineCSS' ) ); 
        add_action( $inFooter, array( 'ResponsiveMenu', 'InlineJavaScript' ) ); 
        
    endif;
    
endif;

/* 4.2 Add Colour Picker to Admin Pages ============= */
if( is_admin() && isset( $_GET['page'] ) && $_GET['page'] == 'responsive-menu' ) :

    add_action( 'admin_enqueue_scripts', array( 'ResponsiveMenu', 'Colorpicker' ) );
    add_action( 'plugins_loaded', array( 'ResponsiveMenu', 'Internationalise' ) );
    
endif;

/* ====================
   5. Upgrade Functions
   =================== */

/* 5.1 If upgrade, check version and then create files if necessary */
if( get_option( 'RMVer' ) != RM_V ) :
    
    if( $options['RMExternal'] ) :
        
        ResponsiveMenu::createDataFolders();
    
        $css = ResponsiveMenu::getCSS( 'strip_tags' );

        /* Added 1.9 */
        if( $options['RMMinify'] ) $css = ResponsiveMenu::Minify( $css );

        ResponsiveMenu::createCSSFile( $css );

        $js = ResponsiveMenu::getJavascript( 'strip_tags' );

        /* Added 1.9 */
        if( $options['RMMinify'] ) $js = ResponsiveMenu::Minify( $js );

        ResponsiveMenu::createJSFile( $js );
        
    endif;
    
    /* Update option version so this doesn't get called again */
    update_option( 'RMVer', RM_V );
    
endif;