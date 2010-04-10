<?php if(!$data) return; ?>
<table <?php if ($tabId){ ?> id=<?php echo "'$tabId'"; ?> <?php if ($class){ ?> class=<?php echo "'$class'"; ?>  <?php } ?>>
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
	<tfoot>
		<tr>
<?php foreach($footData as $tfoot) {?>
			<td><?php echo $tfoot; ?></td>
<?php } ?>
		</tr>
	</tfoot>
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
