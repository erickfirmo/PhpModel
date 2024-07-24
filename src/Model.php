<?php

namespace ErickFirmo;

abstract class Model {

    protected $sql;

    protected $collection = [];

    protected $statement;

    protected $db;

    protected $perPage;

    protected $pages = [];

    protected $hasWhere;

    // realiza conexão com o banco de dados
    public function connect() : Object
    {
        return $this->db = $this->getPDOConnection();
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
        $this->pages = array_keys(array_fill(1, $pages, []));
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
    public function setCollection($registers, $singleRegister=false, array $items = [], $item = null) : void
    {
        $modelName = get_called_class();

        /// verifica se há registros antes de criar o objeto
        if($registers)
        {
            if($singleRegister) {
                array_push($items, $this->createObject($registers, $modelName));
            } else {
                foreach($registers as $key => $register) {
                    array_push($items, $this->createObject($register, $modelName));
                }
            }
        }

        $collection = new \stdClass;
        $collection->model = $modelName;
        $collection->table = $this->table;
        $collection->attributes = $this->fillable;
        $collection->items = $items;
        $collection->pages = $this->pages;
        
        $this->collection = $collection;
    }

    // cria objeto model baseado no fillable
    public function createObject($register, $modelName)
    {
        $modelItem = new $modelName;

        foreach ($this->fillable as $f) {
            $modelItem->$f = $register[$f];
        }

        return $modelItem;
    }

    // retorna objeto com os registros buscados
    public function get() : Object
    {
        $this->statement->execute();

        $registers = $this->statement->fetchAll(\PDO::FETCH_ASSOC);

        $this->setCollection($registers);

        return $this->collection;
    }

    // seta limite de dados da consulta
    public function limit(int $limit) : Object
    {
        $this->addQuery(' LIMIT '.$limit);
        $this->setStatement();

        return $this;
    }

    // retorna registros com paginação
    public function paginate(int $perPage=10, $page=1)
    {  
        $page = isset($_GET['page']) ? $_GET['page'] : $page;

        // verifica se valor da paginação não é numerico
        if(!is_numeric($page) || $page == null || $page == 0) {
            $page = 1;
        }
        
        /* busca uma determinada quantidade de itens */
        $start = ($page * $perPage) - $perPage;
        $this->addQuery(' LIMIT '.$start.', '.$perPage);
        $this->setStatement();
        $this->statement->execute();
        $registers = $this->statement->fetchAll(\PDO::FETCH_ASSOC);

        /* define quantidade de itens por página */
        $this->perPage = $perPage;

        /* limpa query */
        $this->clearQuery();

        /* define links */
        $this->addQuery('SELECT COUNT(*) FROM '.$this->table);
        $this->setStatement();
        $this->statement->execute();
        $count = $this->statement->fetchColumn();
        $this->setLink($count);

        /* define collection */
        $this->setCollection($registers);

        return $this->collection;
    }

    // ordena regitros em ordem crescente
    public function orderByAsc() : Object
    {
        $this->addQuery(' ORDER BY id ASC');
        $this->setStatement();

        return $this;
    }

    // ordena regitros em ordem decrescente
    public function orderByDesc() : Object
    {
        $this->addQuery(' ORDER BY id DESC');
        $this->setStatement();

        return $this;
    }

    // retorna query
    public function getSql()
    {
        return $this->sql;
    }

    // busca registros
    public function select(array $columns=null) : Object
    {
        $columns = !$columns ? '*' : implode(', ', $columns);

        $this->clearQuery();
        $sql = "SELECT $columns FROM ".$this->table;
        $this->addQuery($sql);
        $this->setStatement();

        return $this;
    }

    // salva registro
    public function insert(array $values)
    {
        $this->clearQuery();

        $columns = array_keys($values);
        $values = array_values($values);
        $columns = implode(", ", $columns);

        $values = implode(', ', array_map(
            function ($v) { return sprintf("'%s'", addslashes($v)); },
            $values
        ));

        $sql = "INSERT INTO ".$this->table." (".$columns.") VALUES (".$values.")";
        $this->addQuery($sql);
        $this->setStatement();

        $this->statement->execute();

        return $this->findById($this->db->lastInsertId());
    }

    // atualiza registro
    public function update(int $id, array $values)
    { 
        $this->clearQuery();

        $columns = implode(', ', array_map(
            function ($v, $k) { return sprintf("%s='%s'", $k, addslashes($v)); },
            $values,
            array_keys($values)
        ));

        $sql = 'UPDATE '.$this->table.' SET '.$columns.' WHERE id='.$id;
        $this->addQuery($sql);
        $this->setStatement();

        return $this->statement->execute();
    }

    // deleta registro
    public function delete(int $id)
    {
        $this->clearQuery();

        $sql = 'DELETE FROM '.$this->table.' WHERE id='.$id;
        $this->addQuery($sql);
        $this->setStatement();

        return $this->statement->execute();
    }

    // busca registro pelo id
    public function findById(int $id)
    {
        $this->clearQuery();

        $sql = 'SELECT * FROM '.$this->table.' WHERE id='.$id;
        $this->addQuery($sql);
        $this->setStatement();

        $this->statement->execute();

        $registers = $this->statement->fetch();

        $this->setCollection($registers, true);

        return $this->collection->items[0];
    }

    // adiciona where a query
    public function where($column, $condition, $value)
    {
        $sql = !$this->hasWhere ?  ' WHERE ' : ' AND ';
        $sql = $sql . $column . $condition . "'$value'";
        $this->addQuery($sql);
        $this->setStatement();

        $this->hasWhere = true;

        return $this;
    }

    // conexão com o banco de dados via pdo
    public function getPDOConnection()
    {
        $dsn = 'mysql:host='. DB_HOST .';dbname='. DB_NAME;

        try {
            $pdo = new \PDO($dsn, DB_USER, DB_PASSWORD);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $pdo;

        } catch(PDOException $ex) {
            print 'Error: '.$ex->getMessage();
        }
    }
}
