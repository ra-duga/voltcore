<?php if (isset($recordName)){ ?>
    var <?php echo $recordName ?> = Ext.data.Record.create([{
<?php $first=true; ?>
<?php foreach($fields as $fname=>$f){ ?>
<?php	if ($first) $first=false; else { ?>
		,{
<?php		} ?>
<?php	foreach($f['record'] as $k=>$v){	?>
			<?php echo $k ?>: '<?php echo $v ?>',
<?php 	} ?>
			name: '<?php echo $fname ?>'
		}		
<?php } ?>
	]);

<?php if (isset($storeName) ){ ?>
    var <?php echo $storeName ?> = new Ext.data.Store({
        reader: new Ext.data.JsonReader({
			fields: <?php echo $recordName ?>,
			root: '<?php echo $rootField ?>',
			totalProperty: '<?php echo $totalField ?>'
		}),
		proxy: new Ext.data.HttpProxy({
			url: '<?php echo $dataUrl ?>',
			method:'<?php echo $method ?>'	
		}),
		baseParams:<?php echo $baseParams ?>,
		remoteSort: true,
		storeId:'<?php echo $storeId ?>'
    });
<?php } ?>
<?php } ?>
	
	var <?php echo $columnsName ?> = [{
<?php $first=true; ?>
<?php foreach($fields as $fname=>$f){ ?>	
<?php	if ($first) $first=false; else { ?>
		,{
<?php		} ?>

            header: '<?php echo $fname ?>',
            dataIndex: '<?php echo $fname ?>',
<?php			foreach($f as $k=>$v){	?>
<?php				if ($k!='editor' && $k!='record' && $k!='width') { ?>
			<?php echo $k ?>: <?php echo $v ?>,
<?php	 			} ?>
<?php 			} ?>
			width:<?php echo $f["width"] ?>
<?php            if (isset($f['editor'])){ ?>
,
			editor: {
<?php $edfirst=true; ?>
<?php			foreach($f['editor'] as $k=>$v){	?>
<?php	if ($edfirst) $edfirst=false; else { ?>
,
<?php		} ?>
					<?php echo $k ?>: <?php echo $v ?>
<?php 			} ?>
            }
<?php 			} ?>
        }
<?php } ?>
		];