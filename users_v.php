<script type="text/javascript">
<!--
	function confirm_del(no) {
		if(confirm("Do you want to delete?")) {
			window.location.href = "<?php echo site_url("admin/user/del");?>/" + no;
		}
	}
//-->
</script>
<table class="tbl" cellpadding="2" cellspacing="1" width="660" style="text-align:center">
	<tr>
		<th width="30">No.</th>
<!--	<th>User Index</th> -->
		<th width="300">User Name</th>
		<th width="100">Login Name</th>
<!--	<th>Email</th> -->
		<th>User Type</th>				<th>Manager</th>
		<th>Del</th>
	<tr>
<?php
	$number = 0;
	foreach($user_list as $user) {
		 $number++;
		 $link = site_url("admin/user/edit/".$user['id']);
		 $roles = $this->config->item('user_type');
		 $user_type = $roles[$user['role_id']];		 		 		 if ($user_type == "Band") {		 	$manager_name = $this->admin_m->get_manager_of_band($user['id']);		 } else {		 	$manager_name = "-";		 }		 		 
?>
	<tr>
		<td><?php echo $number;?></td>
		<!--<td><?php echo $user['id'];?></td> -->
		<td><a href="<?php echo $link;?>"><?php 
				if ($user['role_id'] == 0)
					echo $user['band_name'];
				else 
					echo $user['full_name'];
		?></a></td>
		<td><a href="<?php echo $link;?>"><?php echo $user['username'];?></a></td>
<!--	<td><a href="<?php echo $link;?>"><?php echo $user['email'];?></a></td> -->
		<td><?php echo $user_type;?></td>		<td><?php echo $manager_name;?></td>
		<td><a href="javascript:confirm_del(<?php echo $user['id'];?>)">X</a></td>
	</tr >
<?php
	}
?>
</table>