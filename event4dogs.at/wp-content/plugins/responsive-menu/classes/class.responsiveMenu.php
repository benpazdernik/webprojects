<?php
/*
  Main Class for Responsive Menu

  Copyright 2014  Peter Featherstone <peter.featherstone@networkintellect.com>

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class ResponsiveMenu {

    /* Added in 1.8 */
    private static $success = null;
    private static $error = null;
    
    static function install() {

            add_option( 'RMVer', RM_V );

            add_option( 'RMOptions', array(
                
                'RM' => '',
                'RMBreak' => 600,
                'RMDepth' => 2,
                'RMTop' => 10,
                'RMRight' => 5,
                'RMCss' => '',
                'RMTitle' => __( 'Menu Title', 'responsive-menu' ),
                'RMLineCol' => '#FFFFFF',
                'RMClickBkg' => '#000000',
                'RMClickTitle' => '',
                'RMBkgTran' => 'checked',
                'RMFont' => '',
                'RMPos' => '',
                'RMImage' => '',
                'RMWidth' => '75',
                'RMBkg' => '#43494C',
                'RMBkgHov' => '#3C3C3C',
                'RMTitleCol' => '#FFFFFF',
                'RMTextCol' => '#FFFFFF',
                'RMBorCol' => '#3C3C3C',
                'RMTextColHov' => '#FFFFFF',
                'RMTitleColHov' => '#FFFFFF',
                
                /* Added in 1.6 */
                'RMAnim' => 'overlay',
                'RMPushCSS' => '',
                'RMTitleBkg' => '#43494C',
                'RMFontSize' => 13,
                'RMTitleSize' => 14,
                'RMBtnSize' => 13,
                'RMCurBkg' => '#43494C',
                'RMCurCol' => '#FFFFFF',
                'RMAnimSpd' => 0.5,
                
                /* Added in 1.7 */
                'RMTranSpd' => 1,
                'RMTxtAlign' => 'left',
                'RMSearch' => false,
                'RMExpand' => false,
                'RMLinkHeight' => 20,
                
                /* Added in 1.8 */
                'RMExternal' => false,
                'RMSide' => 'left',
                
                /* Added in 1.9 */
                'RMFooter' => false,
                'RMClickImg' => false,
                'RMMinify' => false,
                'RMClickClose' => false,
                'RMRemImp' => false,
                'RMX' => false,
                'RMMinWidth' => null
                
            ) );

    }

    static function menus() {

        add_menu_page( 
                
                __( 'Responsive Menu', 'responsive-menu' ), 
                __( 'Responsive Menu', 'responsive-menu' ), 
                'manage_options', 
                'responsive-menu', 
                array( 'ResponsiveMenu', 'adminPage' ), 
                RM_IMAGES . 'icon.png' 
                
                );

    }

    public static function adminPage() {
            
         if ( get_option('responsive_menu_options') && !get_option( 'RMVer' ) ) :

            add_option( 'RMVer', RM_V );
            
            // Migrate Old Data 
            $options = unserialize( get_option( 'responsive_menu_options' ) );

                add_option( 'RMOptions', array(
                    
                    'RM' => $options['reponsiveMenuMenu'],
                    'RMBreak' => $options['responsiveMenuBreakpoint'],
                    'RMDepth' => $options['reponsiveMenuDepth'],
                    'RMTop' => $options['responsiveMenuTop'],
                    'RMRight' => $options['responsiveMenuRight'],
                    'RMCss' => $options['responsiveMenuCss'],
                    'RMTitle' => $options['responsiveMenuTitle'],
                    'RMLineCol' => $options['responsiveMenuLineColour'],
                    'RMClickBkg' => $options['responsiveMenuBackgroundColour'],
                    'RMClickTitle' => $options['responsiveMenuButtonTitle'],
                    'RMBkgTran' => $options['responsiveMenuBackgroundTransparent'],
                    'RMFont' => $options['responsiveMenuFont'],
                    'RMPos' => $options['responsiveMenuFixed'],
                    'RMImage' => $options['responsiveMenuImage'],
                    'RMWidth' => $options['responsiveMenuWidth'],
                    'RMBkg' => $options['responsiveMenuMainBackground'],
                    'RMBkgHov' => $options['responsiveMenuMainBackgroundHover'],
                    'RMTitleCol' => $options['responsiveMenuMainTitleColour'],
                    'RMTextCol' => $options['responsiveMenuMainTextColour'],
                    'RMBorCol' => $options['responsiveMenuMainBorderColour'],
                    'RMTextColHov' => $options['responsiveMenuMainTextColourHover'],
                    'RMTitleColHov' => $options['responsiveMenuMainTitleColourHover'],
                    
                    /* Added in 1.6 */
                    'RMAnim' => 'overlay',
                    'RMPushCSS' => '',
                    'RMTitleBkg' => '#43494C',
                    'RMFontSize' => 13,
                    'RMTitleSize' => 14,
                    'RMBtnSize' => 13,
                    'RMCurBkg' => $options['responsiveMenuMainBackground'],
                    'RMCurCol' => $options['responsiveMenuMainTextColour'],
                    'RMAnimSpd' => 0.5,
                    
                    /* Added in 1.7 */
                    'RMTranSpd' => 1,
                    'RMTxtAlign' => 'left',
                    'RMSearch' => false,
                    'RMExpand' => false,
                    'RMLinkHeight' => 20,
                
                    /* Added in 1.8 */
                    'RMExternal' => false,
                    'RMSide' => 'left',
                    
                    /* Added in 1.9 */
                    'RMFooter' => false,
                    'RMClickImg' => false,
                    'RMMinify' => false,
                    'RMClickClose' => false,
                    'RMRemImp' => false,
                    'RMX' => false,
                    'RMMinWidth' => null 
                    
                ) );
                
        endif;
        
        if ( isset( $_POST['RMSubmit'] ) ) :

            $validated = self::validate();

        endif;

        $options = self::getOptions();
        
        ?>

        <style>

            h4
            {
                font-weight: bold;
                margin: 10px 0px 0px 0px;
            }

            h5
            {
                font-size: 11px;
                margin: 0px 0px 10px 0px;
            }

            .numberInput
            {
                width: 50px;
            }

            table
            {
                width: 100%;
            }

            table td
            {
                width: 48%;
                padding-right: 2%;
            }
            
            .default
            {
                font-size: 8px;
                font-style: italic;
            }

            .error
            {
                color: red;
            }
            
            .success
            {
                color: green;
            }
            
            @media only screen and ( min-width : 0px ) and ( max-width : 600px ) { 

                table, table td, table tr
                {
                    width: 100%;
                    display: block;
                }
                
            }
            
        </style>

        <script>

            jQuery( document ).ready( function( $ ) {

                $( '.colourPicker' ).wpColorPicker( );

                var custom_uploader;

                $( '.RMImageButton' ).click( function( e ) {

                    e.preventDefault();
                    window.imgFor = $( this ).attr( 'for' );
                    
                    //If the uploader object has already been created, reopen the dialog
                    if (custom_uploader) {

                        custom_uploader.open();
                        return;
                    }

                    //Extend the wp.media object
                    custom_uploader = wp.media.frames.file_frame = wp.media( {
                        title: 'Choose Image',
                        button: {
                            text: 'Choose Image',
                            id: 'test'
                        },
                        multiple: false
                    } );

                    //When a file is selected, grab the URL and set it as the text field's value
                    custom_uploader.on( 'select', function() {
  
                        attachment = custom_uploader.state().get('selection').first().toJSON();
                        
                        $( '#' + window.imgFor ).val( attachment.url );
                        
                    });

                    //Open the uploader dialog
                    custom_uploader.open();

                });

            });

        </script>

        <div class="wrap">

            <form action="" method="post">

                <h2><?php _e( 'Responsive Menu Options', 'responsive-menu' ); ?></h2>

                <?php if ( isset( self::$success ) ) : ?>

                    <div id="message" class="updated below-h2 cookieBannerSuccess">
                        <p><?php echo self::$success; ?>.</p>
                    </div>
                
                <?php elseif( isset( self::$error ) ) : ?>
                
                    <div id="message" class="error below-h2 cookieBannerSuccess">
                        <p><?php echo self::$error; ?>.</p>
                    </div>
                
                <?php endif; ?>

                <hr />

                <h3><?php _e( 'Initial Checks', 'responsive-menu' ); ?></h3>

                <h4><?php _e( 'Viewport Meta Tag Check', 'responsive-menu' ); ?>
                    
                    <?php if ( $portTag = self::checkViewPortTag() ) : ?>
                    
                        <span class='success'> - <?php _e( 'Below Viewport Meta Tag Found', 'responsive-menu' ); ?></span>
                    
                    <?php else : 
                        
                        $portTag = null; 
                    
                    endif; ?>
                    
                </h4> 

                <?php
                if ( $portTag ) :
                    echo "&lt;meta name='viewport' content='" . $portTag . "' /&gt;";
                else :
                    echo "<span class='error'>" . __( 'Viewport Meta Tag Not Found', 'responsive-menu' ) . "</span>";
                endif;
                ?>

                <h4><?php _e( 'Recommended', 'responsive-menu' ); ?></h4>
                
                &lt;meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' /&gt;
                
                <br /><br />

                <hr />

                <h3><?php _e( 'Menu Settings', 'responsive-menu' ); ?></h3>

                <table>
                    
                    <tr>
                        <td>

                            <h4><?php _e( 'Menu Title', 'responsive-menu' ); ?></h4> 

                            <h5><?php _e( 'This is the title at the top of the responsive menu', 'responsive-menu' ); ?></h5>

                            <input 
                                type="text" 
                                name="RMTitle" 
                                value="<?php echo isset($options['RMTitle']) ? $options['RMTitle'] : ''; ?>" 
                                />

                        </td>
                        <td>

                            <h4><?php _e( 'Menu Image', 'responsive-menu' ); ?></h4> 

                            <h5><?php _e( 'This is the image that sits next to the responsive menu title. The best size is 32px x 32px', 'responsive-menu' ); ?></h5>

                            <input 
                                type="text" 
                                id="RMImage" 
                                name="RMImage" 
                                value="<?php echo isset($options['RMImage']) ? $options['RMImage'] : ''; ?>" 
                                />
                            
                            <input 
                                type="button" 
                                id="RMImageButton" 
                                value="<?php _e( 'Upload Image', 'responsive-menu' ); ?>" 
                                class="button RMImageButton" 
                                for="RMImage"
                                />

                        </td>
                    </tr>
 
                    <tr>
                        <td>
                            <h4><?php _e( 'Menu Button Title', 'responsive-menu' ); ?></h4> 

                            <h5><?php _e( 'This is the title under the 3 lines of the menu button', 'responsive-menu' ); ?></h5>

                            <input 
                                type="text" 
                                name="RMClickTitle" 
                                value="<?php echo isset($options['RMClickTitle']) ? $options['RMClickTitle'] : ''; ?>" 
                                />
                        </td>
                        <td>

                            <h4><?php _e( 'Click Menu Image', 'responsive-menu' ); ?></h4> 

                            <h5><?php _e( 'This is the click image button that replaces the 3 lines. If empty, the 3 lines will be used', 'responsive-menu' ); ?></h5>

                            <input 
                                type="text" 
                                id="RMClickImg" 
                                name="RMClickImg" 
                                value="<?php echo isset($options['RMClickImg']) ? $options['RMClickImg'] : ''; ?>" 
                                />
                            
                            <input 
                                type="button" 
                                id="RMImageButton" 
                                value="<?php _e( 'Upload Image', 'responsive-menu' ); ?>" 
                                class="button RMImageButton" 
                                for="RMClickImg"
                                />

                        </td>
                    </tr>
                    
                    <tr>
                        <td>
                            <h4><?php _e( 'Choose Menu To Responsify', 'responsive-menu' ); ?></h4> 

                            <h5><?php _e( 'This is the menu that will be used responsively', 'responsive-menu' ); ?>.</h5>

                            <?php if ( count( get_terms( 'nav_menu' ) ) > 0) : ?>

                                <select name="RM">

                                    <?php foreach( get_terms( 'nav_menu' ) as $menu ) : ?>

                                        <option 
                                            value="<?php echo $menu->slug; ?>"
                                            <?php echo $menu->slug == $options['RM'] ? 'selected="selected">' : '>'; ?>
                                            <?php echo $menu->name; ?>
                                        </option>

                                <?php endforeach; ?>

                            </select>

                            <?php else : ?>

                                <span style="color: red;"><?php _e( "You haven't set up any site menus yet", "responsive-menu" ); ?>.</span>

                            <?php endif; ?>  
                        </td>
                        <td>
                        
                        <h4><?php _e( 'Slide Side', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the side of the screen from which the menu will slide', 'responsive-menu' ); ?></h5>

                        <select name="RMSide">

                                <option 
                                    value="left"
                                    <?php echo isset( $options['RMSide'] ) && $options['RMSide'] == 'left' ? 'selected="selected">' : '>'; ?>
                                    Left
                                </option>
                                
                                <option 
                                    value="right"
                                    <?php echo isset( $options['RMSide'] ) && $options['RMSide'] == 'right' ? 'selected="selected">' : '>'; ?>
                                    Right
                                </option>
                                
                        </select>     

                            
                        </td>
                </tr>
                
                <tr>
                    <td>
                        
                        <h4><?php _e( 'Menu Breakpoint', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the point where the responsive menu will be visible in px width of the browser', 'responsive-menu' ); ?></h5>

                        <input 
                            class="numberInput" 
                            type="text" 
                            name="RMBreak" 
                            value="<?php echo isset($options['RMBreak']) ? $options['RMBreak'] : ''; ?>" 
                            /> <?php _e( 'px', 'responsive-menu' ); ?>

                    </td>
                    <td>
                        
                        <h4><?php _e( 'CSS of Menu To Hide', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the CSS of the menu you want to hide once the responsive menu shows', 'responsive-menu' ); ?> - <?php _e( 'e.g', 'responsive-menu' ); ?> #primary-nav, .menu</h5>

                        <input 
                            type="text" 
                            name="RMCss" 
                            value="<?php echo isset($options['RMCss']) ? $options['RMCss'] : ''; ?>" 
                            />
                        
                    </td>
                </tr>
                
                <tr>
                    <td>
                        
                        <h4><?php _e( 'Menu Depth', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is how deep into your menu tree will be visible (max 3)', 'responsive-menu' ); ?></h5>

                        <select name="RMDepth">

                            <?php for ($i = 1; $i < 4; $i++) : ?>

                                <option 
                                    value="<?php echo $i; ?>"
                                    <?php echo isset( $options['RMDepth'] ) &&$i == $options['RMDepth'] ? 'selected="selected">' : '>'; ?>
                                    <?php echo $i; ?>
                                </option>

                            <?php endfor; ?>

                        </select>
                        
                    </td>
                    <td>
                    
                        <h4><?php _e( 'Menu Width', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the width the menu takes up across the page once expanded', 'responsive-menu' ); ?>. <span class="default"><?php _e( 'default', 'responsive-menu' ); ?>: 75</span></h5>

                        <input 
                            class="numberInput" 
                            type="text" 
                            name="RMWidth" 
                            value="<?php echo isset($options['RMWidth']) ? $options['RMWidth'] : ''; ?>" 
                            /> %

                    </td>
                </tr>                
                <tr>
                    <td>
                        
                        <h4><?php _e( 'Remove Search Box', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'Tick if you would like to remove the search box', 'responsive-menu' ); ?></h5>

                    <input 
                        type="checkbox" 
                        name="RMSearch" 
                        id="RMSearch"
                        value="search"
                        <?php echo isset( $options['RMSearch'] ) && $options['RMSearch'] == 'search' ? ' checked="checked" ' : ''; ?>
                        />
                    
                    </td>
                    <td>
                    
                        <h4><?php _e( 'Auto Expand Sub-Menus', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'Tick if you would like sub-menus to be automatically expanded', 'responsive-menu' ); ?></h5>

                        <input 
                            type="checkbox" 
                            name="RMExpand" 
                            id="RMExpand"
                            value="expand"
                            <?php echo isset( $options['RMExpand'] ) && $options['RMExpand'] == 'expand' ? ' checked="checked" ' : ''; ?>
                            />

                    </td>                
                </tr>  
                
                <tr>
                    <td>
                        
                        <h4><?php _e( 'Include CSS/JS as external files', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'Tick if you would like to include CSS and jQuery as external files', 'responsive-menu' ); ?></h5>

                        <input 
                            type="checkbox" 
                            name="RMExternal" 
                            id="RMExternal"
                            value="external"
                            <?php echo isset( $options['RMExternal'] ) && $options['RMExternal'] == 'external' ? ' checked="checked" ' : ''; ?>
                            />
                    
                    </td>
                    <td>

                        <h4><?php _e( 'Include script in footer', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'Tick if you would like to include your jQuery script in footer', 'responsive-menu' ); ?></h5>

                        <input 
                            type="checkbox" 
                            name="RMFooter" 
                            id="RMFooter"
                            value="footer"
                            <?php echo isset( $options['RMFooter'] ) && $options['RMFooter'] == 'footer' ? ' checked="checked" ' : ''; ?>
                            />
                        
                    </td>                
                </tr>  
                
                          <tr>
                    <td>
                        <h4><?php _e( 'Close menu on click', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'Tick if you would like to close the menu on each link click, useful for single page sites', 'responsive-menu' ); ?></h5>

                        <input 
                            type="checkbox" 
                            name="RMClickClose" 
                            id="RMClickClose"
                            value="close"
                            <?php echo isset( $options['RMClickClose'] ) && $options['RMClickClose'] == 'close' ? ' checked="checked" ' : ''; ?>
                            />
       
                    
                    </td>
                    <td>
                        <h4><?php _e( 'Minify output', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'Tick if you would like to minify the script/style output. Saves up to 50% in file size', 'responsive-menu' ); ?></h5>

                        <input 
                            type="checkbox" 
                            name="RMMinify" 
                            id="RMMinify"
                            value="minify"
                            <?php echo isset( $options['RMMinify'] ) && $options['RMMinify'] == 'minify' ? ' checked="checked" ' : ''; ?>
                            />
                    </td>                
                </tr>  
                
            </table>
                
            <hr />
                
            <h3><?php _e( 'Location Settings', 'responsive-menu' ); ?></h3>

            <table>
                <tr>
                    <td>

                        <h4><?php _e( 'Top', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the distance from the top of the page in px that the menu will be displayed', 'responsive-menu' ); ?></h5>

                        <input 
                            class="numberInput" 
                            type="text" 
                            name="RMTop" 
                            value="<?php echo isset($options['RMTop']) ? $options['RMTop'] : ''; ?>" 
                            /> <?php _e( 'px', 'responsive-menu' ); ?>

                    </td>
                    <td>

                        <h4><?php _e( 'Right', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the distance from the right of the page in % that the menu will be displayed', 'responsive-menu' ); ?></h5>

                        <input 
                            class="numberInput" 
                            type="text" 
                            name="RMRight" 
                            value="<?php echo isset($options['RMRight']) ? $options['RMRight'] : ''; ?>" 
                            /> %

                    </td>
                </tr>
            </table>

            <hr />

            <h3><?php _e( 'Colour Settings', 'responsive-menu' ); ?></h3>

            <table>
                <tr>
                    <td>

                        <h4><?php _e( 'Menu Line & Text Colour', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the colour of the 3 lines and text for the menu button', 'responsive-menu' ); ?></h5>

                        <input 
                            type="text" 
                            name="RMLineCol" 
                            id="RMLineCol" 
                            class="colourPicker" 
                            value="<?php echo isset($options['RMLineCol']) ? $options['RMLineCol'] : ''; ?>" 
                            />

                    </td>
                    <td>

                        <h4><?php _e( 'Menu Button Background Colour', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the background colour of the 3 lines container', 'responsive-menu' ); ?></h5>

                        <input 
                            type="text" 
                            name="RMClickBkg" 
                            id="RMClickBkg" 
                            class="colourPicker" 
                            value="<?php echo isset($options['RMClickBkg']) ? $options['RMClickBkg'] : ''; ?>" 
                            />

                    </td>
                </tr>
                <tr>
                    <td>

                        <h4><?php _e( 'Menu Background Colour', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the background colour of the expanded menu', 'responsive-menu' ); ?></h5>

                        <input 
                            type="text" 
                            name="RMBkg" 
                            id="RMBkg" 
                            class="colourPicker" 
                            value="<?php echo isset($options['RMBkg']) ? $options['RMBkg'] : ''; ?>" 
                            />

                    </td>
                    <td>

                        <h4><?php _e( 'Menu Background Hover Colour', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the hover background colour of the expanded menu', 'responsive-menu' ); ?></h5>

                        <input 
                            type="text" 
                            name="RMBkgHov" 
                            id="RMBkgHov" 
                            class="colourPicker" 
                            value="<?php echo isset($options['RMBkgHov']) ? $options['RMBkgHov'] : ''; ?>" 
                            />

                    </td>
                </tr>
                <tr>

                    <td>

                        <h4><?php _e( 'Menu Title Colour', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the text colour of the expanded menu title', 'responsive-menu' ); ?></h5>

                        <input 
                            type="text" 
                            name="RMTitleCol" 
                            id="RMTitleCol" 
                            class="colourPicker" 
                            value="<?php echo isset($options['RMTitleCol']) ? $options['RMTitleCol'] : ''; ?>" 
                            />

                    </td>
                    <td>

                        <h4><?php _e( 'Menu Title Hover Colour', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the hover colour of the expanded menu title', 'responsive-menu' ); ?></h5>

                        <input 
                            type="text" 
                            name="RMTitleColHov" 
                            id="RMTitleColHov" 
                            class="colourPicker" 
                            value="<?php echo isset($options['RMTitleColHov']) ? $options['RMTitleColHov'] : ''; ?>" 
                            />

                    </td>
                </tr>
                <tr>
                    <td>

                        <h4><?php _e( 'Menu Text Colour', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the text colour of the expanded menu links', 'responsive-menu' ); ?></h5>

                        <input 
                            type="text" 
                            name="RMTextCol" 
                            id="RMTextCol" 
                            class="colourPicker" 
                            value="<?php echo isset($options['RMTextCol']) ? $options['RMTextCol'] : ''; ?>" 
                            />

                    </td>
                    <td>

                        <h4><?php _e( 'Menu Text Hover Colour', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the text hover colour of the expanded menu links', 'responsive-menu' ); ?></h5>

                        <input 
                            type="text" 
                            name="RMTextColHov" 
                            id="RMTextColHov" 
                            class="colourPicker" 
                            value="<?php echo isset($options['RMTextColHov']) ? $options['RMTextColHov'] : ''; ?>" 
                            />

                    </td>
                </tr>
                <tr>
                    <td>

                        <h4><?php _e( 'Menu Link Border Colour', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the border colour of the expanded menu titles', 'responsive-menu' ); ?></h5>

                        <input 
                            type="text" 
                            name="RMBorCol" 
                            id="RMBorCol" 
                            class="colourPicker" 
                            value="<?php echo isset($options['RMBorCol']) ? $options['RMBorCol'] : ''; ?>" 
                            />

                    </td>
                    <td>

                        <h4><?php _e( 'Title Background Colour', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the background colour of the expanded menu title', 'responsive-menu' ); ?></h5>

                        <input 
                            type="text" 
                            name="RMTitleBkg" 
                            id="RMTitleBkg" 
                            class="colourPicker" 
                            value="<?php echo isset( $options['RMTitleBkg'] ) ? $options['RMTitleBkg'] : ''; ?>" 
                            />

                    </td>
                </tr>
                <tr>
                    <td>

                        <h4><?php _e( 'Current Page Background Colour', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the background colour of the current page', 'responsive-menu' ); ?></h5>

                        <input 
                            type="text" 
                            name="RMCurBkg" 
                            id="RMCurBkg" 
                            class="colourPicker" 
                            value="<?php echo isset($options['RMCurBkg']) ? $options['RMCurBkg'] : ''; ?>" 
                            />

                    </td>
                    <td>

                        <h4><?php _e( 'Current Page Text Colour', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'This is the text colour of the current page', 'responsive-menu' ); ?></h5>

                        <input 
                            type="text" 
                            name="RMCurCol" 
                            id="RMCurCol" 
                            class="colourPicker" 
                            value="<?php echo isset($options['RMCurCol']) ? $options['RMCurCol'] : ''; ?>" 
                            />

                    </td>
                </tr>
            </table>

            <hr />
        
            <h3><?php _e( 'Style Settings', 'responsive-menu' ); ?></h3>
        
            <table>
                <tr>
                    <td>

                        <h4><?php _e( 'Menu Background Transparent', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'Tick this if you would like a transparent background', 'responsive-menu' ); ?></h5>

                        <input 
                            type="checkbox" 
                            name="RMBkgTran" 
                            id="RMBkgTran"
                            value="checked"
                            <?php echo $options['RMBkgTran'] == 'checked' ? ' checked="checked" ' : ''; ?>
                            />

                    </td>
                    <td>

                        <h4><?php _e( 'Fixed Positioning', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'Tick this if you would like the menu button to remain in the same place when scrolling', 'responsive-menu' ); ?></h5>

                        <input 
                            type="checkbox" 
                            name="RMPos" 
                            id="RMPos"
                            value="fixed"
                            <?php echo $options['RMPos'] == 'fixed' ? ' checked="checked" ' : ''; ?>
                            />

                    </td>
                </tr>
                
                <tr>
                    <td>

                        <h4><?php _e( 'Font', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'Enter a font name below, if empty your default site font will be used', 'responsive-menu' ); ?></h5>

                        <input 
                            type="text" 
                            name="RMFont" 
                            value="<?php echo isset($options['RMFont']) ? $options['RMFont'] : ''; ?>" 
                            />

                    </td>
                    <td>    

                        <h4><?php _e( 'Font Size', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'Enter a font size in pixels below', 'responsive-menu' ); ?>. <span class='default'><?php _e( 'default', 'responsive-menu' ); ?>: 13</span></h5>

                        <input 
                            type="text" 
                            name="RMFontSize" 
                            class="numberInput" 
                            value="<?php echo isset($options['RMFontSize']) ? $options['RMFontSize'] : ''; ?>" 
                            /> <?php _e( 'px', 'responsive-menu' ); ?>

                    </td>
                </tr>  
                
                <tr>
                    <td>

                        <h4><?php _e( 'Click Button Font Size', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'Enter a click button font size in pixels below', 'responsive-menu' ); ?>. <span class='default'><?php _e( 'default', 'responsive-menu' ); ?>: 13</span></h5>

                        <input 
                            type="text" 
                            name="RMBtnSize" 
                            class="numberInput" 
                            value="<?php echo isset($options['RMBtnSize']) ? $options['RMBtnSize'] : ''; ?>" 
                            /> <?php _e( 'px', 'responsive-menu' ); ?>

                    </td>
                    <td>  

                        <h4><?php _e( 'Title Font Size', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'Enter a title font size in pixels below', 'responsive-menu' ); ?>. <span class='default'><?php _e( 'default', 'responsive-menu' ); ?>: 14</span></h5>

                        <input 
                            type="text" 
                            name="RMTitleSize" 
                            class="numberInput" 
                            value="<?php echo isset($options['RMTitleSize']) ? $options['RMTitleSize'] : ''; ?>" 
                            /> <?php _e( 'px', 'responsive-menu' ); ?>

                    </td>
                </tr>    

                <tr>
                    <td>

                        <h4><?php _e( 'Text Alignment', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'Enter a text alignment option below', 'responsive-menu' ); ?> <span class='default'><?php _e( 'default', 'responsive-menu' ); ?>: <?php _e( 'Left', 'responsive-menu' ); ?></span></h5>

                        <select name="RMTxtAlign">

                            <option 
                                value="left"
                                <?php echo isset($options['RMTxtAlign']) && 'overlay' == $options['RMTxtAlign'] ? ' selected="selected " ' : ''; ?>>
                                <?php _e( 'Left', 'responsive-menu' ); ?>
                            </option>
                            <option 
                                value="center"
                                <?php echo isset($options['RMTxtAlign']) && 'center' == $options['RMTxtAlign'] ? ' selected="selected " ' : ''; ?>>
                                <?php _e( 'Centre', 'responsive-menu' ); ?>
                            </option>  
                            <option 
                                value="right"
                                <?php echo isset($options['RMTxtAlign']) && 'right' == $options['RMTxtAlign'] ? ' selected="selected " ' : ''; ?>>
                                <?php _e( 'Right', 'responsive-menu' ); ?>
                            </option> 

                        </select>

                    </td>               
                    <td>                    

                        <h4><?php _e( 'Links Height', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'Enter a link height size in pixels below', 'responsive-menu' ); ?>. <span class='default'><?php _e( 'default', 'responsive-menu' ); ?>: 20</span></h5>

                        <input 
                            type="text" 
                            name="RMLinkHeight" 
                            class="numberInput" 
                            value="<?php echo isset($options['RMLinkHeight']) ? $options['RMLinkHeight'] : ''; ?>" 
                            /> <?php _e( 'px', 'responsive-menu' ); ?>

                    </td>
                </tr>

                <tr>
                    <td>

                        <h4><?php _e( 'Remove CSS !important tags', 'responsive-menu' ); ?></h4> 

                        <h5>
                            <?php _e( 'Tick this if you would like to remove the !important tags from the CSS', 'responsive-menu' ); ?>. 
                            <?php _e( 'Ticking this will make it easier to over-ride the styles but may make the default settings not display well', 'responsive-menu' ); ?>
                        </h5>
                        <input 
                            type="checkbox" 
                            name="RMRemImp" 
                            id="RMRemImp"
                            value="remove"
                            <?php echo $options['RMRemImp'] == 'remove' ? ' checked="checked" ' : ''; ?>
                            />

                    </td>
                    <td>
                        
                        <h4><?php _e( 'Change click menu to an x on click', 'responsive-menu' ); ?></h4> 

                        <h5>
                            <?php _e( 'Tick this if you would like the 3 lines to turn into an x once clicked', 'responsive-menu' ); ?>. 
                        </h5>
                        <input 
                            type="checkbox" 
                            name="RMX" 
                            id="RMX"
                            value="rmx"
                            <?php echo $options['RMX'] == 'rmx' ? ' checked="checked" ' : ''; ?>
                            />

                    </td>
                </tr>
                <tr>
                    <td>                    

                        <h4><?php _e( 'Minimum Width', 'responsive-menu' ); ?></h4> 

                        <h5><?php _e( 'Enter a minimum menu width size in pixels below', 'responsive-menu' ); ?>.</h5>

                        <input 
                            type="text" 
                            name="RMMinWidth" 
                            class="numberInput" 
                            value="<?php echo isset($options['RMMinWidth']) ? $options['RMMinWidth'] : ''; ?>" 
                            /> <?php _e( 'px', 'responsive-menu' ); ?>

                    </td>
                    <td>                    

                    </td>
                </tr>
            </table>

        <hr />        

        <h3><?php _e( 'Animation Settings', 'responsive-menu' ); ?></h3>

        <?php if( $options['RMAnim'] == 'push' && $portTag != 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' ) : ?>

            <span class='error'>
                <?php _e( 'Warning: The Push Animation requires you to place the following meta tag in your site header', 'responsive-menu' ); ?>:
            </span>
        
            <br />
            
            <span class='success'>
                &lt;meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" /&gt;
            </span>
        
        <?php endif; ?>
        
                <table>
                    <tr>
                        <td>

                            <h4><?php _e( 'Slide Animation', 'responsive-menu' ); ?></h4> 

                            <h5><?php _e( 'Choose the type of animation applied to the menu', 'responsive-menu' ); ?></h5>

                            <select name="RMAnim">

                                <option 
                                    value="overlay"
                                    <?php echo 'overlay' == $options['RMAnim'] ? ' selected="selected " ' : ''; ?>>
                                    <?php _e( 'Overlay', 'responsive-menu' ); ?>
                                </option>
                                <option 
                                    value="push"
                                    <?php echo 'push' == $options['RMAnim'] ? ' selected="selected " ' : ''; ?>>
                                    <?php _e( 'Push', 'responsive-menu' ); ?>
                                </option>      

                            </select>

                        </td>
                        <td>

                            <h4><?php _e( 'Page Wrappers CSS', 'responsive-menu' ); ?></h4> 

                            <h5><?php _e( 'This is the CSS of the wrapper you want to push when using the push animation', 'responsive-menu' ); ?> (<?php _e( 'e.g', 'responsive-menu' ); ?> - #pushWrapper)</h5>

                            <input 
                                type="text" 
                                name="RMPushCSS" 
                                value="<?php echo isset($options['RMPushCSS']) ? $options['RMPushCSS'] : ''; ?>" 
                                />

                        </td>
                    </tr>
                       
                    <tr>
                        <td>

                            <h4><?php _e( 'Animation Speed', 'responsive-menu' ); ?></h4> 

                            <h5><?php _e( 'Enter a speed in seconds below of the slide animation', 'responsive-menu' ); ?>. <span class="default"><?php _e( 'default', 'responsive-menu' ); ?>: 0.5</span></h5>

                            <input 
                                type="text" 
                                name="RMAnimSpd" 
                                class="numberInput" 
                                value="<?php echo isset($options['RMAnimSpd']) ? $options['RMAnimSpd'] : ''; ?>" 
                                /> <?php _e( 'seconds', 'responsive-menu' ); ?>

                        </td>
                        <td>

                            <h4><?php _e( 'Transition Speed', 'responsive-menu' ); ?></h4> 

                            <h5><?php _e( 'Enter a speed in seconds below of the hover transition effect', 'responsive-menu' ); ?>. <span class="default"><?php _e( 'default', 'responsive-menu' ); ?>: 1</span></h5>

                            <input 
                                type="text" 
                                name="RMTranSpd" 
                                class="numberInput" 
                                value="<?php echo isset($options['RMTranSpd']) ? $options['RMTranSpd'] : ''; ?>" 
                                /> <?php _e( 'seconds', 'responsive-menu' ); ?>
                    
                        </td>
                    </tr>
                    
                </table>
 
                <br /><br />

                <input 
                    type="submit" 
                    class="button button-primary" 
                    name="RMSubmit" 
                    value="<?php _e( 'Update Responsive Menu Options', 'responsive-menu' ); ?>"
                    />

            </form>

        </div>

        <?php
    }

    private static function validate() {

        if ( isset($_POST['RMSubmit'] ) ) :

            // Initialise Variables Correctly
            $RM = isset($_POST['RM']) ? $_POST['RM'] : '';
            $RMTitle = isset($_POST['RMTitle']) ? $_POST['RMTitle'] : '';
            $RMBreak = isset($_POST['RMBreak']) ? $_POST['RMBreak'] : '';
            $RMDepth = isset($_POST['RMDepth']) ? $_POST['RMDepth'] : '1';
            $RMTop = isset($_POST['RMTop']) ? $_POST['RMTop'] : '';
            $RMRight = isset($_POST['RMRight']) ? $_POST['RMRight'] : '';
            $RMCss = isset($_POST['RMCss']) ? $_POST['RMCss'] : '';
            $RMLineCol = isset($_POST['RMLineCol']) ? $_POST['RMLineCol'] : '';
            $RMClickBkg = isset($_POST['RMClickBkg']) ? $_POST['RMClickBkg'] : '';
            $RMClickTitle = isset($_POST['RMClickTitle']) ? $_POST['RMClickTitle'] : '';
            $RMBkgTran = isset($_POST['RMBkgTran']) ? $_POST['RMBkgTran'] : '';
            $RMPos = isset($_POST['RMPos']) ? $_POST['RMPos'] : '';
            $RMImage = isset($_POST['RMImage']) ? $_POST['RMImage'] : '';
            $RMWidth = isset($_POST['RMWidth']) ? $_POST['RMWidth'] : '75';
            $RMBkg = isset($_POST['RMBkg']) ? $_POST['RMBkg'] : '#43494C';
            $RMBkgHov = isset($_POST['RMBkgHov']) ? $_POST['RMBkgHov'] : '#3C3C3C';
            $RMTitleCol = isset($_POST['RMTitleCol']) ? $_POST['RMTitleCol'] : '#FFFFFF';
            $RMTextCol = isset($_POST['RMTextCol']) ? $_POST['RMTextCol'] : '#FFFFFF';
            $RMBorCol = isset($_POST['RMBorCol']) ? $_POST['RMBorCol'] : '#3C3C3C';
            $RMTextColHov = isset($_POST['RMTextColHov']) ? $_POST['RMTextColHov'] : '#FFFFFF';
            $RMTitleColHov = isset($_POST['RMTitleColHov']) ? $_POST['RMTitleColHov'] : '#FFFFFF';
            
            /* Added in 1.6 */
            $RMAnim = isset($_POST['RMAnim']) ? $_POST['RMAnim'] : 'overlay';
            $RMPushCSS = isset($_POST['RMPushCSS']) ? $_POST['RMPushCSS'] : '';
            $RMTitleBkg = isset($_POST['RMTitleBkg']) ? $_POST['RMTitleBkg'] : '#43494C';
            $RMFont =  isset($_POST['RMFont']) ? $_POST['RMFont'] : '';
            $RMFontSize = isset($_POST['RMFontSize']) ? $_POST['RMFontSize'] : 13;
            $RMTitleSize = isset($_POST['RMTitleSize']) ? $_POST['RMTitleSize'] : 14;
            $RMBtnSize = isset($_POST['RMBtnSize']) ? $_POST['RMBtnSize'] : 13;
            $RMCurBkg = isset($_POST['RMCurBkg']) ? $_POST['RMCurBkg'] : $RMBkg;
            $RMCurCol = isset($_POST['RMCurCol']) ? $_POST['RMCurCol'] : $RMTextCol;
            $RMAnimSpd = isset($_POST['RMAnimSpd']) ? $_POST['RMAnimSpd'] : 0.5;
            
            /* Added in 1.7 */
            $RMTranSpd = isset($_POST['RMTranSpd']) ? $_POST['RMTranSpd'] : 1;
            $RMTxtAlign = isset($_POST['RMTxtAlign']) ? $_POST['RMTxtAlign'] : 'left';
            $RMSearch = isset($_POST['RMSearch']) ? $_POST['RMSearch'] : false;
            $RMExpand = isset($_POST['RMExpand']) ? $_POST['RMExpand'] : false;
            $RMLinkHeight = isset($_POST['RMLinkHeight']) ? $_POST['RMLinkHeight'] : 20;
                    
            /* Added in 1.8 */
            $RMExternal = isset( $_POST['RMExternal'] ) ? $_POST['RMExternal'] : false;
            $RMSide = isset( $_POST['RMSide'] ) ? $_POST['RMSide'] : 'left';
            
            /* Added in 1.9 */
            $RMFooter = isset( $_POST['RMFooter'] ) ? $_POST['RMFooter'] : false;
            $RMClickImg = isset( $_POST['RMClickImg'] ) ? $_POST['RMClickImg'] : false;
            $RMMinify = isset( $_POST['RMMinify'] ) ? $_POST['RMMinify'] : false;
            $RMClickClose = isset( $_POST['RMClickClose'] ) ? $_POST['RMClickClose'] : false;
            $RMRemImp = isset( $_POST['RMRemImp'] ) ? $_POST['RMRemImp'] : false;  
              
            $RMX = isset( $_POST['RMX'] ) ? $_POST['RMX'] : false;
            $RMMinWidth = isset( $_POST['RMMinWidth'] ) ? $_POST['RMMinWidth'] : null;
                    
            $optionsArray = array(
                // Filter Input Correctly
                'RM' => self::filterInput($RM),
                'RMBreak' => intval($RMBreak),
                'RMDepth' => intval($RMDepth),
                'RMTop' => intval($RMTop),
                'RMRight' => intval($RMRight),
                'RMCss' => self::filterInput($RMCss),
                'RMTitle' => self::filterInput($RMTitle),
                'RMLineCol' => self::filterInput($RMLineCol),
                'RMClickBkg' => self::filterInput($RMClickBkg),
                'RMClickTitle' => self::filterInput($RMClickTitle),
                'RMBkgTran' => self::filterInput($RMBkgTran),
                'RMFont' => self::filterInput($RMFont),
                'RMPos' => self::filterInput($RMPos),
                'RMImage' => self::filterInput($RMImage),
                'RMWidth' => intval($RMWidth),
                'RMBkg' => self::filterInput($RMBkg),
                'RMBkgHov' => self::filterInput($RMBkgHov),
                'RMTitleCol' => self::filterInput($RMTitleCol),
                'RMTextCol' => self::filterInput($RMTextCol),
                'RMBorCol' => self::filterInput($RMBorCol),
                'RMTextColHov' => self::filterInput($RMTextColHov),
                'RMTitleColHov' => self::filterInput($RMTitleColHov),
                        
                /* Added in 1.6 */
                'RMAnim' => self::filterInput($RMAnim),
                'RMPushCSS' => self::filterInput($RMPushCSS),
                'RMTitleBkg' => self::filterInput( $RMTitleBkg ),
                'RMFontSize' => intval( $RMFontSize ),
                'RMTitleSize' => intval( $RMTitleSize ),
                'RMBtnSize' => intval( $RMBtnSize ),
                'RMCurBkg' => self::filterInput( $RMCurBkg ),
                'RMCurCol' => self::filterInput( $RMCurCol ),
                'RMAnimSpd' => floatval( $RMAnimSpd ),
                        
                /* Added in 1.7 */
                'RMTranSpd' => floatval( $RMTranSpd ),
                'RMTxtAlign' => self::filterInput( $RMTxtAlign ),
                'RMSearch' => self::filterInput( $RMSearch ),
                'RMExpand' => self::filterInput( $RMExpand ),    
                'RMLinkHeight' => intval( $RMLinkHeight ),
                
                /* Added in 1.8 */
                'RMExternal' => self::filterInput( $RMExternal ),
                'RMSide' => self::filterInput( $RMSide ),
                
                /* Added in 1.9 */
                'RMFooter' => self::filterInput( $RMFooter ),    
                'RMClickImg' => self::filterInput( $RMClickImg ),
                'RMMinify' => self::filterInput( $RMMinify ),
                'RMClickClose' => self::filterInput( $RMClickClose ),
                'RMRemImp' => self::filterInput( $RMRemImp ),
                'RMX' => self::filterInput( $RMX ),
                'RMMinWidth' => intval( $RMMinWidth )
            
            );
            
            // Update Submitted Options 
            update_option( 'RMOptions', $optionsArray );

            /* Create Css & JS Files If Required */
            if( $RMExternal == 'external' ) :
                
                self::createDataFolders();
                
                $css = self::getCSS( 'strip_tags' );
   
                /* Added 1.9 */
                if( $RMMinify ) $css = self::Minify( $css );

                $cssFile = self::createCSSFile( $css );
                
                $js = self::getJavascript( 'strip_tags' );
                
                /* Added 1.9 */
                if( $RMMinify ) $js = self::Minify( $js );
                
                $jsFile = self::createJSFile( $js );
                
                if( $cssFile === false || $jsFile === false ) :
                    
                    self::$error = __( 'There was a problem writing the CSS and JS files, please check the plugins folder/file permissions', 'responsive-menu');
                
                else :
                    
                    self::$success = __( 'Your Responsive Menu Options and CSS/JS files have been updated successfully', 'responsive-menu' );
                
                endif;
                
            else :
            
                self::$success = __( 'Your Responsive Menu Options have been updated', 'responsive-menu' );
            
            endif;

        else :

                self::$error = __( 'There was an error updating the plugin options', 'responsive-menu');

        endif;
        
    }
    
    /* Added in 1.8 */
    static function ExternalScripts() {

        $options = self::getOptions();
        
        $inFooter = isset( $options['RMFooter'] ) && $options['RMFooter'] == 'footer' ? true : false;
        
        wp_enqueue_style( 'responsive-menu', RM_CSS_URL . 'responsive-menu-' . get_current_blog_id() . '.css', array(), '1.0', 'all' );
        wp_enqueue_script( 'responsive-menu', RM_JS_URL . 'responsive-menu-' . get_current_blog_id() . '.js', 'jquery', '1.0', $inFooter );   

    }
    
    /* Added in 1.9 */
    static function InlineCss() {
        
        echo self::Minify( self::getCSS() );
        
    }
    
    /* Added in 1.9 */
    static function InlineJavaScript() {
        
        echo self::Minify( self::getJavascript() );
        
    }
    
    static function displayMenuHtml() {
        
        echo self::getHTML();
        
    }
    
    static function Colorpicker(){ 
    
        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker');
        wp_enqueue_script( 'wp-color-picker');

    }
    
    /* Added in 1.8 */
    static function Internationalise() {
    
        load_plugin_textdomain( 'responsive-menu', false, basename( dirname( dirname( __FILE__ ) ) ) . '/translations/' );

    }

    static function jQuery() { 
    
        wp_enqueue_script( 'jquery' );
  
    }
    
    static function getJavascript( $args = null ) {

        $options = self::getOptions();

        $setHeight = $options['RMPos'] == 'fixed' ? '' : " \$RMjQuery( '#responsive-menu' ).css( 'height', \$RMjQuery( document ).height() ); ";
        $breakpoint = empty($options['RMBreak']) ? "600" : $options['RMBreak'];
        $width = empty($options['RMWidth']) ? "75" : $options['RMWidth'];
        $RMPushCSS = empty($options['RMPushCSS']) ? "" : $options['RMPushCSS'];

        $slideOpen = $options['RMAnim'] == 'push' && !empty($options['RMPushCSS']) ? " \$RMjQuery( 'body' ).addClass( 'RMPushOpen' ); " : '';
        $slideRemove = $options['RMAnim'] == 'push' && !empty($options['RMPushCSS']) ? " \$RMjQuery( 'body' ).removeClass( 'RMPushOpen' ); " : '';

        /* Added 1.8 */
        $side = empty( $options['RMSide'] ) ? 'left' : $options['RMSide']; 
        $pos = $side == 'left' ? '' : '-';

        $sideSlideOpen = $side == 'right' && empty( $slideOpen ) ? " \$RMjQuery( 'body' ).addClass( 'RMPushOpen' ); " : '';
        $sideSlideRemove =  $side == 'right' && empty( $slideRemove ) ? " \$RMjQuery( 'body' ).removeClass( 'RMPushOpen' ); " : '';
        
        $slideOver = $options['RMAnim'] == 'push' && !empty($options['RMPushCSS']) ? " \$RMjQuery( '$RMPushCSS' ).animate( { left: \"{$pos}{$width}%\" }, 500, 'linear' ); " : '';
        $slideOverCss = $options['RMAnim'] == 'push' && !empty($options['RMPushCSS']) ? " \$RMjQuery( '$RMPushCSS' ).addClass( 'RMPushSlide' ); " : '';

        $slideBack = $options['RMAnim'] == 'push' && !empty($options['RMPushCSS']) ? " \$RMjQuery( '$RMPushCSS' ).animate( { left: \"0\" }, 500, 'linear' ); " : '';
        $slideOverCssRemove = $options['RMAnim'] == 'push' && !empty($options['RMPushCSS']) ? " \$RMjQuery( '$RMPushCSS' ).removeClass( 'RMPushSlide' ); " : '';

        $speed = empty( $options['RMAnimSpd'] ) ? 500 : $options['RMAnimSpd'] * 1000;
        
        if( $options['RMX'] ) : 
        
            $closeX = " \$RMjQuery( '#click-menu #RMX' ).css( 'display', 'none' );
                        \$RMjQuery( '#click-menu #RM3Lines' ).css( 'display', 'block' ); ";
        
            $showX = " \$RMjQuery( '#click-menu #RM3Lines' ).css( 'display', 'none' );
                         \$RMjQuery( '#click-menu #RMX' ).css( 'display', 'block' ); ";        
        else :
        
            $closeX = "";
            $showX = "";
        
        endif;
            
        $js = '';
        
        if( $args != 'strip_tags' ) : 

            $js .= "<script> ";
        
        endif;
        
        $js .= "

            var \$RMjQuery = jQuery.noConflict();

            \$RMjQuery( document ).ready( function( ) {

                function openRM() {

                      $slideOpen  
                      $sideSlideOpen
                      $slideOverCss
                      $slideOver
                      $showX
                          
                      \$RMjQuery( '#responsive-menu' ).css( 'display', 'block' ); 
                      \$RMjQuery( '#responsive-menu' ).stop().animate( { $side: \"0\" }, $speed, 'linear', function() { 
                          
                        $setHeight
    
                      } ); 
                      
                }
   
                function closeRM() {

                        $slideBack
                        
                        \$RMjQuery( '#responsive-menu' ).animate( { $side: \"-{$width}%\" }, $speed, 'linear', function() { 
                      
                            $slideRemove
                            $sideSlideRemove
                            $slideOverCssRemove
                            $closeX
                            \$RMjQuery( '#responsive-menu' ).css( 'display', 'none' );  

                        } );
                        
                }
                
                isOpen = false;

                \$RMjQuery( '#click-menu' ).click( function() {
                       
                    $setHeight

                    if( !isOpen ) {

                         openRM();

                          isOpen = true;

                    } else {

                        closeRM();

                          isOpen = false;

                    }

                });
                    
                \$RMjQuery( window ).resize( function() { 
                
                    $setHeight

                    if( \$RMjQuery( window ).width() > $breakpoint ) { 

                        if( \$RMjQuery( '#responsive-menu' ).css( '$side' ) != '-{$width}%' ) {
                            
                            closeRM();

                        }

                    }

                });

            ";
        
    /* Added 1.7 */
    if ( !$options['RMExpand'] ) : 

        $js .= " 
            
                clickLink = '<span class=\"appendLink\">&#9660;</span>';
                \$RMjQuery( '#responsive-menu .responsive-menu .sub-menu' ).css( 'display', 'none' ); 
                \$RMjQuery( '#responsive-menu .responsive-menu .menu-item-has-children' ).prepend( clickLink );
                
                \$RMjQuery( '.appendLink' ).on( 'click', function() { 
                
                    \$RMjQuery( this ).nextAll( 'ul.sub-menu' ).toggle(); 

                    if( \$RMjQuery( this ).html() == '▼' ) {

                        \$RMjQuery( this ).html( '&#9650;' ); 

                    } else {

                        \$RMjQuery( this ).html( '&#9660;' );

                    }

                    $setHeight
    
                } );
                ";

    endif;
    
     /* Added 1.9 */
    if ( isset( $options['RMClickClose'] ) && $options['RMClickClose'] == 'close' ) : 

        $js .= " 
            \$RMjQuery( '#responsive-menu .responsive-menu li a' ).on( 'click', function() { 
            
                closeRM();
            
            } );";

    endif;
    
        $js .= "}); ";

        if( $args != 'strip_tags' ) : 

            $js .= "</script> ";
        
        endif;

        return $js;
            
    }

    static function getHTML() {

        $options = self::getOptions();

        $html = '<div id="responsive-menu">';
        
        if( $options['RMTitle'] || $options['RMImage'] ) :
			
            $html .= '<div id="responsive-menu-title">';

            $html .= $options['RMImage'] ? '<a href="' . get_home_url() . ' "><img src="' . $options['RMImage'] . '" class="RMImage" alt="' . $options['RMTitle'] . '" title="' . $options['RMTitle'] . '" /></a>' : '';


            $html .= '<a href="' . get_home_url() . ' ">' . $options['RMTitle'] . '</a>';
            
            $html .= '</div>';

        endif;
            
        $html .= wp_nav_menu(array(
            'menu' => $options['RM'],
            'echo' => false,
            'menu_class' => 'responsive-menu'));

        if( !$options['RMSearch'] ) : 
            
            $html .= '<form action="/" id="responsiveSearch" method="get" role="search">

                        <input type="text" name="s" value="" placeholder="Search" id="responsiveSearchInput">

                    </form>';
        
        endif;
                
        $html .= '</div>';

        $html .= '<div id="click-menu">';
        
        if( $options['RMX'] ) : 
            
            $html .= '<div class="threeLines" id="RMX">x</div>';
        
        endif;
        
        if( !$options['RMClickImg'] ) : 

            $html .= '
            <div class="threeLines" id="RM3Lines">       
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
            </div>';
                
        else :
            
            $html .= '<img id="RM3Lines" src="' . $options['RMClickImg'] . '" class="click-menu-image" />';
              
        endif;

        $html .= $options['RMClickTitle'] ? '<div id="click-menu-label">' . $options['RMClickTitle'] . '</div>' : '';

        $html .= '</div>';

        return $html;
        
    }

    static function getCSS( $args = null ) {

        $options = self::getOptions();

        /* Added 1.9 [$important] */
        $important = empty( $options['RMRemImp'] ) ? ' !important;' : ';';
        
        $position = $options['RMPos'] == 'fixed' ? 'fixed' : 'absolute';
        $overflowy = $options['RMPos'] == 'fixed' ? 'overflow-y: auto;' : '';
        $bottom = $options['RMPos'] == 'fixed' ? 'bottom: 0px;' : '';

        $right = empty($options['RMRight']) ? '0' : $options['RMRight'];
        $top = empty($options['RMTop']) ? '0' : $options['RMTop'];
        $width = empty($options['RMWidth']) ? '75' : $options['RMWidth'];
        $mainBkg = empty($options['RMBkg']) ? "#43494C" : $options['RMBkg'];
        $mainBkgH = empty($options['RMBkgHov']) ? "#3C3C3C" : $options['RMBkgHov'];
        $font = empty($options['RMFont']) ? '' : 'font-family: ' . $options['RMFont'] . $important;
        $titleCol = empty($options['RMTitleCol']) ? '#FFFFFF' : $options['RMTitleCol'];
        $titleColH = empty($options['RMTitleColHov']) ? '#FFFFFF' : $options['RMTitleColHov'];
        $txtCol = empty($options['RMTextCol']) ? "#FFFFFF" : $options['RMTextCol'];
        $txtColH = empty($options['RMTextColHov']) ? "#FFFFFF" : $options['RMTextColHov'];
        $clickCol = empty($options['RMLineCol']) ? "#FFFFFF" : $options['RMLineCol'];
        $clickBkg = empty($options['RMBkgTran']) ? "background: {$options['RMClickBkg']};" : '';
        $borCol = empty($options['RMBorCol']) ? "#3C3C3C" : $options['RMBorCol'];
        $breakpoint = empty($options['RMBreak']) ? "600" : $options['RMBreak'];
        $titleBkg = empty($options['RMTitleBkg']) ? "#43494C" : $options['RMTitleBkg'];
        
        $fontSize = empty($options['RMFontSize']) ? 13 : $options['RMFontSize'];
        $titleSize = empty($options['RMTitleSize']) ? 14 : $options['RMTitleSize'];                        
        $btnSize = empty($options['RMBtnSize']) ? 13 : $options['RMBtnSize'];
        
        $curBkg = empty($options['RMCurBkg']) ? $mainBkg : $options['RMCurBkg'];
        $curCol = empty($options['RMCurCol']) ? $txtCol : $options['RMCurCol'];
        
        /* Added 1.7 */
        $trans = empty( $options['RMTranSpd'] ) ? 1 : $options['RMTranSpd'];
        $align = empty( $options['RMTxtAlign'] ) ? 'left' : $options['RMTxtAlign'];
        $linkPadding = $options['RMTxtAlign'] == 'right' ? '12px 5% 12px 0px' : '12px 0px 12px 5%';
        $titlePadding = $options['RMTxtAlign'] == 'right' ? '20px 5% 20px 0px' : '20px 0px 20px 5%';
        $paddingAlign = $align == 'center' ? 'left' : $align;
        $height = empty( $options['RMLinkHeight'] ) ? 19 : $options['RMLinkHeight'];
        $subBtnAlign =   $align == 'right' ? 'left' : 'right';
        
        /* Added 1.8 */
        $side = empty( $options['RMSide'] ) ? 'left' : $options['RMSide'];
        
        /* Added 1.9 */
        $minWidth = empty( $options['RMMinWidth'] ) ? '' : 'min-width: ' . $options['RMMinWidth'] . 'px' . $important;
        
        $css = '';
        
        if( $args != 'strip_tags' ) : 

            $css .= "<style> ";
        
        endif;
        
        $css .= "

            #responsive-menu .appendLink, 
            #responsive-menu .responsive-menu li a, 
            #responsive-menu #responsive-menu-title a,
            #responsive-menu .responsive-menu, 
            #responsive-menu div, 
            #responsive-menu .responsive-menu li, 
            #responsive-menu 
            {
                box-sizing: content-box{$important}
                -moz-box-sizing: content-box{$important}
                -webkit-box-sizing: content-box{$important}
                -o-box-sizing: content-box{$important}
            }

            #click-menu #RMX {

                display: none;
                font-size: 24px;
                line-height: 30px;
                color: $clickCol{$important}
            }

            .RMPushOpen
            {
                width: 100%{$important}
                overflow-x: hidden{$important}
                height: 100%{$important}
            }

            .RMPushSlide
            {
                position: relative;
                $side: $width%;
            }

            #responsive-menu								
            { 
                position: $position;
                $overflowy
                $bottom
                width: $width%;
                top: 0px; 
                $side: -$width%;
                background: $mainBkg;
                z-index: 9999;  
                box-shadow: 0px 1px 8px #333333; 
                font-size: {$fontSize}px{$important}
                max-width: 999px;
                display: none;
                $minWidth
            }

            #responsive-menu .appendLink
            {
                $subBtnAlign: 0px{$important}
                position: absolute{$important}
                border: 1px solid $borCol{$important}
                padding: 12px 10px{$important}
                color: $txtCol{$important}
                background: $mainBkg{$important}
                height: {$height}px{$important}
                line-height: {$height}px{$important}
                border-right: 0px{$important}
            }
            
            #responsive-menu .appendLink:hover
            {
                cursor: pointer;
                background: $mainBkgH{$important}
                color: $txtColH{$important}
            }

            #responsive-menu .responsive-menu, 
            #responsive-menu div, 
            #responsive-menu .responsive-menu li,
            #responsive-menu
            {
                text-align: $align{$important}
            }
                    
            #responsive-menu .RMImage
            {
                vertical-align: middle;
                margin-right: 10px;
            }

            #responsive-menu,
            #responsive-menu input {
                $font
            }      
            
            #responsive-menu #responsive-menu-title			
            {
                width: 95%{$important} 
                font-size: {$titleSize}px{$important} 
                padding: $titlePadding{$important}
                margin-left: 0px{$important}
                background: $titleBkg{$important}
            }
      
            #responsive-menu #responsive-menu-title,
            #responsive-menu #responsive-menu-title a
            {
                color: $titleCol{$important}
                text-decoration: none{$important}
                white-space: pre{$important}
                overflow: hidden{$important}
            }
            
            #responsive-menu #responsive-menu-title a:hover {
                color: $titleColH{$important}
                text-decoration: none{$important}
            }
   
            #responsive-menu .appendLink,
            #responsive-menu .responsive-menu li a,
            #responsive-menu #responsive-menu-title a
            {

                transition: {$trans}s all;
                -webkit-transition: {$trans}s all;
                -moz-transition: {$trans}s all;
                -o-transition: {$trans}s all;

            }
            
            #responsive-menu .responsive-menu			
            { 
                float: left{$important}  
                width: 100%{$important} 
                list-style-type: none{$important}
                margin: 0px{$important}
            }
                        
            #responsive-menu .responsive-menu li.current_page_item > a
            {
                background: $curBkg{$important}
                color: $curCol{$important}
            }
                    
            #responsive-menu  .responsive-menu ul
            {
                margin-left: 0px{$important}
            }

            #responsive-menu .responsive-menu li		
            { 
                list-style-type: none{$important}
            }

            #responsive-menu .responsive-menu ul li:last-child	
            { 
                padding-bottom: 0px{$important} 
            }

            #responsive-menu .responsive-menu li a	
            { 
                padding: $linkPadding{$important}
                width: 95%{$important}
                display: block{$important}
                height: {$height}px{$important}
                line-height: {$height}px{$important}
                overflow: hidden{$important}
                white-space: nowrap{$important}
                color: $txtCol{$important}
                border-top: 1px solid $borCol{$important} 
                text-decoration: none{$important}
            }

            #click-menu						
            { 
                text-align: center;
                cursor: pointer; 
                width: 50px;
                font-size: {$btnSize}px{$important}
                display: none;
                position: $position;
                right: $right%;
                top: {$top}px;
                color: $clickCol;
                $clickBkg
                padding: 5px;
                border-radius: 5px;
                z-index: 9999;
            }

            #responsive-menu #responsiveSearch
            {
                display: block{$important}
                width: 95%{$important}
                padding-$paddingAlign: 5%{$important}
                border-top: 1px solid $borCol{$important} 
                clear: both{$important}
                padding-top: 10px{$important}
                padding-bottom: 10px{$important}
                height: 40px{$important}
                line-height: 40px{$important}
            }

            #responsive-menu #responsiveSearchInput
            {
                width: 91%{$important}
                padding: 5px 0px 5px 3%{$important}
                -webkit-appearance: none{$important}
                border-radius: 2px{$important}
                border: 1px solid $borCol{$important}
            }
  
            #responsive-menu .responsive-menu,
            #responsive-menu div,
            #responsive-menu .responsive-menu li
            {
                width: 100%{$important}
                float: left{$important}
                margin-left: 0px{$important}
                padding-left: 0px{$important}
            }

            #responsive-menu .responsive-menu li li a
            {
                padding-$paddingAlign: 10%{$important}
                width: 90%{$important}
                overflow: hidden{$important}
            }
 
            #responsive-menu .responsive-menu li li li a
            {
                padding-$paddingAlign: 15%{$important}
                width: 85%{$important}
                overflow: hidden{$important}
            }
            
            #responsive-menu .responsive-menu li li li li
            {
                display: none{$important}
            }
            
            #responsive-menu .responsive-menu li a:hover
            {       
                background: $mainBkgH{$important}
                color: $txtColH{$important}
                list-style-type: none{$important}
                text-decoration: none{$important}
            }
            
            #click-menu .threeLines
            {
                width: 33px{$important}
                height: 33px{$important}
                margin: auto{$important}
            }

            #click-menu .threeLines .line
            {
                height: 5px{$important}
                margin-bottom: 6px{$important}
                background: $clickCol{$important}
                width: 100%{$important}
            }

            @media only screen and ( min-width : 0px ) and ( max-width : {$breakpoint}px ) { 

                #click-menu	
                {
                    display: block;
                }

";

        $css .= $options['RMCss'] ? $options['RMCss'] . " { display: none !important; } " : '';

        if( $options['RMDepth'] == 1 ) :
            
            $css .= "
                
                #responsive-menu .responsive-menu li .appendLink,
                #responsive-menu .responsive-menu li li { display: none; }

            ";

        endif;
        
        if( $options['RMDepth'] == 2 ) :
            
            $css .= "
                
                #responsive-menu .responsive-menu li li .appendLink,
                #responsive-menu .responsive-menu li li li { display: none; }

            ";

        endif;


        $css .= " }";

        $css .= $options['RMAnim'] == 'push' && $options['RMPushCSS'] ? $options['RMPushCSS'] . " { position: relative{$important} left: 0px; } " : '';

        if( $args != 'strip_tags' ) : 

            $css .= "</style> ";
        
        endif;

        return $css;
        
    }

    private static function checkViewPortTag() {

        $metaTags = get_meta_tags( get_bloginfo( 'url' ) );

        if ($metaTags['viewport'])
            return $metaTags['viewport'];
        
    }

    private static function filterInput($input) {

        return stripslashes( strip_tags( trim( $input ) ) );
        
    }
    
    public static function getOptions() {
        
        $options = !is_array( get_option( 'RMOptions' ) ) ? unserialize( get_option( 'RMOptions' ) ) :  get_option( 'RMOptions' );

        return $options;
        
    }
    
    /* Added 1.9 *
     * Function to minify outputted JavaScript and CSS Files
     * Parts taken from
     * http://castlesblog.com/2010/august/14/php-javascript-css-minification
     * @param string $input
     * @param string $type [css, js]
     */
    
    static function Minify( $input ) {

        /* remove comments */
        $output = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $input);
        /* remove tabs, spaces, newlines, etc. */
        $output = str_replace(array("\r\n","\r","\n","\t",'  ','    ','     '), '', $output);
        /* remove other spaces before/after ; */
        $output = preg_replace(array('(( )+{)','({( )+)'), '{', $output);
        $output = preg_replace(array('(( )+})','(}( )+)','(;( )*})'), '}', $output);
        $output = preg_replace(array('(;( )+)','(( )+;)'), ';', $output);

        return $output;
        
    }
    
    /* Added 1.9 */
    static function createDataFolders() {
        
        if( !file_exists( RM_DATA ) ) mkdir( RM_DATA, 0777 );
        if( !file_exists( RM_CSS ) ) mkdir( RM_CSS, 0777 );
        if( !file_exists( RM_JS ) ) mkdir( RM_JS, 0777 ); 
                
    }
    
    /* Added 1.9 */
    static function createCSSFile( $css ) {
        
        $file = fopen( RM_CSS . 'responsive-menu-' . get_current_blog_id() . '.css', 'w' );
        $cssFile = fwrite( $file, $css );
        fclose( $file );
        
        return $cssFile;
        
    }
    
    /* Added 1.9 */
    static function createJSFile( $js ) {

        $file = fopen( RM_JS . 'responsive-menu-' . get_current_blog_id() . '.js', 'w' );
        $jsFile = fwrite( $file, $js  );
        fclose( $file );
        
        return $jsFile;
        
    }
}