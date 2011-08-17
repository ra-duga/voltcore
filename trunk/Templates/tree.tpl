<ul>
<?php	foreach($tree as $child) { ?>
    <li>
<?php 		if($gettext && $child['name']!=""){ ?>
<?php 		echo _($child['name']) ?>
<?php 		}else{ ?>
<?php 		echo $child['name'] ?>
<?php 		} ?>
<?php 		if($child['tree'] && is_array($child['tree'])) { ?>
<?php 			echo new TreeTpl($child['tree'], null, $gettext);?>
<?php 		} ?>
    </li>
<?php 	} ?>
</ul>