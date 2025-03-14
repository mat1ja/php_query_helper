# PHP queryHelper
QueryHelper is a simple and efficient query builder for generating SQL queries dynamically. It helps streamline query creation, making database interactions faster and more manageable. Designed for flexibility, it reduces errors and improves maintainability.

## Examples

### Select
```
$query = new queryHelper();

$query->addTable('user');
$query->addField('user.*');
$query->addWhere('user.country', 'New York', 'LIKE');
$query->addWhere('user.age', 18, '>');
$query->addJoin('company', 'company.id = user.company_id');
$query->addPagination(1, 15);
$query->addSort('user.create', 'ASC');

print($query->getQuery());
print_r($query->getParameters());
```

#### Output
```
SELECT user.* FROM user JOIN company ON company.id = user.company_id WHERE user.country LIKE CONCAT('%', :par_1, '%') AND user.age > :par_2 ORDER BY user.create ASC LIMIT 15 OFFSET 15

Array
(
    [par_1] => New York
    [par_2] => 18
)
```

### Insert
```
$query = new queryHelper();

$data = array(
	'firstname' => 'Ivo',
	'lastname' => 'Horvatic',
	'city' => 'New York'
);

$query->addTable("user");
$query->addInserts($data);

print($query->getQuery());
print_r($query->getParameters());
```

#### Output
```
INSERT INTO user (firstname, lastname, city) VALUES (:par_1, :par_2, :par_3)

Array
(
    [par_1] => Ivo
    [par_2] => Horvatic
    [par_3] => New York
)
```

### Edit
```
$query = new queryHelper();

$data = array(
	'firstname' => 'Ivan',
	'lastname' => 'Horvat',
	'city' => 'New York'
);


$query->addTable("aform_job");
$query->addWhere("user_id", 10);
$query->addUpdates($data);

print($query->getQuery());
print_r($query->getParameters());
```

#### Output
```
UPDATE aform_job SET firstname = :par_2, lastname = :par_3, city = :par_4 WHERE user_id = :par_1

Array
(
    [par_1] => 10
    [par_2] => Ivan
    [par_3] => Horvat
    [par_4] => New York
)
```

### Delete
```
$query = new queryHelper();

$query->addTable("user");
$query->addWhere("id", 10);
$query->addDelete();
$query->limit(1);

print($query->getQuery());
print_r($query->getParameters());
```

#### Output
```
DELETE FROM user WHERE id = :par_1 LIMIT 1

Array
(
    [par_1] => 10
)

```
