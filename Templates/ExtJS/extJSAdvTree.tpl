<?php if (!empty($tree)) {?>
	children: [{
<?php $first=true; ?>
<?php 	foreach($tree as $child) { ?>
<?php		if ($first) $first=false; else {?>
,{
<?php		} ?>
		expanded: false,
<?php		foreach($child as $key=>$data) {?>
<?php			if($key!="tree" && $key!="name") {?>
<?php 		echo $key ?>: <?php echo toJS($data) ?>,
<?php			} ?>
<?php 		} ?>
		text: "<?php echo $child['name']?>",
<?php 		if(isset($child['tree']) && is_array($child['tree'])) { ?>
<?php 			echo new TreeTpl($child['tree'], TreeTpl::EXTJSADV);?>
<?php 		}else{ ?>
		leaf: true
<?php		} ?>
		}
<?php	} ?>
	]
<?php 		}else{ ?>
		leaf: true
<?php		} ?>