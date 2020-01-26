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
}



