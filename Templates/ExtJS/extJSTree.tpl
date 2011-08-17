<?php if (!empty($tree)) {?>
	children: [{
<?php $first=true; ?>
<?php 	foreach($tree as $child) { ?>
<?php		if ($first) $first=false; else { ?>
,{
<?php		} ?>
<?php 		if($gettext && $child['name']!=""){?>
		text: "<?php echo _($child['name']) ?>",
<?php 		}else{ ?>
		text: "<?php echo $child['name']?>",
<?php 		} ?>
		expanded: false,
<?php 		if($child['tree'] && is_array($child['tree'])) { ?>
<?php 			echo new TreeTpl($child['tree'], TreeTpl::EXTJS, $gettext);?>
<?php 		}else{ ?>
		leaf: true
<?php		} ?>
		}
<?php	} ?>
	]
<?php }else{ ?>
		leaf: true
<?php } ?>