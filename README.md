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
      
      protected $table = 'cars';
      
      protected $fillable = [
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
      'company' => $company,
      'year' => $year,
      'plate' => $plate,
      'color' => $color,
  ]);
  
  // Select register example, returns collection
  $cars = (new Car())->select()
                     ->where('year', '=', $year)
                     ->get();

```
## Collections

```json


{
  "model": "App\\Models\\Customer",
  "table": "customers",
  "attributes": [
    "id",
    "name",
    "company",
    "year",
    "plate",
    "uf",
    "color",
    "price"
  ],
  "items": [
    {
      "id": "12",
      "name": "Fusca",
      "company": "VW",
      "year": "1934",
      "plate": "ERX-8761",
      "uf": "SP",
      "color": "yellow",
      "price": "89000"
    },
    {
      "id": "13",
      "name": "Uno",
      "company": "Fiat",
      "year": "1934",
      "plate": "ERX-8761",
      "uf": "SP",
      "color": "red",
      "price": "89000"
    },
    {
      "id": "14",
      "name": "Chevette",
      "company": "Chevrolet",
      "year": "1934",
      "plate": "ERX-8761",
      "uf": "SP",
      "color": "black",
      "price": "89000"
    },
  ],
  "pages": [
  
  ],
}


```


## Query
Methods that facilitate the execution of mysql queries in the database:

#### Select

Select all columns from the table using the `select` method. Use the `get` method to perform a query and return a collection:
```php
<?php

  // Returns all columns from `cars` table
  $cars = (new Car())->select()
                    ->get();

```

Select specific columns from the table passing an array as parameter in `select` method. Use the `get` method to perform a query:
```php
<?php

  // Returns specific columns from model table
  $cars = (new Car())->select(['name', 'company', 'year'])
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
Inserting record into database table:

```php
<?php

  $saved = (new Car())->insert([
      'name' => $name,
      'company' => $company,
      'plate' => $plate,
      'year' => $year,
      'color' => $color,
  ]);

```

#### Update
Updating register into database table:
```php
<?php

  $saved = (new Car())->update($id, [
            'plate' => $plate,
            'color' => $color,
        ]);

```

#### Delete
Deleting register into database table:
```php
<?php

  $saved = (new Car())->delete($id);

```

#### FindById
Searching register by id:

```php
<?php

  $car = (new Car())->findById($id);


```

#### OrderBy
You can configure the ordering as ascending or descending using the words `asc` or `desc` as parameter in `OrderBy` method:


##### Ordering as ascending
```php
<?php

  $cars = (new Car())->select()
                     ->orderBy('asc')
                     ->get();
                     
```
##### Ordering as descending:

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
#### Pagination
```php
<?php

  $cars = (new Car())->select()
                    ->paginate(15);


```

Use the paginationLinks helper.

```html

<nav aria-label="Page navigation example">
    <ul class="pagination">
        <?php
        // percorre página e cria link de páginação
        for ($i=$start; $i <= $end; $i++) { ?>
            <li class="page-item <?php activePage('pessoas', 'active', ['page' => $i]); ?>"><a class="page-link" href="<?php url('pessoas?page='.$i); ?>">
                 <?php echo $i; ?>
            </a></li>
        <?php } ?>
    </ul>
</nav>


```





<!-- Relationships -->

<!-- Exceptions -->


<!--## License -->

<!--<a href="https://erickfirmo.dev" target="_blank">Érick Firmo</a>-->

