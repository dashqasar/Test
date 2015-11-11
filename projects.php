<?php
	class Projects extends Controller {
		function Projects()
		{
			parent::Controller();
		
			$this->load->database();
			
			$this->load->library('session');
		}
		
		function remove() {
			$project_idx=$_POST['project_idx'];
			$query = $this->db->delete('tblprojectdetails', array('idx' => $project_idx));
			if ($query == true) {
				echo "SUCCESS" ;
			} else {
				echo "FAILED";
			}
		}
		
		function add(){
			$project_name=$_POST['project_name'];
			$due_date=$_POST['due_date'];
			$project_type = $_POST['project_type'];
			$master_idx = $_POST['master_idx'];
			$for_whom = $_POST['for_whom'];
			
			$data = array(
               'project_name' => $project_name ,
                'project_status' => 0,
                'project_type' => $project_type,
                'master_idx' => $master_idx,
               'project_estimated_completion_date' => $due_date,
                'for_whom' => $for_whom
            );
        	$query = $this->db->insert('tblprojectdetails', $data);
        	if ($query == true) {
				echo "SUCCESS&".$this->db->insert_id();
			} else {
				echo "FAILED";
			}
			
			
		}
		
		function update(){
			$project_idx=$_POST['project_idx'];
			$project_status=$_POST['project_status'];
			$due_date=$_POST['due_date'];
			
			$data = array(
               'project_status' => $project_status,
               'project_estimated_completion_date' => $due_date,
            );

			$this->db->where('idx', $project_idx);
			$query = $this->db->update('tblprojectdetails', $data);

			if ($query == true) {
				echo "SUCCESS";
			} else {
				echo "FAILED";
			}
		}
	}
?>