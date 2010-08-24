<?php if (!empty($tree)) {?>
	children: [{
<?php $first=true; ?>
<?php 	foreach($tree as $child) { ?>
<?php		if ($first) $first=false; else {?>
,{
<?php		} ?>
<?php		foreach($child as $key=>$data) {?>
<?php			if($key!="tree") {?>
		<?php 		echo $key;?>: "<?php echo $data;?>",
<?php			} ?>
<?php 		} ?>
		uiProvider:'<?php echo $provider ?>',
		expanded: false,
<?php 		if(isset($child['tree']) && is_array($child['tree'])) { ?>
<?php 			echo new TreeTpl($child['tree'], TreeTpl::EXTJSTABLE);?>
<?php 		}else {?>
		leaf: true
<?php		} ?>
		}
<?php	} ?>
	]
<?php }else { ?>
	leaf: true
<?php } ?>