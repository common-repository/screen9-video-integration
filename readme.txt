=== Screen9 Video Integration ===

Contributors: @fantomen
Tags: CMS, video
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 0.1
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin allows uploading and displaying Screen9 video content

== Description ==

*This plugin was created by Adeprimo to give customers an easy to use tool
for uploading and displaying Screen9 video content on worpress sites.

Screen9 is an Online Video Platform, or in other words, a platform for publishing video online.

To be able to use the plugin you need to have an account over at Screen9. To try it out for free, go to: [Link](http://www.screen9.com/packages-and-pricing/free-trial/ "screen 9 trial signup")

= Requirements =

- Wordpress version >= 3.0
- PHP version >= 5.2
- Modern web browser

== Installation ==

1. Install the plugin via the WordPress.org plugin directory.
2. Go to settings -> Settings Screen9 in wp-admin and fill in your credentials
3. To display video your single-article template/content.php must implement the function screen9_display_video(get_post_meta( get_the_ID(), 'screen9_videoid', true ));

== Changelog ==

= 0.1 =

- First version