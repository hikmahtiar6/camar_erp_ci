<?php

class Do_model extends CI_Model {
    
    const TABLE = 'MasterDo';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * save data
     */
    public function save($data)
    {
        return $this->db->insert(static::TABLE, $data);
    }
    
    /**
     * update data
     */
    public function update($id, $data)
    {
        $this->db->where('MasterDoId', $id);
        
        return $this->db->update(static::TABLE, $data);
    }
    
    /**
     * delete data
     */
    public function delete($id)
    {
        $this->db->where('MasterDoId', $id);
        return $this->db->delete(static::TABLE);
    }
    
    /**
     * get data do
     */
    public function get_data()
    {
        $sql = $this->db;
        
        $sql->select('*');
        $sql->from(static::TABLE);
        $sql->order_by('MasterDoId', 'ASC');
        
        $get = $sql->get();
        return $get;
    }
}