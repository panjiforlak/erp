<?php defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: japasys Ltd
# Author link: https://www.japasys.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class App_setting extends CI_Model
{

	private $table = "appsetting";

	public function create($data = [])
	{
		return $this->db->insert($this->table, $data);
	}

	public function read()
	{
		return $this->db->select("*")
			->from($this->table)
			->get()
			->row();
	}

	public function update($data = [])
	{
		return $this->db->where('id', $data['id'])
			->update($this->table, $data);
	}
}
