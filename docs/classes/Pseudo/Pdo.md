***

# Pdo





* Full name: `\Pseudo\Pdo`
* Parent class: [`PDO`](../PDO.md)



## Properties


### mockedQueries



```php
private \Pseudo\ResultCollection $mockedQueries
```






***

### inTransaction



```php
private bool $inTransaction
```






***

### queryLog



```php
private \Pseudo\QueryLog $queryLog
```






***

## Methods


### __construct



```php
public __construct(\Pseudo\ResultCollection|null $collection = null): mixed
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$collection` | **\Pseudo\ResultCollection&#124;null** |  |





***

### prepare



```php
public prepare(string $query, array&lt;int|string,mixed&gt; $options = []): \Pseudo\PdoStatement
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$query` | **string** |  |
| `$options` | **array<int&#124;string,mixed>** |  |




**Throws:**

- [`PseudoException`](./Exceptions/PseudoException.md)

- [`Throwable`](../Throwable.md)



***

### beginTransaction



```php
public beginTransaction(): bool
```












***

### commit



```php
public commit(): bool
```












***

### rollBack



```php
public rollBack(): bool
```












***

### inTransaction



```php
public inTransaction(): bool
```












***

### exec



```php
public exec(mixed $statement): false|int
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$statement` | **mixed** |  |





***

### query



```php
public query(string $query, int|null $fetchMode = null, mixed $fetchModeArgs): \Pseudo\PdoStatement
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$query` | **string** |  |
| `$fetchMode` | **int&#124;null** |  |
| `$fetchModeArgs` | **mixed** |  |




**Throws:**

- [`\Pseudo\Exceptions\PseudoException|\Throwable`](./Exceptions/PseudoException|/Throwable.md)



***

### lastInsertId



```php
public lastInsertId(string|null $name = null): string|false
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$name` | **string&#124;null** |  |




**Throws:**

- [`PseudoException`](./Exceptions/PseudoException.md)

- [`Throwable`](../Throwable.md)



***

### getLastResult



```php
private getLastResult(): \Pseudo\Result|bool
```











**Throws:**

- [`\Pseudo\Exceptions\PseudoException|\Throwable`](./Exceptions/PseudoException|/Throwable.md)



***

### save



```php
public save(string $filePath): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$filePath` | **string** |  |





***

### load



```php
public load(string $filePath): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$filePath` | **string** |  |




**Throws:**

- [`PseudoException`](./Exceptions/PseudoException.md)



***

### mock



```php
public mock(string $sql, array&lt;int|string,mixed&gt;|null $params = null, mixed $expectedResults = null): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$sql` | **string** |  |
| `$params` | **array<int&#124;string,mixed>&#124;null** |  |
| `$expectedResults` | **mixed** |  |





***

### getMockedQueries



```php
public getMockedQueries(): \Pseudo\ResultCollection
```












***


***
> Automatically generated on 2024-10-12
