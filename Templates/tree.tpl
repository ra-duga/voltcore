<ul>
<?php	foreach($tree as $child) { ?>
    <li>
<?php 		echo $child['name'] ?>
<?php 		if($child['tree'] && is_array($child['tree'])) { ?>
<?php 			echo new TreeTpl($child['tree']);?>
<?php 		} ?>
    </li>
<?php 	} ?>
</ul>