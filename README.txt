=== WURFL Capabilities ===
Contributors: bmsterling 
Donate link:http://benjaminsterling.com/donations/
Tags: wurfl, mobile, capabilites, theme
Requires at least: 3.5
Tested up to: 3.5.1
Stable tag: 0.3.4
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Using WURFL to expose capabilities of your users browser.  NOTE: please see installation instructions before activating.

== Description ==

Using WURFL to expose capabilities of your users browser. See <http://wurfl.sourceforge.net/help_doc.php> for full list of capability checking. **FTP access is needed to upload the wurfl database**

Some initial methods that are exposed are:

*   `is_wireless_device`
*   `is_tablet`
*   `is_touch`
*   `supports_borderradius`
*   `supports_gradients`
*   `pointing_method`
*   `getCapability` (points to the WURFL API method of the same name, pass in the param of capability you'd like to get. See the help doc link for those param names)


<strong>Example usage:</strong> `$wurflcap->is_touch();`

If something fails, please provide step by step instructions on how to reproduce so that I may address the issue in a timely manner.


Please be aware of the licensing at [http://wurfl.sourceforge.net/license.php](http://wurfl.sourceforge.net/license.php)

*WURFL is the registered trademark of ScientiaMobile, Inc., Reston, VA, USA*

== Installation ==

Installation is pretty straight forward, follow steps in order:

1.  Download and extract the plugin to your plugins directory (wp-content/plugins)
2.  Download the most recent WURFL database from [http://sourceforge.net/projects/wurfl/files/WURFL/](http://sourceforge.net/projects/wurfl/files/WURFL/)
3.  Extract the downloaded zip file and place the XML file into the WURFL Capabilities plugin's folder
4.  Active the plugin. Note: activation may, depending on your server, take about a minute, do not refresh or close your browser. The WURFL API is caching the data based off the XML file.

**Side note:** At this point, do to the point that I can't include the XML file, any future updates to the plugin via the automatic update in wordpress would delete the XML file you installed.  You will need to follow the same flow for updating.

== Frequently Asked Questions ==

= How do I update the database? =

Follow steps 2 and 3 of the installation steps, the WURFL API checkes the xml for last modifed and, if changed, will update the cache. Again, be aware the initial change will probably take about a minute depending on your server.

== Changelog ==

= 0.3.4 =
*   Change from using ZIP to XML do to some servers not being able to extract zips
*   Update install instructions to be clearer
*   Check to see if folders are there in the event of reactivating a plugin
*   Clean up readme