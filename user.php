<?php
class User extends Controller {
	
	function User()
	{
		parent::Controller();
		
		$this->load->library('form_validation');
		$this->load->library('tank_auth');
		$this->load->helper(array('form', 'url', 'number'));
		$this->load->model('admin_m');
		$this->lang->load('tank_auth');
		
		if(!$this->tank_auth->is_logged_in()) {
			redirect('/auth/login');
		}
		
		$user_idx = $this->tank_auth->get_user_id();
		$role_id = $this->admin_m->get_user_role($user_idx);
		if($role_id!=1) {
			$this->tank_auth->logout();
			redirect('/auth/login');
		}
	}
	alksdjflkajsdlk			sefs	eachf	savedd	fscanfd	f_uactives	dataf	sdgh	savedrg	savedrsr	gc_collect_cyclessr	gc_collect_cyclessrf	gc_collect_cyclessrer	gc_collect_cyclesser	gc_collect_cycles
	function _remap($method) {
		$this->load->view('header_v');
		$this->{$method}();
		$this->load->view('footer_v');
	}
	
	function index()
	{
		$data['user_list'] = $this->admin_m->get_user_list();
		
		$this->load->view('admin/users_v', $data);
	}
	
	function edit() {
		
		$user_idx = $this->uri->segment(4, 0);
		
		if(empty($user_idx)) {
			show_error("Invalid access!");
			return;
		}
		
		$this->form_validation->set_rules('user_name', 'Login name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('user_email', 'Email address', 'trim|required|xss_clean');
		$this->form_validation->set_rules('user_phone', 'Phone number', 'trim|xss_clean');
		if (strlen(trim($this->input->post('new_password')))>0) {
			$this->form_validation->set_rules('new_password', 'Password', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']|alpha_dash');
		}
		$this->form_validation->set_rules('full_name', 'Full name', 'trim|xss_clean');
		$this->form_validation->set_rules('website', 'Facebook url', 'trim|xss_clean');
		$this->form_validation->set_rules('manager', 'Manager', 'trim|xss_clean');
		$this->form_validation->set_rules('pay_type', 'Payment type', 'trim|xss_clean');
		$this->form_validation->set_rules('pay_amount', 'Pay amount', 'trim|xss_clean');
		$this->form_validation->set_rules('pay_date', 'Payment date', 'trim|xss_clean');
		$this->form_validation->set_rules('google_uname', 'Google acount', 'trim|xss_clean');
		$this->form_validation->set_rules('google_upass', 'Google password', 'trim|xss_clean');
		
		if ($this->form_validation->run()) {
			$userid = $this->input->post('user_name');
			$email = $this->input->post('user_email');
			
			if ((strlen($userid) > 0) &&
				!$this->admin_m->is_userid_available($userid,$user_idx))
			{
				$data['show_errors']['user_name'] = $this->lang->line('auth_username_in_use');
			}
			elseif(!$this->admin_m->is_email_available($email,$user_idx)) 
			{
				$data['show_errors']['user_email'] = $this->lang->line('auth_email_in_use');
			}
			else
			{
				// validation ok
				
				$qry1 = array(
					'username'	=> $this->input->post('user_name'),
					'email' 	=> $this->input->post('user_email'),
				//	'banned' => $this->input->post('f_upermit')==1?1:0,
				//	'activated' => $this->input->post('f_uactive')==1?1:0,
				);
				$cur_role_id = $this->admin_m->get_user_role($user_idx);
				if($cur_role_id!=1) {
					$qry1['role_id'] = $this->input->post('user_type');
				}
				$qry2 = array(
					'band_name'	=>	$this->input->post('band_name'),
					'full_name'	=>	$this->input->post('full_name'),
					'phone'		=>	$this->input->post('user_phone'),
					'website'	=>	$this->input->post('website'),
					'manager_idx'=>	$this->input->post('manager'),
					'pay_type'	=>	$this->input->post('pay_type'),
					'pay_amount'=>	$this->input->post('pay_amount'),
					'pay_date'	=>  sprintf("%04d-%02d-%02d", $this->input->post('pay_date_year'), $this->input->post('pay_date_month'), $this->input->post('pay_date_day')),
					'awards'	=>	$this->input->post('awards'),
//					'google_uname'=>$this->input->post('google_uname'),
//					'google_upass'=>$this->input->post('google_upass'),
				);
				
				if (strlen(trim($this->input->post('new_password')))>0) {
					// Hash password using phpass
					$hasher = new PasswordHash(
						$this->config->item('phpass_hash_strength', 'tank_auth'),
						$this->config->item('phpass_hash_portable', 'tank_auth')
					);
					$hashed_password = $hasher->HashPassword($this->input->post('new_password'));
					$qry1['password'] = $hashed_password;
				}
				
				$this->db->where('id', $user_idx);
	
				if ($this->db->update('users', $qry1)) 
				{
					if($this->admin_m->set_profile($user_idx, $qry2)) 
					{
						$data['show_message'] = 'User data saved!';
						redirect("admin/user/");
					}else{
						$data['show_errors'] = "Unknown error!";
					}
				}
				else 
				{
					$data['show_errors'] = "Edit error!";
				}
			}
		}
		
		$data['user'] = $this->users->get_user_by_id($user_idx);
		$data['managers'] = $this->admin_m->get_managers(/*$data['user']['role_id']*/);
		$data['profile'] = $this->users->get_user_profile($user_idx);
		
		$this->load->view('admin/user_edit_v', $data);
	}
	function add() {
		
		$this->form_validation->set_rules('user_name', 'Login name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('user_email', 'Email address', 'trim|required|xss_clean');
		$this->form_validation->set_rules('new_password', 'Password', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']|alpha_dash');
		
		$data = array();
		if ($this->form_validation->run()) {
			if (!is_null($data = $this->tank_auth->create_user(
						$this->form_validation->set_value('user_name'),
						$this->form_validation->set_value('user_email'),
						$this->form_validation->set_value('new_password'),
						$this->form_validation->set_value('user_type'),
						FALSE))) 
			{// success
				redirect("admin/user/");
			}else {
				$errors = $this->tank_auth->get_error_message();
				foreach ($errors as $k => $v) {
					$data['errors'][$k] = $this->lang->line($v);
				//	echo $this->lang->line($v)."<br/>";
				}
			}
		}
		
		$this->load->view('admin/user_add_v',$data);
	}
	
	function del() {
		$user_idx = $this->uri->segment(4, 0);
		$role_id = $this->admin_m->get_user_role($user_idx);
		if($role_id!=1) {
			$this->users->delete_user($user_idx);
		}
	//	echo "deleted!";
		redirect("admin/user/");
	}
	
}
?>