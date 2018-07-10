=== DietMaster Integration ===
Contributors: szstar
Tags: DietMaster, DietMaster Pro, Nutrition Software, Online Nutrition, fitness, nutrition
Requires at least: 3.8
Tested up to: 4.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Authenticated users can seamlessly access DietMaster Pro Nutrition Software.


== Description ==

This plugin provides the ability to seamlessly add a nutrition tool to any web site built in WordPress. Lifestyles Technologies, publishers of DietMaster Pro Nutrition Software, has been publishing nutrition software tools for 20 years. DietMaster Pro software products are being used in health, fitness, medical and wellness settings. Our collection of software technology includes access to hundreds of meal plan templates designed by contributing doctors and registered dietitians addressing disease prevention, fitness and performance, weight loss, special needs, medical and childhood obesity.

DietMaster Web and the DietMaster Go mobile application can be implemented as a stand-alone platform on your web site, or integrated with an eCommerce shopping cart. For more information regarding the requirements, access the readme file within the provided files.

*NOTE:* You will need a DietMaster Pro account to use this plugin. Visit [our site](http://www.dietmastersoftware.com/) to sign up!

For more information on DietMaster Pro, Web and Mobile products, visit our web site at [www.dietmastersoftware.com](http://www.dietmastersoftware.com/)

If you are not a developer and need any help with this plugin, you may contact our technology partner at [Fitness Website Formula](http://www.fitnesswebsiteformula.com/).

= Plugin Features =

* Two types of shortcodes that lets you control how your clients access their account
* Option to let your clients create a profile right on your site
* Ability to add custom meal plans
* Option to prompt a user to download a mobile App if they are on mobile

= Recommended Plugins =

The following are other recommended plugins for creating membership sites.

* [Wishlist Member](http://member.wishlistproducts.com/)
* [Paid Member Pro](http://www.paidmembershipspro.com/)

== Installation ==

1. Search for "dietmaster" in WordPress or upload the entire `dietmaster-integration` plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. You will find 'DietMaster' menu in your WordPress admin panel under 'Settings'.

Use this shortcode [dietmaster-integration-profile-form] within any page to generate a profile update form. 

== Changelog ==

For more information, see (http://dietmastersoftware.com/).

= 1.3.0 =
* New: two shortcodes [dmi_if_profile] and [dmi_no_profile]  were added to help display conditional content based on an existence of profile.
* Changed: Name, DOB, and gender becomes uneditable once a member saves a profile

= 1.2.1 =
* Fixed: Fixed error of the plugin page not loading when Wishlist was not installed.

= 1.2.0 =
* New: Ability to add custom meal plans
* New: Mobile detection - Option to prompt a user to download a mobile App if they are on mobile
* New: Wishlist integration - membership cancellation triggers ExpirationPassThru

= 1.1.2 =
* Improved: Clearly separated 3 ways to use the plugin with Dietmaster. 
* Chagned: Removed the "persistent" option. Now administrator-level WordPress users can access Dietmaster as well.

= 1.1 =

* Fixed: Fixed bug of users not able to login when they do profilepassthru for the first time
* Added: Message to login as non-admin to use profile form added
* Improved: Light tweaks on CSS

= 1.0 =

* Initial release
