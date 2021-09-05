# PhpModel
 MySQL Query Builder in PHP. Developed by Érick Firmo (BR) http://erickfirmo.dev
 
## Requirements
- PHP >= 5.4

## Install
To install with composer:

```sh
composer require erickfirmo/phpmodel
```

## Usage example
```php
<?php

  // Requires composer autoloader
  require __DIR__ . '/vendor/autoload.php';

  // Insert register example, returns boolean
  $saved = (new Customer())->insert([
      'name' => $name,
      'email' => $email,
      'phone' => $phone,
  ]);
  
  // Select register example, returns colletion
  $customers = (new Customer())->select()
                               ->where('email', '=', $email)
                               ->get();

```

### Query
Methods that facilitate the execution of mysql queries in the database:

#### Select

Select all columns from the table using the `select` method. Use the `get` method to perform a query:
```php
<?php

  // Returns all columns from `cars` table
  $car = (new Car())->select()
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
```php
<?php

  $cars = (new Car())->select()
                     ->where('email', '=', $email)
                     ->get();

```

#### Update
```php
<?php

  $customers = (new Customer())->select()
                               ->where('email', '=', $email)
                               ->get();

```

#### Delete
```php
<?php

  $customers = (new Customer())->select()
                               ->where('email', '=', $email)
                               ->get();

```

#### FindById
```php
<?php

  $customers = (new Customer())->select()
                               ->where('email', '=', $email)
                               ->get();

```

#### OrderBy
```php
<?php

  $customers = (new Customer())->select()
                               ->where('email', '=', $email)
                               ->get();

```

#### Limit
```php
<?php

  $customers = (new Customer())->select()
                               ->where('email', '=', $email)
                               ->get();

```
### Pagination
```php
<?php



```

### Collection
```php
<?php



```

### Namespace

```php
<?php

```

<!-- Relationships -->

<!-- Exceptions -->


<!--## License -->

<!--<a href="https://erickfirmo.dev" target="_blank">Érick Firmo</a>-->

