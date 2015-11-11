<?php
class Managers extends Controller {
	
	function Managers()
	{
		parent::Controller();
	
		$this->load->database();
		
		$this->load->library('session');
		
		$this->load->library('tank_auth');
		
		$this->load->model('rate_m');
	}
	
	function manager_get_alerts(){
		$manager_idx = $_POST['manager_idx'];
		
		$strsql = "select band_name, alert_call_number, alert_email_number ".
					"from user_profiles,users ".
					"where (alert_call_number > 0 || alert_email_number > 0) and manager_idx='$manager_idx' and role_id=0 and user_profiles.user_id=users.id";
		$query = $this->db->query($strsql);
		$rows = $query->result_array();
		
		if (empty($rows)) {
			echo "empty";
			return;
		}
		
		header("Content-type: text/xml;charset=utf-8");
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		echo "<alerts>\n";
		$i=0;
		foreach ($rows as $row) {
			$i++;
			echo 	"<item>\n".
					"<no>$i</no>\n".
					"<band_name>".$row['band_name']."</band_name>\n".
					"<call_number>".$row['alert_call_number']."</call_number>\n".
					"<email_number>".$row['alert_email_number']."</email_number>\n".
					"</item>\n";
			
		}
		echo "</alerts>";
	}
	
	function manager_get_bands_list(){
		$manager_idx = $_POST['manager_idx'];
		
		$strsql = "select * from user_profiles,users ".
					"where manager_idx='$manager_idx' and role_id=0 and user_profiles.user_id=users.id";
		
		$query = $this->db->query($strsql);
		$rows = $query->result_array();
		
		if (empty($rows)) {
			echo "empty";
			return;
		}
		
		header("Content-type: text/xml;charset=utf-8");
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		echo "<bands>\n";
		$i=0;
		foreach ($rows as $row) {
			$i++;
			
			$rate_array = $this->rate_m->band_rate_in_month($row['user_id'], date('Y-m-d'));
			$rate_up_number = $rate_array['up_number'];
			$rate_down_number = $rate_array['down_number'];
		
			echo 	"<item>\n".
					"<no>$i</no>\n".
					"<band_idx>".$row['user_id']."</band_idx>\n".
					"<band_name>".$row['band_name']."</band_name>\n".
					"<phone>".$row['phone']."</phone>\n".
					"<email>".$row['email']."</email>\n".
					"<facebook>".$row['website']."</facebook>\n".
					"<rate_up>".$rate_up_number."</rate_up>\n".
					"<rate_down>".$rate_down_number."</rate_down>\n".
					"</item>\n";
			
		}
		echo "</bands>";
	}
	
	function manager_login(){
		
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
		
		$strSql = "select * from users where upper(username)=upper('".$user_name."') and role_id=2";
		$query = $this->db->query($strSql);
		$row = $query->row_array();

		if (empty($row) || empty($row['id'])) {
			echo "fail";
			return;
		}
		
		if ($hasher->CheckPassword($user_password, $row['password'])){
			$session_id = $this->session->userdata('session_id');
			echo "success&manager_idx=".$row['id']."&"."session_id=".$session_id;
		} else {
			echo "password_?";
		}
	}
	
	function get_manager_info(){
		$idx=$_POST['manager_idx'];
		
		$strsql="select * from user_profiles where user_id='".$idx."'";
		$query=$this->db->query($strsql);
		$row = $query->row_array();
		if (empty($row)) {
			echo "fail";
			return;
		}
		
		$awards = $row['awards'];
		$manager_name = $row['full_name'];
		$session_id = $this->session->userdata('session_id');
		
		/*$strsql = "SELECT SUM(rate_up_number) AS rate_up_total, SUM(rate_down_number) AS rate_down_total FROM user_profiles, users WHERE manager_idx='".$idx."' and user_profiles.user_id=users.id and role_id=0";
		$query=$this->db->query($strsql);
		$row = $query->row_array();
		
		if (empty($row)) {
			$rateup_total = 0;
			$ratedown_total = 0;
		} else {
			$rateup_total = $row['rate_up_total'];
			$ratedown_total = $row['rate_down_total'];
		}*/
		
		$rate_array = $this->rate_m->manager_rate_in_month($idx, date('Y-m-d'));
		$rate_up_number = $rate_array['up_number'];
		$rate_down_number = $rate_array['down_number'];
		

		$strsql = "SELECT COUNT(*) AS total_count FROM user_profiles, users WHERE manager_idx='$idx' AND (alert_call_number > 0 OR alert_email_number > 0) and user_profiles.user_id=users.id and role_id=0";
		$query=$this->db->query($strsql);
		$row = $query->row_array();
		
		if (empty($row)) {
			$alert_total = 0;
		} else {
			$alert_total = $row['total_count'];
		}
		
		$strsql = "SELECT COUNT(*) AS total_count FROM user_profiles, users WHERE manager_idx='$idx' and user_profiles.user_id=users.id and role_id=0";
		$query=$this->db->query($strsql);
		$row = $query->row_array();
		if (empty($row)) {
			$bands_total = 0;
		} else {
			$bands_total = $row['total_count'];
		}
		
		echo "success&name=".$manager_name."&awards=".$awards."&rateup=".$rate_up_number."&ratedown=".$rate_down_number."&alert_total=".$alert_total."&bands_number=".$bands_total;
	}
	
	function get_manager_project(){
		$band_idx = $_POST['manager_idx'];		$for_whom = $_POST['band_idx'];
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
		
		$strsql = "select * from tblprojectdetails where master_idx='$band_idx' and project_type='1' and for_whom='$for_whom'order by idx";
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
	
	function get_payment_info() {
		$manager_idx = $_POST['manager_idx'];
		$strsql = "select user_id, band_name, pay_amount, pay_type, pay_date from user_profiles, users".
					" where users.id=user_profiles.user_id and manager_idx=$manager_idx and pay_date>'2000-01-01'";
		$query = $this->db->query($strsql);
		$rows = $query->result_array();
		
		if (empty($rows)) {
			echo "empty";
			return;
		}
		
		header("Content-type: text/xml;charset=utf-8");
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		echo "<payment>\n";
		$i=0;
		foreach ($rows as $row) {
			$i++;
			//$txt = html_entity_decode(str_replace($order, "", strip_tags($row['content'])),ENT_QUOTES, 'UTF-8') ;
			echo 	"<item>\n".
					"<no>$i</no>\n".
					"<idx>".$row['user_id']."</idx>\n".
					"<name>".$row['band_name']."</name>\n".
					"<amount>".$row['pay_amount']."</amount>\n".
					"<type>".$row['pay_type']."</type>\n".
					"<date>".$row['pay_date']."</date>\n".
					"</item>\n";
			
			$i++;
		}
		echo "</payment>";
	}
	
	function get_manager_rate_history(){
		$manager_idx = $_POST['manager_idx'];
		$rate_type = $_POST['rate_type'];
		
		if ($rate_type=="up")
			$rate_type=1;
		else
			$rate_type=0;
		$strsql = "SELECT SUM(rate_number) as rate_count,  rate_date FROM tblrate, users, user_profiles 
					WHERE band_id = users.id AND users.id = user_profiles.user_id AND manager_idx='$manager_idx' AND rate_type=$rate_type and rate_date like '".
					substr(date('Y-m-d'),0,7)."%'".
					" GROUP BY rate_date".
					" ORDER BY rate_date";
		
		$query = $this->db->query($strsql);
		$rows = $query->result_array();
		
		if (empty($rows)) {
			echo "empty";
			return;
		}
		
		header("Content-type: text/xml;charset=utf-8");
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		echo "<payment>\n";
		$i=0;
		foreach ($rows as $row) {
			$i++;
			//$txt = html_entity_decode(str_replace($order, "", strip_tags($row['content'])),ENT_QUOTES, 'UTF-8') ;
			echo 	"<item>\n".
					"<no>$i</no>\n".
					"<date>".$row['rate_date']."</date>\n".
					"<count>".$row['rate_count']."</count>\n".
					"</item>\n";
		}
		echo "</payment>";
	}
	
	function set_manager_awards(){
		$manager_idx=$_POST['manager_idx'];
		$awards=$_POST['awards'];
		
		$data = array(
           'awards' => $awards,
        );

		$this->db->where('user_id', $manager_idx);
		$query = $this->db->update('user_profiles', $data);

		if ($query == true) {
			echo "SUCCESS";
		} else {
			echo "FAILED";
		}
	}
}
?>
