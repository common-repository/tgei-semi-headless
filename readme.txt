=== TGEI Semi Headless  ===
Contributors: toogoodenterprises 
Tags: headless, semi headless, block, allow
Requires at least: 6.0
Tested up to: 6.6
Stable tag: 1.0.0
Requires PHP: 7.4
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

TGEI Semi Headless can fully or partially disable the frontend of Wordpress for headless Wordpress sites.

== Description ==
TGEI Semi Headless can fully or partially disable the frontend of Wordpress for headless Wordpress sites.

= What a Semi Headless Wordpress Site? =
In a typical headless Wordpress setup, the entire frontend is disabled. All requests to the frontend are automatically redirected to another page. All features of the website are re-implemented in the framework of the headless website.

Instead of re-implementing all features of a headless website, a semi headless Wordpress setup disables most of the frontend leaving only designated posts and/or pages accessible. These posts/pages enable the reuse of built-in Wordpress features or features of installed plugins.

= Semi Headless Examples =
### Online Store
You install an e-commerce plugin to turn your wordpress into an online store. However, you may want to display your products in such a way that it does not fit well with the Wordpress templating engine, so you decide to go headless with your own templating engine. But you don't want to reimplement every feature of the store such as cart management and checkout, so you use TGEI Semi Headless to disable all frontend pages except the cart and checkout pages.


### Blog
You have a travel blog where you make frequent posts about your trips. You use an image gallery plugin to manage the various image galleries of your trips. You decide to go headless to speed up your website. However, you don't want to use another image gallery plugin, so you use TGEI Semi Headless to disable all the frontend pages except the image gallery pages.

= Control What Is Allowed or Blocked =
The following is can be toggled to be allowed or blocked:

* individual posts and pages
* works with custom post types
* archive pages of taxonomies such as categories and tags
* search result pages
* home page

= Multiple Ways to Set Block Status =
Toggle the block status via:

* the gutenberg post/page editor
* the quick editor
* the bulk action menu

= 404 Redirect =
Redirect Wordpress 404 errors to your headless site's 404 error page to maintain consistency.

== Frequently Asked Questions ==

= Does TGEI Semi Headless create a headless version of my site? =

No, TGEI Semi Headless does not convert your site to a headless version. You will need to create the headless version.

TGEI Semi Headless only disables the frontend of Wordpress and redirects all front end requests to the headless version unless that particular post/page is allowed.

= What is the default block status for post and pages? =

By default, all post and pages are blocked. To unblock, each desired page/post must be set to allow.

= I installed TGEI Semi Headless but I can not find the settings page. =

You can get to the settings page in two ways:

*Method 1*
From the Wordpress Plugin page where all your installed plugins are listed. There is a link to the settings page from there.

*Method 2*
From the Wordpress Menu: Tools -> TGEI Semi Headless

== Installation ==

= Automatic Installation =
In the Wordpress plugin directory, find "TGEI Semi Headless" and click install. After install is complete, click activate.

= Manual Installation =
1. In the Wordpress plugin directory, find "TGEI Semi Headless" and click download.
2. Unzip the downloaded file. It should have a folder named "TGEI-SemiHeadless"
3. Upload all contents of the TGEI-SemiHeadless folder to your wordpress site into the plugins folder in wp-content
4. Log into the admin area of your wordpress site
5. Go to the Installed Plugins page
6. Find TGEI Semi Headless and click activate.


== Screenshots ==

1. Set the allow status in the Gutenberg editor for a post/page
2. Use quick edit to set the allow status for a post/page
3. You can also use Bulk Actions to set the status of multiple items
4. Set the alow status of categories/tags in it's edit page
5. Use quick edit to set the allow status for categories/tags
6. TGEI Semi Headless allow and block in action

== Changelog ==

= 1.0.0 =
* First release version.
