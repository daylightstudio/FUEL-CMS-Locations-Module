<?php if (!empty($locations)) : ?>
<?php foreach($locations as $location) : ?>

<p class="location">
	<a href="<?=$location->map_url?>" target="map"><?=$location->name?></a><br>
	<?=$location->get_full_address('<br>')?><br>
	<?php if ($location->has_website()) : ?>
	<a href="<?=$location->website_url?>"><?=$location->website?></a>
	<?php endif; ?>
</p>

<?php endforeach; ?>
<?php else: ?>
	<p>No results have been located.</p>
<?php endif; ?>