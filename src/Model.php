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

    #public static $pivot_entity = NULL;
    #public static $pivot_parent_id = NULL;
    #public static $pivot_table = NULL;

    // realiza conexão com o banco de dados
    public function connect() : Object
    {
        return $this->db = (new \Connection())->getPDOConnection();
    }

    // seta statement
    public function setStatement() : void
    {
        $this->statement = $this->connect()->prepare($this->getSql());
    }

    // seta array de links da paginação
    public function setLink(int $count) : void
    {
        $rest = $count % $this->perPage;
        $pages = $count / $this->perPage;
        $pages = $rest > 0 ? ($pages + 1) : $pages;
        $this->links = array_keys(array_fill(1, $pages, null));
    }

    // limpa query do objeto
    public function clearQuery() : void
    {
        $this->sql = null;
    }

    // acrescenta query a query já existente
    public function addQuery(string $sql) : Object
    {
        $this->sql = $this->sql . $sql;

        return $this;
    }

    // seta objeto com os registros da consulta
    public function setCollection(array $registers, $singleRegister=false, array $items = [], $item = null) : void
    {
        $modelName = get_called_class();

        if($singleRegister) {
            $items = (object) $registers;
        } else {
            foreach($registers as $register) {
                $item = new $modelName;
                // cria objeto model baseado no fillable
                foreach ($this->fillable as $f) {
                    $item->$f = $register[$f];
                }

                array_push($items, $item);
            }
        }

        $collection = new \stdClass;
        $collection->items = $items;
        $collection->links = $this->links;
        
        $this->collection = $collection;
    }

    /*
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

    public static function pivot()
    {
        $pivot_params = $_SESSION['PIVOT_PARAMS'];
        $pivot_entity_name = $pivot_params['entity'];
        return self::findPivot($pivot_entity_name, $pivot_params['table'], $pivot_params['parent_id'], $pivot_params['parent_table'], self::$id);
    }
    */
}
