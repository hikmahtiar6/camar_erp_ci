<!DOCTYPE html>
<html>
<head>
	<title>List barang</title>
</head>
<body>
	<table border="1">
		<tr>
			<td>No.</td>
			<td>Section ID.</td>
			<td>Section Name.</td>
			<td>Harga</td>
		</tr>
		<?php $no = 1; ?>
		<?php if($brg): ?>
			<?php foreach($brg as $row): ?>
			<tr>
				<td><?php echo $no; ?></td>
				<td><?php echo $row->section_id; ?></td>
				<td><?php echo $row->section_name; ?></td>
				<td><?php echo $row->harga; ?></td>
			</tr>
			<?php $no++; ?>
			<?php endforeach; ?>
		<?php endif; ?>
	</table>
</body>
</html>

<?php

//var_dump($brg);
?>