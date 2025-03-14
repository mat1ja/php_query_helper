# PHP queryHelper
QueryHelper is a simple and efficient query builder for generating SQL queries dynamically. It helps streamline query creation, making database interactions faster and more manageable. Designed for flexibility, it reduces errors and improves maintainability.

## Example
```
$query = new queryHelper();
$query->addTable('user');
$query->addField('user.*');
$query->addWhere('user.id', 50, '>');
$query->addJoin('company', 'company.id = user.company_id');
$query->addPagination(1, 15);
$query->addSort('user.create', 'ASC');

print($query->getQuery());
print_r($query->getParameters());
```
### Output
```
SELECT user.* FROM user JOIN company ON company.id = user.company_id WHERE user.id > :par_1 ORDER BY user.create ASC LIMIT 15 OFFSET 15

Array
(
    [par_1] => 50
)
```
