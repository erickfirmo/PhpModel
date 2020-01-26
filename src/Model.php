<?php

namespace ErickFirmo;

use DBConnection;

abstract class Model {
    protected $paginate = false;
    protected $limit = false;
    protected $cascade = false;
    protected $action = NULL;
    public $pivot_entity = NULL;
    public $pivot_parent_id = NULL;
    public $pivot_table = NULL;

    public function getPDOConnection()
    {
        return (new DBConnection())->getPDOConnection();
    }

    public function save()
    {    
        $fields = NULL;
        $values = NULL;
        foreach ($this->fields as $key => $field)
        {
            if(count($this->fields) != $key+1)
            {
                $fields = $fields.$field.',';
                $values = $values.'?,';
            } else {
                $fields = $fields.$field;
                $values = $values.'?';
            }
        }
        $db = $this->getPDOConnection();
        $sql = 'INSERT INTO '.$this->table.' ('.$fields.') VALUES ('.$values.')';
        $stmt = $db->prepare($sql);
        foreach ($this->fields as $key => $field)
        {  
            $stmt->bindValue($key+1, $this->$field);
        }
        $stmt->execute();
        $_SESSION['PARAMETER'] = $db->lastInsertId();
    }

    public function find($id)
    {
        $db = $this->getPDOConnection();
        $sql = 'SELECT * FROM '.$this->table.' WHERE id='.$id;
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $register = $stmt->fetch();
        return $this->createObject($register, static::class);
    }
}



