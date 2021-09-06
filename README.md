# PhpModel
 MySQL Query Builder in PHP. Developed by Érick Firmo (BR) http://erickfirmo.dev
 
## Requirements
- PHP >= 5.4

## Install
To install with composer:

```sh
composer require erickfirmo/phpmodel
```

### Namespace

```php
<?php

 use ErickFirmo/Model;

```

## Usage example
```php
<?php

  // Requires composer autoloader
  require __DIR__ . '/vendor/autoload.php';
  
  use ErickFirmo\Model;
  
  // Creating a class for the entity
  class Car extends Model {
      
      public $table = 'cars';
      
      public $fillables = [
          'name',
          'company',
          'year',
          'plate',
          'color'
      ];
  }

  // Insert register example, returns boolean
  $saved = (new Car())->insert([
      'name' => $name,
      'email' => $email,
      'phone' => $phone,
  ]);
  
  // Select register example, returns colletion
  $cars = (new Car())->select()
                     ->where('year', '=', $year)
                     ->get();

```


### Query
Methods that facilitate the execution of mysql queries in the database:

#### Select

Select all columns from the table using the `select` method. Use the `get` method to perform a query:
```php
<?php

  // Returns all columns from `cars` table
  $cars = (new Car())->select()
                    ->get();

```

Select specific columns from the table passing an array as parameter in `select` method. Use the `get` method to perform a query:
```php
<?php

  // Returns all columns from model table
  $cars = (new Car())->select(['name', 'company', 'year', 'plate'])
                     ->get();

```

#### Where

Adding where clause to query builder:
```php
<?php

  $cars = (new Car())->select()
                     ->where('company', '=', $company)
                     ->get();

```

Adding multiple where clause to query builder:
```php
<?php

  $cars = (new Car())->select()
                     ->where('company', '=', $company)
                     ->where('year', '=', $year)
                     ->get();

```

#### Insert
insert record into database table:

```php
<?php

  $car = (new Car())->insert([
      'name' => $name,
      'company' => $company,
      'plate' => $plate,
      'year' => $year,
  ]);

```

#### Update
// update register into database table
```php
<?php

  $status = (new Car())->update($id, [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ]);

```

#### Delete
```php
<?php

  $status = (new Car())->delete($id);

```

#### FindById
```php
<?php

  $car = (new Car())->findById($id);


```

#### OrderBy
```php
<?php

  $cars = (new Car())->select()
                     ->orderBy('asc')
                     ->get();
                     
```

```php
<?php
                   
  $cars = (new Car())->select()
                     ->orderBy('desc')
                     ->get();

```

#### Limit
```php
<?php

  $cars = (new Car())->select()
                     ->limit(50)
                     ->get();

```
### Pagination
```php
<?php

  $cars = (new Car())->select()
                    ->paginate(15);


```

```html

<span>Pagination</span>


```

### Collection
```php
<?php



```


<!-- Relationships -->

<!-- Exceptions -->


<!--## License -->

<!--<a href="https://erickfirmo.dev" target="_blank">Érick Firmo</a>-->

