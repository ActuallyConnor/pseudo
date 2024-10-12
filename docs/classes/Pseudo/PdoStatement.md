***

# PdoStatement





* Full name: `\Pseudo\PdoStatement`
* Parent class: [`PDOStatement`](../PDOStatement.md)



## Properties


### result



```php
private \Pseudo\Result $result
```






***

### fetchMode



```php
private int $fetchMode
```






***

### boundParams



```php
private array&lt;int|string,mixed&gt; $boundParams
```






***

### boundColumns



```php
private array&lt;int|string,mixed&gt; $boundColumns
```






***

### queryLog



```php
private \Pseudo\QueryLog $queryLog
```






***

### statement



```php
private string $statement
```






***

## Methods


### __construct



```php
public __construct(mixed $result = null, \Pseudo\QueryLog|null $queryLog = null, string $statement = &#039;&#039;): mixed
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$result` | **mixed** |  |
| `$queryLog` | **\Pseudo\QueryLog&#124;null** |  |
| `$statement` | **string** |  |





***

### setResult



```php
public setResult(\Pseudo\Result|bool $result): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$result` | **\Pseudo\Result&#124;bool** |  |





***

### execute



```php
public execute(array&lt;int|string,mixed&gt;|null $params = null): bool
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$params` | **array<int&#124;string,mixed>&#124;null** |  |




**Throws:**

- [`LogicException`](./Exceptions/LogicException.md)



***

### fetch



```php
public fetch(mixed $mode = null, mixed $cursorOrientation = PDO::FETCH_ORI_NEXT, mixed $cursorOffset): mixed
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$mode` | **mixed** |  |
| `$cursorOrientation` | **mixed** |  |
| `$cursorOffset` | **mixed** |  |





***

### bindParam



```php
public bindParam(mixed $param, mixed& $var, mixed $type = PDO::PARAM_STR, mixed $maxLength = null, mixed $driverOptions = null): bool
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$param` | **mixed** |  |
| `$var` | **mixed** |  |
| `$type` | **mixed** |  |
| `$maxLength` | **mixed** |  |
| `$driverOptions` | **mixed** |  |





***

### bindColumn



```php
public bindColumn(mixed $column, mixed& $var, mixed $type = null, mixed $maxLength = null, mixed $driverOptions = null): bool
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **mixed** |  |
| `$var` | **mixed** |  |
| `$type` | **mixed** |  |
| `$maxLength` | **mixed** |  |
| `$driverOptions` | **mixed** |  |





***

### bindValue



```php
public bindValue(mixed $param, mixed $value, mixed $type = PDO::PARAM_STR): bool
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$param` | **mixed** |  |
| `$value` | **mixed** |  |
| `$type` | **mixed** |  |





***

### rowCount



```php
public rowCount(): int
```












***

### fetchColumn



```php
public fetchColumn(mixed $column): mixed
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$column` | **mixed** |  |





***

### fetchAll



```php
public fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed $args): array&lt;int|string,mixed&gt;
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$mode` | **int** |  |
| `$args` | **mixed** |  |




**Throws:**

- [`PseudoException`](./Exceptions/PseudoException.md)



***

### processFetchedRow



```php
private processFetchedRow(array&lt;int|string,mixed&gt; $row, int|null $fetchMode): mixed
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$row` | **array<int&#124;string,mixed>** |  |
| `$fetchMode` | **int&#124;null** |  |





***

### fetchObject



```php
public fetchObject(class-string|null $class = &quot;stdClass&quot;, array&lt;int|string,mixed&gt; $constructorArgs = []): object|false
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$class` | **class-string&#124;null** |  |
| `$constructorArgs` | **array<int&#124;string,mixed>** |  |




**Throws:**

- [`ReflectionException`](../ReflectionException.md)



***

### errorCode



```php
public errorCode(): string|null
```












***

### errorInfo



```php
public errorInfo(): string[]
```












***

### columnCount



```php
public columnCount(): int
```











**Throws:**

- [`PseudoException`](./Exceptions/PseudoException.md)



***

### setFetchMode



```php
public setFetchMode(int $mode, null $className = null, mixed $params): bool|int
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$mode` | **int** |  |
| `$className` | **null** |  |
| `$params` | **mixed** |  |





***

### getBoundParams



```php
public getBoundParams(): array&lt;int|string,mixed&gt;
```












***

### getIterator



```php
public getIterator(): \Iterator
```











**Throws:**

- [`PseudoException`](./Exceptions/PseudoException.md)



***


***
> Automatically generated on 2024-10-12
