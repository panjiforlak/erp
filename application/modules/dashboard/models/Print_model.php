<?php defined('BASEPATH') or exit('No direct script access allowed');
#------------------------------------    
# Author: japasys Ltd
# Author link: https://www.japasys.com/
# Dynamic style php file
# Developed by :Isahaq
#------------------------------------    

class Print_model extends CI_Model
{

	private $table = "print_setting";

	public function create($data = [])
	{
		return $this->db->insert($this->table, $data);
	}

	public function read()
	{
		$data = $this->db->select("*")
			->from($this->table)
			->get()
			->row();
		return $data;
	}

	public function update($data = [])
	{
		return $this->db->where('id', $data['id'])
			->update($this->table, $data);
	}
}
