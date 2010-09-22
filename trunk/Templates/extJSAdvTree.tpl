<?php if (!empty($tree)) {?>
	children: [{
<?php $first=true; ?>
<?php 	foreach($tree as $child) { ?>
<?php		if ($first) $first=false; else {?>
,{
<?php		} ?>
		text: "<?php echo $child['name']?>",
<?php		foreach($child as $key=>$data) {?>
<?php			if($key!="tree" && $key!="name") {?>
<?php 		echo $key;?>: "<?php echo $data;?>",
<?php			} ?>
<?php 		} ?>
		expanded: false,
<?php 		if(isset($child['tree']) && is_array($child['tree'])) { ?>
<?php 			echo new TreeTpl($child['tree'], TreeTpl::EXTJSADV);?>
<?php 		}else {?>
		leaf: true
<?php		} ?>
		}
<?php	} ?>
	]
<?php }else { ?>
	leaf: true
<?php } ?>