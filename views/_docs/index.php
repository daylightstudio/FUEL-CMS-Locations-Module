<h1>Locations Module Documentation</h1>
<p>This Locations module documentation is for version <?=LOCATIONS_VERSION?>.</p>

<p>The Locations allows you to create location records that can be plotted on a <a href="https://developers.google.com/maps/documentation/javascript/tutorial" target="_blank">Google map</a> found out <dfn>locations/map</dfn>.
The configuration allows you a lot of flexibility in controlling the map and has all the options commented out in the config. It's recommended that you copy the 
<span class="file">fuel/modules/locations/config/locations.php</span> to <span class="file">fuel/application/config/locations.php</span> which will allow
for painless updating going forward. Many of the module configuration parameters map directly to the <a href="https://developers.google.com/maps/documentation/javascript/reference#MapOptions" target="_blank">Google map options</a>.</p>

<h2>The Map</h2>
<p>The map can be found at the URI <dfn>locations/map</dfn>. You can pass the following query string parameters that will overwrite any default configuration settings found at <span class="file">fuel/application/config/locations.php</span>:</p>
<ul>
	<li><strong>zoom</strong>: an integer value of how far in to zoom the map. The default is 16.</li>
	<li><strong>type</strong>: the map type. The default is roadmap.</li>
</ul>
<p>It is recommended that you use an iframe in your pages to embed the map with the src value of <dfn>&lt;?=site_url('locations/map')?&gt;</dfn>.</p>

<h2>Categories</h2>
<p>You can associate categories to your locations. If this is done and you have a custom image marker, you will need to create a new marker for each category using the "marker.img" value as
the base and the category slug value as the suffix (all markers should be .png file types). So for example, if you have a "marker.img" value of "mappin" and a category with a slug value of "mycategory" the image name would be
"mappin_mycategory.png".</p>

<h3>Custom Map CSS and Javascript</h3>
<p>The <dfn>locations/map</dfn> page is setup to look for a <dfn>$css</dfn> and <dfn>$js</dfn> variable. You can set them in a <a href="http://docs.getfuelcms.com/general/pages-variables">variables</a> file. 
	For example, if you add the following to your <span class="file">_variables/global.php</span>, it will load in a css file that you can use to override the default values.
	In addition, the following will load in an additional javascript file, which you can use to override things like the info window.</p>

<pre class="brush:php">
$pages['locations/map'] = array('css' =&gt; 'google_map', 'js' =&gt; 'google_overlay');
</pre>

<p><span class="file">assets/css/google_map.css</span></p>
<pre class="brush:php">
html, body { height: 100% !important; margin: 0; }
.gm-style div.info_win { display: none; width: 200px; height: 110px; background: #fff; 
	-moz-border-radius: 3px; -webkit-border-radius: 3px; border-radius: 3px; -khtml-border-radius: 3px; border: 3px solid #ccc; 
	padding: 10px;  position: absolute; z-index: 99999; overflow: hidden; 
	box-shadow: 0 0 15px rgba(0, 0, 0, 1);
	color: #333; font-size: 1em;
	}

.info_win h3 { color: #de1a22; font-size: 1.5em; }
.info_win address { color: #333; font-size: 1em; }
.info_win .btn { text-align: center; display: block; width: 100px; margin: 10px auto;   }
.marker_tooltip { color: #333; border: 1px #999 solid; background-color: #fff; padding:3px 8px; font-size: 1em; white-space: nowrap; box-shadow: 0 0 10px rgba(0, 0, 0, .5);
}
</pre>

<p><span class="file">assets/js/google_overlay.js</span></p>
<pre class="brush:php">
CustomOverlay.prototype.draw = function(){
	// your code for the info window goes here
}
</pre>

<?=generate_config_info()?>

<?=generate_toc()?>


