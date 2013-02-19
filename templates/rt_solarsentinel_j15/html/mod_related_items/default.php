<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<ul class="menu<?php echo $params->get('moduleclass_sfx'); ?>">
<?php foreach ($list as $item) :	?>
<li>
	<a href="<?php echo $item->route; ?>">
<?php 
echo $item->title . " - "; 
$dateDisplay = $item->created;
$dateDisplay = date("n-j-Y", strtotime($dateDisplay));
if ($showDate) 
	echo $dateDisplay;
?>
</a>
</li>
<?php endforeach; ?>
</ul>
