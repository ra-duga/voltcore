<?php if(!$data) return; ?>
<table <?php if ($tabId){echo "id='$tabId'";}?> <?php if ($class){echo "class='$class'"; } ?>>
<?php if ($title){ ?>
	<caption><?php echo $title; ?></caption>
<?php } ?>
	<thead>
		<tr>
<?php foreach(array_keys($data[key($data)]) as $thead) {?>
			<th><?php echo $thead; ?></th>
<?php } ?>
		</tr>
	</thead>
<?php if($footData)	{ ?>
	<tfoot>
		<tr>
<?php foreach($footData as $tfoot) {?>
			<td><?php echo $tfoot; ?></td>
<?php } ?>
		</tr>
	</tfoot>
<?php } ?>
	<tbody>
<?php foreach($data as $row){ ?>
		<tr>
<?php foreach ($row as $cellData){?>
			<td><?php echo $cellData; ?></td>
<?php } ?>
		</tr>
<?php } ?>
	</tbody>
</table>
