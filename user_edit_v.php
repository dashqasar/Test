
<script language="javascript">
	function change_user_type(){
		var role_type = document.getElementById('user_type_drop_down').value;
		var id_array = [	"full_name", 
							"awards",
							"band_name", 
							"manager_drop_down", 
							"pay_type_drop_down", 
							"pay_amount",
							"website",
							"pay_date_year",
							"pay_date_month",
							"pay_date_day",
						];
		var start1 = 2;
		
		for(i = 0; i < id_array.length;i++){
			document.getElementById(id_array[i]).disabled = false;
		}
		
		if (role_type == 0) {		// for band
			for(i = 0; i < start1; i++){
				document.getElementById(id_array[i]).disabled = true;
			}
		} else if (role_type == 2) { // for manager
			for(i = start1; i < id_array.length;i++){
				document.getElementById(id_array[i]).disabled = true;
			}
		} else {
			for(i = 0; i < id_array.length;i++){
				document.getElementById(id_array[i]).disabled = true;
			}
		}
	}
</script>
<?php
	$user_name = array(
		'name'	=> 'user_name',
		'id'	=> 'user_name',
		'value' => $user->username,
		'size' 	=> 30,
		'maxlength'	=> 50,
	); 
	$user_email = array(
		'name'	=> 'user_email',
		'id'	=> 'user_email',
		'value' => $user->email,
		'size' 	=> 30,
		'maxlength'	=> 100,
	);
	$user_phone = array(
		'name'	=> 'user_phone',
		'id'	=> 'user_phone',
		'value' => $profile['phone'],
		'size' 	=> 30,
		'maxlength'	=> 64,
	);
	$new_password = array(
		'name'	=> 'new_password',
		'id'	=> 'new_password',
		'maxlength'	=> $this->config->item('password_max_length', 'tank_auth'),
		'size'	=> 30,
	);
	$full_name = array(
		'name'	=> 'full_name',
		'id'	=> 'full_name',
		'value' => $profile['full_name'],
		'size' 	=> 30,
		'maxlength'	=> 64,
	);
	$awards = array(
		'name'	=> 'awards',
		'id'	=> 'awards',
		'value' => $profile['awards'],
		'size' 	=> 30,
		'maxlength'	=> 64,
	);
	$band_name = array(
		'name'	=> 'band_name',
		'id'	=> 'band_name',
		'value' => $profile['band_name'],
		'size' 	=> 70,
		'maxlength'	=> 128,
	);
	$website = array(
		'name'	=> 'website',
		'id'	=> 'website',
		'value' => $profile['website'],
		'size' 	=> 70,
		'maxlength'	=> 255,
	);
	$user_type = array(
		'name'	=> 'user_type',
		'id'	=> 'user_type',
		'value' => $user->role_id,
	);
	
	$manager = array(
		'name'	=> 'manager',
		'id'	=> 'manager',
		'value' => $profile['manager_idx'],
	);
	$pay_type = array(
		'name'	=> 'pay_type',
		'id'	=> 'pay_type',
		'value' => $profile['pay_type'],
	);
	$pay_amount = array(
		'name'	=> 'pay_amount',
		'id'	=> 'pay_amount',
		'value' => $profile['pay_amount'],
		'size' 	=> 20,
		'maxlength'	=> 20,
	);
	$pay_date = array(
		'name'	=> 'pay_date',
		'id'	=> 'pay_date',
		'value' => $profile['pay_date'],
		'size' 	=> 20,
		'maxlength'	=> 10,
	);
	
	if (!empty( $profile['pay_date']))
		list($pay_date_year_value, $pay_date_month_value, $pay_date_day_value) = split('[/.-]', $profile['pay_date']);
	else{
		$pay_date_year_value = 2000;
		$pay_date_month_value = 1;
		$pay_date_day_value = 1;
	}
		
	$pay_date_year = array(
		'name' 	=> 'pay_date_year',
		'id' 	=> 'pay_date_year',
		'value' => $pay_date_year_value,
	);
	$pay_date_month = array(
		'name' 	=> 'pay_date_month',
		'id' 	=> 'pay_date_month',
		'value' => $pay_date_month_value,
	);
	$pay_date_day = array(
		'name' 	=> 'pay_date_day',
		'id' 	=> 'pay_date_day',
		'value' => $pay_date_day_value,
	);
	$google_uname = array(
		'name'	=> 'google_uname',
		'id'	=> 'google_uname',
		'value' => $profile['google_uname'],
		'size' 	=> 30,
		'maxlength'	=> 64,
	);
	$google_upass = array(
		'name'	=> 'google_upass',
		'id'	=> 'google_upass',
		'value' => $profile['google_upass'],
		'size' 	=> 30,
		'maxlength'	=> 255,
	);
	
	include_once APPPATH.'views/show_message.php';
	
	$days_of_Months = array(
		1 => 31,
		2 => 28,
		3 => 31,
		4 => 30,
		5 => 31,
		6 => 30,
		7 => 31,
		8 => 31,
		9 => 30,
		10 => 31,
		11 => 30,
		12 => 31,
	);
	
	$days_array = array();
	
	if (intval($pay_date_month['value'])==0)
		$monthVal = 1;
	else
		$monthVal = intval($pay_date_month['value']);
		for ($i = 1; $i <= $days_of_Months[$monthVal];$i++){
			$days_array[$i] = $i;
	}
?>

<script language="javascript">
	function onChangeMonth(){
		var monthVal = document.getElementById('pay_date_month').value;
		var yearVal = document.getElementById('pay_date_year').value;
		
		var daysOfMonth = [	
<?php
			for ($i=1;$i<=12;$i++) {
				echo $days_of_Months[$i].",";
			}
?>
					];
		if ((yearVal % 4 == 0 && yearVal % 100 != 0) || (yearVal % 400 == 0)) {
			daysOfMonth[1] = 29;
		}
		
		var totalDays = daysOfMonth[monthVal - 1];
		
		var dayElement = document.getElementById('pay_date_day');
		var len = dayElement.options.length;

		for (i=0; i<len;i++) {
       		dayElement.remove(0);
    	}
    	
    	for (i=0;i<totalDays;i++){
    		var opt = document.createElement("option");
    		opt.text=(i+1);
    		opt.value=(i+1);
    		dayElement.options.add(opt);
    	}
	}
</script>

<?php echo form_open($this->uri->uri_string()); ?>
<table class="tbl" cellpadding="2" cellspacing="0" width="560"  >
	<tr>
		<th width="160">Index.</th>
		<td><?php echo $user->id; ?></td>
	</tr>
	<tr>
		<th><?php echo form_label('Login Name', $user_name['id']); ?></th>
		<td>
			<?php echo form_input($user_name); ?>
			<br />
			<font color="red">
			<?php echo form_error($user_name['name']); ?><?php echo isset($errors[$user_name['name']])?$errors[$user_name['name']]:''; ?>
			</font>
		</td>
	</tr>
	<tr>
		<th><?php echo form_label('Email Address', $user_email['id']); ?></th>
		<td>
			<?php echo form_input($user_email); ?>
			<br />
			<font color="red">
			<?php echo form_error($user_email['name']); ?><?php echo isset($errors[$user_email['name']])?$errors[$user_email['name']]:''; ?>
			</font>
		</td>
	</tr>
	<tr>
		<th><?php echo form_label('New Password', $new_password['id']); ?></th>
		<td>
			<?php echo form_password($new_password); ?> 
			<br />
			(Leave blank if you don't want to change password. )
			<br />
			<font color="red">
			<?php echo form_error($new_password['name']); ?><?php echo isset($errors[$new_password['name']])?$errors[$new_password['name']]:''; ?>
			</font>
		</td>
	</tr>
	<tr>
		<th><?php echo form_label('Phone Number', $user_phone['id']); ?></th>
		<td>
			<?php echo form_input($user_phone); ?>
			<br />
			<font color="red">
			<?php echo form_error($user_phone['name']); ?><?php echo isset($errors[$user_phone['name']])?$errors[$user_phone['name']]:''; ?>
			</font>
		</td>
	</tr>
	<tr>
		<th><?php echo form_label('Full Name', $full_name['id']); ?></th>
		<td>
			<?php echo form_input($full_name); ?> 
			<br />
			<font color="red">
			<?php echo form_error($full_name['name']); ?><?php echo isset($errors[$full_name['name']])?$errors[$full_name['name']]:''; ?>
			</font>
		</td>
	</tr>
	<tr>
		<th><?php echo form_label('Awards', $awards['id']); ?></th>
		<td>
			<?php echo form_input($awards); ?> 
			<br />
			<font color="red">
			<?php echo form_error($awards['name']); ?><?php echo isset($errors[$awards['name']])?$errors[$awards['name']]:''; ?>
			</font>
		</td>
	</tr>	
	<tr>
		<th><?php echo form_label('Band Name', $band_name['id']); ?></th>
		<td>
			<?php
				$type_attr = ($user->role_id==1)? "disabled" : ""; 
				echo form_input($band_name,$band_name['value'],$type_attr); 
			?> 
			<br />
			<font color="red">
			<?php echo form_error($band_name['name']); ?><?php echo isset($errors[$band_name['name']])?$errors[$band_name['name']]:''; ?>
			</font>
		</td>
	</tr>	
	<tr>
		<th><?php echo form_label('User Type', $user_type['id']); ?></th>
		<td>
			<?php
				$user_type_attr = ($user->role_id==1)? "disabled" : "" . " onchange='change_user_type()' id='user_type_drop_down'";
				echo form_dropdown($user_type['name'],$this->config->item('user_type_to_edit'),$user_type['value'], $user_type_attr); 
			?> 
			<br />
			<font color="red">
			<?php echo form_error($user_type['name']); ?><?php echo isset($errors[$user_type['name']])?$errors[$user_type['name']]:''; ?>
			</font>
		</td>
	</tr>
	<tr>
		<th><?php echo form_label('Manager', $manager['id']); ?></th>
		<td>
			<?php
				$type_attr = ($user->role_id==1)? "disabled" : ""." id='manager_drop_down'"; 
				echo form_dropdown($manager['name'],$managers,$manager['value'],$type_attr); 
			?> 
			<br />
			<font color="red">
			<?php echo form_error($manager['name']); ?><?php echo isset($errors[$manager['name']])?$errors[$manager['name']]:''; ?>
			</font>
		</td>
	</tr>
	<tr>
		<th><?php echo form_label('Pay Type', $pay_type['id']); ?></th>
		<td>
			<?php
				$type_attr = ($user->role_id==1)? "disabled" : ""." id='pay_type_drop_down'";  
				echo form_dropdown($pay_type['name'],$this->config->item('pay_type'),$pay_type['value'],$type_attr); 
			?> 
			<br />
			<font color="red">
			<?php echo form_error($pay_type['name']); ?><?php echo isset($errors[$pay_type['name']])?$errors[$pay_type['name']]:''; ?>
			</font>
		</td>
	</tr>
	<tr>
		<th><?php echo form_label('Pay Amount', $pay_amount['id']); ?></th>
		<td>
			$ <?php
				$type_attr = ($user->role_id==1)? "disabled" : ""; 
				echo form_input($pay_amount,$pay_amount['value'],$type_attr); 
			?>
			<br />
			<font color="red">
			<?php
				echo form_error($pay_amount['name']); ?><?php echo isset($errors[$pay_amount['name']])?$errors[$pay_amount['name']]:''; 
			?>
			</font>
		</td>
	</tr>
	<tr>
		<th><?php echo form_label('Pay Date', 'Pay Date Month'); ?></th>
		<td>
				Mon. 
				<?php 
				$type_attr = ($user->role_id==1)? "disabled" : ""." id='pay_date_month' onchange='onChangeMonth()'";
				echo form_dropdown($pay_date_month['name'], $this->config->item('month'),$pay_date_month['value'],$type_attr);?>
				
				Day. 
				<?php 
				$type_attr = ($user->role_id==1)? "disabled" : ""." id='pay_date_day'";
				echo form_dropdown($pay_date_day['name'], $days_array,$pay_date_day['value'],$type_attr);?>
				
				Year. 
				<?php 
				$type_attr = ($user->role_id==1)? "disabled" : ""." id='pay_date_year' onchange='onChangeMonth()'";
				echo form_dropdown($pay_date_year['name'], $this->config->item('year'),$pay_date_year['value'],$type_attr);?>
		</td>
	</tr>
	<tr>
		<th><?php echo form_label('Facebook URL', $website['id']); ?></th>
		<td>
			<?php echo form_input($website); ?> 
			<br />
			<font color="red">
			<?php echo form_error($website['name']); ?><?php echo isset($errors[$website['name']])?$errors[$website['name']]:''; ?>
			</font>
		</td>
	</tr>
<!--
	<tr>
		<th><?php echo form_label('Google Login', $google_uname['id']); ?></th>
		<td>
			<?php echo form_input($google_uname); ?>
			<br />
			<font color="red">
			<?php echo form_error($google_uname['name']); ?><?php echo isset($errors[$google_uname['name']])?$errors[$google_uname['name']]:''; ?>
			</font>
		</td>
	</tr>
	<tr>
		<th><?php echo form_label('Google Password', $google_upass['id']); ?></th>
		<td>
			<?php echo form_input($google_upass); ?>
			<br />
			<font color="red">
			<?php echo form_error($google_upass['name']); ?><?php echo isset($errors[$google_upass['name']])?$errors[$google_upass['name']]:''; ?>
			</font>
		</td>
	</tr> -->
	<tr>
		<th></th>
		<td>
			<br />
			<?php echo form_submit('save', 'Save User'); ?>
		</td>
	</tr>
</table>

<?php echo form_close(); ?>
<script language="javascript">
	change_user_type();
</script>