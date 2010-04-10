<?php if (!empty($tree)) {?>
	children: [{
<?php $first=true; ?>
<?php 	foreach($tree as $child) { ?>
<?php		if ($first) $first=false; else { ?>
,{
<?php		} ?>
		text: "<?php echo $child['name']?>",
		expanded: false,
<?php 		if($child['tree'] && is_array($child['tree'])) { ?>
<?php 			echo new TreeTpl($child['tree'], TreeTpl::EXTJS);?>
<?php 		}else {?>
		leaf: true
<?php		} ?>
		}
<?php	} ?>
	]
<?php }else { ?>
	leaf: true
<?php } ?>