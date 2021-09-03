# PhpModel
 MySQL Query Builder in PHP. Developed by Érick Firmo (BR) http://erickfirmo.dev
 
## Requirements
- PHP >= 5.4

## Install
To install with composer:

```sh
composer require erickfirmo/phpmodel
```

## Connection

## Usage
```php
<?php

  // Requires composer autoloader
  require __DIR__ . '/vendor/autoload.php';

  // Insert register example
  $saved = (new Customer())->insert([
      'name' => $name,
      'email' => $email,
      'phone' => $phone,
  ]);
  
  // Select register example
  $customers = (new Customer())->select()
                               ->where('email', '=', $email)
                               ->get();

```

### Query
Lorem ipsum

#### Select
```php
<?php

  $customers = (new Customer())->select()->get();

```

#### Where
```php
<?php

  $customers = (new Customer())->select()
                               ->where('email', '=', $email)
                               ->get();

```

#### Get
```php
<?php

  $customers = (new Customer())->select()
                               ->where('email', '=', $email)
                               ->get();

```

#### Insert
```php
<?php

  $customers = (new Customer())->select()
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

