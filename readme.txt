=== WBLG series Blogs Details ===
Contributors: Alain Bariel
Donate link: http://www.la-dame-du-lac.com/core/
Tags: weblegend, wblg-series, wpmu, blogs, details, contact
Requires at least: 3.4
Tested up to: 3.5
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Plugin for classic or multisite WordPress system. It allows you to fill in a contact form linked to a WordPress blog or site, the multisite support allow to specify a coordinate tuple for each distinct blogs.
— Integration by widget or shortcode

== Installation ==

[WBLG series Core Admin](http://wordpress.org/extend/plugins/wblg-series-core-admin/ "WBLG series Core Admin plugin") required.

When WBLG series Core Admin is installed.

1. Use the WordPress Add New Plugins menu otherwise...
2. Download the zip file from WordPress.org.
3. Upload the WBLG series Blogs Details folder to /wp-content/plugins/. 1, Extract the zip file.
4. Activate the plugin through the WordPress plugins page.

== Frequently asked questions ==

= Form =

* See in Menu Admin Side Bar: WbLgnd Series  > Blogs Details.
* Fill the form.
* In WPMU context each blog has an independant form, you must switch to a blog to another.

= Widget =

* See in Menu Admin Side Bar: Apparence > Widgets, and place "Contact details [en] / Coordonnées [fr]" in your front-end sidebar.
* Checked items don't be displayed.
* In WPMU context, each blog has an independant widget, you must switch to a blog to another.

= Template =

* No action procedure. Create a post or page and place the shortcode.

= Shortcode =

**Usage in your post/page**

* normal usage:
> [blog_coords]

* defaults attributs:
> display: *block* | hidden: *none*

* with breakline:
> [blog_coords display='block']

* for baseline:
> [blog_coords display='bline']

* values:

> **display value** can be: *block*, *bline*, *name*, *address*, *zipcode*, *city*, *phone*, *fax*, *mobile*, *email*

> **hidden value** can be: *name*, *address*, *zipcode*, *city*, *phone*, *fax*, *mobile*, *email* or combined *zipcode,fax,email*


* full example:
> [blog_coords display='bline' hidden='phone,fax,mobile,email']

* In WPMU context, each blog has an independant setting, you must set for each blog.

== Screenshots ==

1. Form in administration page in Menu Admin Side Bar: WbLgnd Series  > Blogs Details.
2. Widget settings in Menu Admin Side Bar: Apparence > Widgets

== Changelog ==

= 1.0 =
* Improve compatibility between single site and multi-site
* Request for Wordpress approval: 01-21-2013

= 0.2 =
* Release candidate: 01-19-2013

== Upgrade notice ==

* None.