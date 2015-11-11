<?php
class Bands extends Controller {
	
	function Bands()
	{
		parent::Controller();
	
		$this->load->database();
		
		$this->load->library('session');
		
		$this->load->library('tank_auth');
		
		$this->load->model('rate_m');

	}
	
	function remove_alert(){
		$user_idx = $_POST['band_idx'];
		$alert_type = $_POST['alert_type'];
		
		if ($alert_type==0){
			$data = array("alert_call_number" => 0);
		} else {
			$data = array("alert_email_number" => 0);
		}
		
		$this->db->where('user_id', $user_idx);
		$query = $this->db->update('user_profiles', $data);
		
		if ($query==true){
			echo "success";
		} else {
			echo "failed";
		}
	}
	
	function bands_alert(){
		$user_idx = $_POST['band_idx'];
		$alert_type = $_POST['alert_type'];
		
		$strSql = "select alert_call_number, alert_email_number from user_profiles where user_id='".$user_idx."'";
		$query = $this->db->query($strSql);

		$row = $query->row_array();
		$data = array();
		if ($alert_type=="call"){
			$current_value = $row['alert_call_number'];
			if (empty($current_value)) {
				$current_value = 0;
			}
			$data = array('alert_call_number' => ($current_value + 1));
		} else {
			$current_value = $row['alert_email_number'];
			if (empty($current_value))
				$current_value = 0;
			$data = array('alert_email_number' => ($current_value + 1));
		}
		
		$this->db->where('user_id', $user_idx);
		$query = $this->db->update('user_profiles', $data);
		
		if ($query == true) {
			echo "SUCCESS";
		} else {
			echo "FAILED";
		}
		
		return;
	}
	
	
	function bands_rate(){
		$user_idx = $_POST['band_idx'];
		$rate_type = $_POST['rate_type'];
		
		if ($rate_type == "up") {
			$rate_type = 1;
		} else {
			$rate_type = 0;
		}
		$result = $this->rate_m->add_band_rate($user_idx, $rate_type);
		
		if ($result == true) {
			echo "SUCCESS";
		} else {
			echo "FAILED";
		}
		
		return;
	}
	
	function bands_login(){
		$user_name = $_POST['user_name'];
		$user_password = $_POST['user_password'];
		if (empty($user_name) || empty($user_password)) {
			echo "invalid";
			return;
		}
		// Hash password using phpass
		$hasher = new PasswordHash(
			$this->config->item('phpass_hash_strength', 'tank_auth'),
			$this->config->item('phpass_hash_portable', 'tank_auth')
			);
		$strSql = "select * from users where upper(username)=upper('".$user_name."') and role_id=0";
		$query = $this->db->query($strSql);
		$row = $query->row_array();

		if (empty($row) || empty($row['id'])) {
			echo "fail";
			return;
		}
		
		if ($hasher->CheckPassword($user_password, $row['password'])){
			$session_id = $this->session->userdata('session_id');
			$strSql = "select * from user_profiles where user_id='".$row['id']."'";
			$query = $this->db->query($strSql);
			$row_profile = $query->row_array();
			echo "success&band_idx=".$row['id']."&"."session_id=".$session_id."&"."band_name=".$row_profile['band_name'];
		} else {
			echo "password_?";
		}
	}
	
	function set_band_next_meeting_date(){
		$band_idx = $_POST['band_idx'];
		$new_date = $_POST['new_date'];
		$data = array("next_meeting_date" => $new_date);
		$this->db->where('user_id', $band_idx);
		$query = $this->db->update('user_profiles', $data);
	}
	
	function get_band_next_meeting_date(){
		$band_idx = $_POST['band_idx'];
		if (empty($band_idx)) {
			echo "invalid";
			return;
		}
		/*$session_id = $this->session->userdata('session_id');
		if ($session_id!=$check_key){
			echo "session_lost";
			return;
		}*/
		
		//$strsql = "select tblband.idx, tblband.manager_idx, manager_first_name, manager_last_name, next_meeting_date, tblmanager.call_number, tblmanager.email_address, payment_due_date, amount_due from tblband, tblmanager where tblband.manager_idx=tblmanager.idx and tblband.idx='$band_idx'";
		$strsql = "select * from user_profiles where user_id=$band_idx";
		
		$query = $this->db->query($strsql);
		$row = $query->row_array();
		//print_r($row);
		if (empty($row)) {
			echo "empty";
			return;
		}
		
		if (empty($row['next_meeting_date'])) {
			echo "NULL";
		} else {
			echo "success&".$row['next_meeting_date'];
		}
	}
	
	function get_band_info(){
		$band_idx = $_POST['band_idx'];
		//$check_key = $_POST['check_id'];
		if (empty($band_idx)) {
			echo "invalid";
			return;
		}
		/*$session_id = $this->session->userdata('session_id');
		if ($session_id!=$check_key){
			echo "session_lost";
			return;
		}*/
		
		//$strsql = "select tblband.idx, tblband.manager_idx, manager_first_name, manager_last_name, next_meeting_date, tblmanager.call_number, tblmanager.email_address, payment_due_date, amount_due from tblband, tblmanager where tblband.manager_idx=tblmanager.idx and tblband.idx='$band_idx'";
		$strsql = "select * from user_profiles where user_id=$band_idx";
		
		$query = $this->db->query($strsql);
		$row = $query->row_array();
		//print_r($row);
		if (empty($row)) {
			echo "empty";
			return;
		}
		
		$manager_idx = $row['manager_idx'];
		
		$strsql = "select * from user_profiles,users where users.id=user_profiles.user_id and users.id='$manager_idx'";
		
		$query = $this->db->query($strsql);
		$row_manager = $query->row_array();
		
		$rate_array = $this->rate_m->band_rate_in_month($band_idx, date('Y-m-d'));
		$rate_up_number = $rate_array['up_number'];
		$rate_down_number = $rate_array['down_number'];
		
		echo "success&";
		echo "manager_idx=".$row['manager_idx']."&";
		echo "manager_name=".$row_manager['full_name']."&";
		echo "next_meeting_date=".$row['next_meeting_date']."&";
		echo "manager_call_number=".$row_manager['phone']."&";
		echo "manager_email_address=".$row_manager['email']."&";
		echo "payment_due_date=".$row['pay_date']."&";
		echo "amount_due=".$row['pay_amount']."&";
		echo "rate_up=".$rate_up_number."&";
		echo "rate_down=".$rate_down_number."&";
		echo "alert_call=".$row['alert_call_number']."&";
		echo "alert_email=".$row['alert_email_number'];
	}
	
	function get_band_project(){
		$band_idx = $_POST['band_idx'];
		$check_key = $_POST['check_id'];
		if (empty($band_idx) || empty($check_key)) {
			echo "invalid";
			return;
		}
		/*$session_id = $this->session->userdata('session_id');
		if ($session_id!=$check_key){
			echo "session_lost";
			return;
		}*/
		
		$strsql = "select * from tblprojectdetails where master_idx='$band_idx' and project_type='0'";
		$query = $this->db->query($strsql);
		$rows = $query->result_array();
		
		if (empty($rows)) {
			echo "empty";
			return;
		}
		
		header("Content-type: text/xml;charset=utf-8");
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		echo "<projects>\n";
		$i=0;
		foreach ($rows as $row) {
			$i++;
			//$txt = html_entity_decode(str_replace($order, "", strip_tags($row['content'])),ENT_QUOTES, 'UTF-8') ;
			echo 	"<item>\n".
					"<no>$i</no>\n".
					"<idx>".$row['idx']."</idx>\n".
					"<name>".$row['project_name']."</name>\n".
					"<status>".$row['project_status']."</status>\n".
					"<date>".$row['project_estimated_completion_date']."</date>\n".
					"</item>\n";
			
			$i++;
		}
		echo "</projects>";
	}
}
?>