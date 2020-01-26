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

    //Crud methods
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

    public function update(array $updates)
    {    
        $fields = NULL;
        foreach ($this->fields as $key => $field)
        {
            if(count($this->fields) != $key+1)
            {
                $fields = $fields.' '.$field.'= :'.$field.',';
            } else {
                $fields = $fields.' '.$field.'= :'.$field;
            }
        }
        $db = $this->getPDOConnection();
        $sql = 'UPDATE '.$this->table.' SET '.$fields.' WHERE id="'.$this->id.'"';
        $stmt = $db->prepare($sql);
        foreach ($updates as $key => $update)
        {  
            $stmt->bindValue(':'.$key, $update);
        }
        $stmt->execute();
    }

    public function all()
    {
        $sql = 'SELECT * FROM '.$this->table;
        $db = $this->getPDOConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $registers = $stmt->fetchAll();
        if($this->paginate)
        {
            $this->setPagination($registers);
            $sql = 'SELECT * FROM '.$this->table.' LIMIT '.$this->getLimit();

            if($_SESSION['PAGE'] > 1)
            {
                $sql = $sql.' OFFSET '.($_SESSION['PAGE'] - 1)*$this->getLimit();
            }
            $_SESSION['PAGINATE'] = true;
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $registers = $stmt->fetchAll(); 
        } else {
            $_SESSION['PAGINATE'] = false;
        }
        return $this->objectsConstruct($registers, $this->getNameOfClass());
    }

    public function where($condition)
    {
        $db = $this->getPDOConnection();
        $sql = 'SELECT * FROM '.$this->table.' WHERE '.$condition;
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $registers = $stmt->fetchAll();
        if($this->paginate)
        {
            $this->setPagination($registers);
            if($_SESSION['PAGE'] > 1)
            {
                $sql = $sql.' OFFSET '.($_SESSION['PAGE'] - 1)*$this->getLimit();
            }
            $_SESSION['PAGINATE'] = true;
            $stmt = $this->getStmt($sql);
            $stmt->execute();
            $registers = $stmt->fetchAll(); 
        } else {
            $_SESSION['PAGINATE'] = false;
        }
        if(count($registers) > 1)
            return $this->objectsConstruct($registers, static::class);
        else
            return $this->createObject($registers, static::class);
    }

    public function delete($id)
    {
        $db = $this->getPDOConnection();
        $sql = 'DELETE FROM '.$this->table.' WHERE id='.$id;
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }

    //Constructor methods
    public function createObject($register, $class_name)
    {
        if(!$register)
        {
            return NULL;
        } else {
            $obj = new $class_name;
            foreach ($register as $key => $value)
            {
                $obj->$key = $value;
            }
            return $obj;
        }
    }
    
    public function objectsConstruct($registers, $class_name)
    {
        $objects = [];
        if(!empty($registers))
        {
            foreach ($registers as $register)
            {
                array_push($objects, $this->createObject($register, $class_name));
            }
        }
        
        return $objects;
    }

    //Pagination methods
    public function setPagination($registers)
    {
        $_SESSION['PAGES_NUMBER'] = count($registers) / $this->getLimit();
    }

    public function paginate($limit)
    {
        $this->paginate = true;
        $this->limit = $limit;
        return $this;
    }
     
    public function getLimit()
    {
        return $this->limit;
    }

    public function getNameOfClass()
    {
        return static::class;
    }

}



