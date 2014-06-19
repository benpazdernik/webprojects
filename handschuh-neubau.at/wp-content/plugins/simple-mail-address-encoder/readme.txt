=== Plugin Name ===
Contributors: bannerweb
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FVDEBJ3XBXQBU
Tags: mail, address, encrypt, secure, hide, encode, email, e-mail, posts, post, page, pages, plugin, bannerweb, sidebar, widget, widgets, custom, meta, mailto, link
Requires at least: 3.0
Tested up to: 3.6.1
Stable tag: 1.5.4

Automatically encodes every e-mail address in a mailto tag on posts and pages to prevent spam. Simply decodes any previously encoded e-mail address by just clicking the mail link.

== Description ==
<p>Automatically encodes every e-mail address in a mailto tag on posts, pages, sidebar widgets and post/page meta values to prevent spam. Simply decodes any previously encoded e-mail address by just clicking the mail link.</p>
<p>This plugin does not have any graphical user interface - just activate it in your Wordpress backend and let it do its job.</p>
<p>The enduser behavior is the same as usual as if the mail address where not encoded. Clicking the mail address link forces a real-time decoding provided by JavaScript.</p>

= Features =
* Works without a graphical user interface
* Encodes e-mail addresses in mailto tags in posts, pages, text widgets, comments, and post/page meta values
* Encodes e-mail addresses even if the link is an image
* Decodes every encoded e-mail address in real-time using javascript
* Works with JavaScript minifying and caching plugins to improve page loading speed

= Requirements =
* WordPress 3.0 or higher
* PHP 5.3 or higher
* A WordPress theme using the wp_footer() template tag


== Frequently Asked Questions ==

Do you have any questions or issues with the &quot;Simple Mail Address Encoder&quot; plugin?

Please follow us on twitter and ask: [@bannerweb](http://twitter.com/bannerweb "@bannerweb")


== Installation ==
1. Unzip and upload the &quot;simple-mail-address-encoder&quot; folder to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Make sure your theme uses the wp_footer() function
4. Enjoy automatically highly secured e-mail addresses in your posts, pages, comments and sidebar widgets without doing anything!


== Changelog ==

= 1.5.4 =
* Compatibility fix with PHP < 5.3: [Unexpected T_FUNCTION](http://wordpress.org/support/topic/error-with-153, "Unexpected T_FUNCTION")
* Minor Bugfix: error message "headers already sent" during activation of the plugin

= 1.5.3 =
* Bugfix: Post-Meta array handling (Patch provided by [Hannes Meitzner](http://meitzner.net/, "Hannes Meitzner"))

= 1.5.2.2 =
* Minor bugfix

= 1.5.2.1 =
* Minor bugfix

= 1.5.2 =
* Added Titel Attribute Encoding
* Added Support for "&" in e-mail adresses (me&you@domain.tld)
* Changed script handling
* Bugfix: [Imagemap broken](http://wordpress.org/support/topic/imagemap-broken, "Imagemap broken")

= 1.5.1 =
* Bugfix: [Memory exhausted](http://wordpress.org/support/topic/plugin-simple-mail-address-encoder-memory-exhausted?replies=4, "Memory exhausted")
* Increased Plugin Speed

= 1.5.0.1 =
* Bugfix: [Simple Email Address Encoder v. 1.5 broken. Earlier version fine on WordPress 3](http://wordpress.org/support/topic/simple-email-address-encoder-v-15-broken-earlier-version-fine-on-wordpress-3?replies=6, "Simple Email Address Encoder v. 1.5 broken. Earlier version fine on WordPress 3")

= 1.5 =
* Sourcecode update: This plugin now follows the new WordPress system requirements
* Added support for js minifying and caching to improve page loading speed
* Deeper WordPress integration

= 1.4.1.2 =
* Minor bugfix

= 1.4.1.1 =
* Bugfix: [line break after link](http://wordpress.org/support/topic/plugin-simple-mail-address-encoder-line-break-after-link-in-v141?replies=2, "line break after link")

= 1.4.1 =
* Bugfix: [Encodes wrong link](http://wordpress.org/support/topic/plugin-simple-mail-address-encoder-plugin-encodes-the-wrong-link?replies=1, "Encodes wrong link")
* Bugfix: problems with special chars

= 1.4.0.1 =
* Minor bugfix

= 1.4 =
* Added e-mail address encoding for post/page meta values

= 1.3 =
* Added e-mail address encoding for comments

= 1.2.1.1 =
* Minor bugfixes

= 1.2.1 =
* Added support for linked pictures (when a picture is the mail address link)

= 1.2 =
* Added e-mail address encoding for sidebar widgets
* Wordpress 3.0 ready!

= 1.1 =
* Attributes of the "a" tag like class="", id="" or style="" won't be ignored any longer

= 1.0 =
* First public release
