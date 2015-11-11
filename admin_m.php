<?php
class Admin_m extends Model
{
//	private $table_name			= 'users';			// user accounts
//	private $profile_table_name	= 'user_profiles';	// user profiles

	var $USER_TABLE = 'users';
	
	function Admin_m()
	{
		parent::Model();
		
		
	}
	function get_manager_of_band($band_idx){		$strSql = "SELECT manager_idx FROM user_profiles WHERE user_id='$band_idx'";		$query = $this->db->query($strSql);		$row = $query->row_array();		$manager_idx = $row['manager_idx'];		$strSql = "SELECT full_name FROM user_profiles WHERE user_id='$manager_idx'";		$query = $this->db->query($strSql);		$row = $query->row_array();		if (empty($row))			return "";		else			return $row['full_name'];	}
	function &get_managers($man_type=2) {
		
		$arr = array(
			0 => "-----",
		);
		
		$strSql = "SELECT id, username FROM users WHERE role_id='$man_type' ";
		$query = $this->db->query($strSql);
		$rows = $query->result_array();
		foreach($rows as $row) {
			$key = $row['id'];
			$arr[$key] = $row['username'];
		}
		return $arr;
	}
	
	function is_userid_available($input_uname, $my_uidx) 
	{
		$this->db->select('1', FALSE);
		$this->db->where('LOWER(username)=', strtolower($input_uname));
		$this->db->where('id!=', $my_uidx, FALSE);
	//	echo "<br>sql=".$this->db->_compile_select();
		$query = $this->db->get($this->USER_TABLE);
		return $query->num_rows() == 0;
	}
	
	function is_email_available($input_uemail, $my_uidx) {
		$this->db->select('1', FALSE);
		$this->db->where('LOWER(email)=', strtolower($input_uemail));
	//	$this->db->or_where('LOWER(new_email)=', strtolower($input_uemail));
		$this->db->where('id!=', $my_uidx, FALSE);
		
	//	echo "<br>sql=".$this->db->_compile_select();
		
		$query = $this->db->get($this->USER_TABLE);
		return $query->num_rows() == 0;
	}
	
	function get_user_role($user_idx) {
		$this->db->select('role_id', FALSE);
		$this->db->where('id', $user_idx, FALSE);
	//	echo "<br>sql=".$this->db->_compile_select();
		$query = $this->db->get($this->USER_TABLE);
		$row = $query->row_array();
		if(empty($row)) {
			return 0;
		}
		return $row['role_id'];
	}
	
	function &get_user_list() {
		
		$strSql = "SELECT users.id as id, username, email, role_id, full_name, band_name FROM users, user_profiles WHERE users.id=user_profiles.user_id order by users.id";
		$query = $this->db->query($strSql);
		$rows = $query->result_array();
		
		return $rows;
	}
	
	function set_profile($user_idx, &$arr_q) {
		
		$ret = FALSE;
		
		$strSql = "SELECT COUNT(*) AS cnt FROM user_profiles WHERE user_id='$user_idx' "; 
		$query = $this->db->query($strSql);
		$row = $query->row_array();
		$cnt = $row['cnt'];
		
		if( $cnt == 0 ) 
		{
			$arr_q['user_id'] = $user_idx;
			$ret = $this->db->insert("user_profiles",$arr_q);
		}
		else
		{
			$this->db->where('user_id', $user_idx);
			$ret = $this->db->update("user_profiles",$arr_q);
		}
		return $ret;
		
	}
	
	
}
