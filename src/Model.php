<?php

namespace ErickFirmo;

abstract class Model {

    protected $sql;

    protected $collection = [];

    protected $statement;

    protected $db;

    protected $perPage;

    public $links;

    public $hasWhere;

    #protected static $paginate = false;
    #protected static $limit = false;
    #protected static $cascade = false;
    #protected static $action = NULL;
    public static $pivot_entity = NULL;
    public static $pivot_parent_id = NULL;
    public static $pivot_table = NULL;

    // realiza conexão com o banco de dados
    public function connect() : Object
    {
        return $this->db = (new \Connection())->getPDOConnection();
    }

    //Crud methods
    public static function save()
    {    
        $fields = NULL;
        $values = NULL;
        foreach (self::$fields as $key => $field)
        {
            if(count(self::$fields) != $key+1)
            {
                $fields = $fields.$field.',';
                $values = $values.'?,';
            } else {
                $fields = $fields.$field;
                $values = $values.'?';
            }
        }
        $db = self::getPDOConnection();
        $sql = 'INSERT INTO '.static::$table.' ('.$fields.') VALUES ('.$values.')';
        $stmt = $db->prepare($sql);
        foreach (self::$fields as $key => $field)
        {  
            $stmt->bindValue($key+1, self::$field);
        }
        $stmt->execute();
        $_SESSION['PARAMETER'] = $db->lastInsertId();
    }

    public static function find($id)
    {
        $db = self::getPDOConnection();
        $sql = 'SELECT * FROM '.static::$table.' WHERE id='.$id;
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $register = $stmt->fetch();
        return self::createObject($register, static::class);
    }

    public static function update(array $updates)
    {    
        $fields = NULL;
        foreach (self::$fields as $key => $field)
        {
            if(count(self::$fields) != $key+1)
            {
                $fields = $fields.' '.$field.'= :'.$field.',';
            } else {
                $fields = $fields.' '.$field.'= :'.$field;
            }
        }
        $db = self::getPDOConnection();
        $sql = 'UPDATE '.static::$table.' SET '.$fields.' WHERE id="'.self::$id.'"';
        $stmt = $db->prepare($sql);
        foreach ($updates as $key => $update)
        {  
            $stmt->bindValue(':'.$key, $update);
        }
        $stmt->execute();
    }

    public static function all()
    {
        $sql = 'SELECT * FROM '.static::$table;
        $db = self::getPDOConnection();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $registers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if(self::$paginate)
        {
            self::setPagination($registers);
            $sql = 'SELECT * FROM '.static::$table.' LIMIT '.self::getLimit();

            if($_SESSION['PAGE'] > 1)
            {
                $sql = $sql.' OFFSET '.($_SESSION['PAGE'] - 1)*self::getLimit();
            }
            $_SESSION['PAGINATE'] = true;
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $registers = $stmt->fetchAll(\PDO::FETCH_ASSOC); 
        } else {
            $_SESSION['PAGINATE'] = false;
        }
        return self::objectsConstruct($registers, self::getNameOfClass());
    }

    public static function where($condition)
    {
        $db = self::getPDOConnection();
        $sql = 'SELECT * FROM '.static::$table.' WHERE '.$condition;
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $registers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if(self::$paginate)
        {
            self::setPagination($registers);
            if($_SESSION['PAGE'] > 1)
            {
                $sql = $sql.' OFFSET '.($_SESSION['PAGE'] - 1)*self::getLimit();
            }
            $_SESSION['PAGINATE'] = true;
            $stmt = self::getStmt($sql);
            $stmt->execute();
            $registers = $stmt->fetchAll(\PDO::FETCH_ASSOC); 
        } else {
            $_SESSION['PAGINATE'] = false;
        }
        if(count($registers) > 1)
            return self::objectsConstruct($registers, static::class);
        else
            return self::createObject($registers, static::class);
    }

    public static function like($operator, $values)
    {
        $db = self::getPDOConnection();
        $sql = 'SELECT * FROM '.static::$table;
        $filter = '';
        foreach ($values as $value => $fieldName) {
            $filter .= $filter == '' ? " WHERE $fieldName LIKE "."'"."%".$value."%"."'" : " $operator $fieldName LIKE "."'"."%".$value."%"."'";
        }
        $sql .= $filter;
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $registers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if(self::$paginate)
        {
            self::setPagination($registers);
            if($_SESSION['PAGE'] > 1)
            {
                $sql = $sql.' OFFSET '.($_SESSION['PAGE'] - 1)*self::getLimit();
            }
            $_SESSION['PAGINATE'] = true;
            $stmt = self::getStmt($sql);
            $stmt->execute();
            $registers = $stmt->fetchAll(\PDO::FETCH_ASSOC); 
        } else {
            $_SESSION['PAGINATE'] = false;
        }
        if(count($registers) > 1)
            return self::objectsConstruct($registers, static::class);
        else
            return self::createObject($registers, static::class);
    }

    public static function delete($id)
    {
        $db = self::getPDOConnection();
        $sql = 'DELETE FROM '.static::$table.' WHERE id='.$id;
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }

    //Constructor methods
    public static function createObject($register, $class_name)
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
    
    public static function objectsConstruct($registers, $class_name)
    {
        $objects = [];
        if(!empty($registers))
        {
            foreach ($registers as $register)
            {
                array_push($objects, self::createObject($register, $class_name));
            }
        }
        
        return $objects;
    }

    //Pagination methods
    public static function setPagination($registers)
    {
        $_SESSION['PAGES_NUMBER'] = count($registers) / self::getLimit();
    }

    public static function paginate($limit)
    {
        self::$paginate = true;
        self::$limit = $limit;
        return $this;
    }
     
    public static function getLimit()
    {
        return self::$limit;
    }

    public static function getNameOfClass()
    {
        return static::class;
    }

    //Relationship methods
    public static function hasMany($entity, $parent_id)
    {
        $db = self::getPDOConnection();
        $sql = 'SELECT * FROM '.$entity->table.' WHERE '.$parent_id.'='.self::$id;
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $registers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return self::objectsConstruct($registers, $entity->getNameOfClass());
    }

    public static function belongsTo($entity, $parent_id)
    {
        $db = self::getPDOConnection();
        $sql = 'SELECT * FROM '.$entity->table.' WHERE id='.self::$$parent_id;
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $registers = $stmt->fetch();
        return self::createObject($registers, $entity->getNameOfClass());
    }

    public static function hasOne()
    {
        $db = self::getPDOConnection();
        $sql = 'SELECT * FROM '.$entity->table.' WHERE id='.self::$$parent_id;
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $registers = $stmt->fetch();
        return self::createObject($registers, $entity->getNameOfClass());
    }

    public static function belongsToMany($entity, $pivot_entity, $parent_id_a, $parent_id_b)
    {
        self::setPivot($pivot_entity, $parent_id_a, static::$table);
        $fields = NULL;

        foreach ($entity->fields as $key => $field)
        {
            if(count($entity->fields) == $key+1)
            {
                $fields = $fields.''.$field;
            } else {
                $fields = $fields.' '.$field.', ';
            }
        }

        $db = self::getPDOConnection();
        $sql = 'SELECT '.$entity->table.'.id, '.$fields.' FROM '.$entity->table.' RIGHT JOIN '.$pivot_entity->table.' AS pivot ON pivot.'.$parent_id_a.'='.self::$id.' AND pivot.'.$parent_id_b.'='.$entity->table.'.id WHERE '.$entity->table.'.id IS NOT NULL';
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $registers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $object = self::objectsConstruct($registers, $entity->getNameOfClass());
        return $object;
    }


    //pivot
    public static function setPivot($pivot_entity, $pivot_parent_id, $parent_table)
    {
        $pivot_params = [];
        $pivot_params['entity'] = $pivot_entity->getNameOfClass();
        $pivot_params['table'] = $pivot_entity->table;
        $pivot_params['parent_id'] = $pivot_parent_id;
        $pivot_params['parent_table'] = $parent_table;
        $_SESSION['PIVOT_PARAMS'] = $pivot_params;
    }

    public static function findPivot($pivot_entity_name, $pivot_table, $pivot_parent_id, $parent_table, $value)
    {
        $db = self::getPDOConnection();
        $sql = 'SELECT pivot.id FROM '.$pivot_table.' AS pivot INNER JOIN '.$parent_table.' AS parent ON pivot.'.$pivot_parent_id.'=parent.id';
        $stmt = $db->prepare($sql);
        
        $stmt->execute();
        $register = $stmt->fetch();
        $obj = self::createObject($register, $pivot_entity_name);
        $obj->pivot_entity = $pivot_entity_name;
        $obj->pivot_parent_id = $pivot_parent_id;
        $obj->pivot_table = $pivot_table;
        return $obj;
    }

    public static function findBy($conditions)
    {
        $db = self::getPDOConnection();
        $sql = 'SELECT * FROM '.static::$table.' WHERE '.$conditions;
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $register = $stmt->fetch();
        return self::createObject($register, static::class);
    }

    public static function pivot()
    {
        $pivot_params = $_SESSION['PIVOT_PARAMS'];
        $pivot_entity_name = $pivot_params['entity'];
        return self::findPivot($pivot_entity_name, $pivot_params['table'], $pivot_params['parent_id'], $pivot_params['parent_table'], self::$id);
    }

    //
    public static function writeParents($relationMethods, $attr)
    {
        $content = null;
        if(self::$relationMethods() != null)
        {
            foreach (self::$relationMethods() as $key => $register) {
                if(count(self::$relationMethods()) == 1 || count(self::$relationMethods()) == $key-1)
                    $content = $content.$register->$attr;
                else 
                    $content = $content.$register->$attr.', ';
            }
        } else {
            return NULL;
        }
        return $content;
    }


    public static function seeInDatabase($table, $fields)
    {
        $conditions = '';
        $first = false;
        foreach ($fields as $field => $value)
        {
            if($first == false)
            {
                $first = true;
                $conditions = $field.'="'.$value.'"';
            } else {
                $conditions = $conditions.' AND '.$field.'="'.$value.'"';

            }
        }
        $db = self::getPDOConnection();
        $sql = 'SELECT * FROM '.$table.' WHERE '.$conditions;
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $register = $stmt->fetch();
        return self::createObject($register, static::class);
    }

}
