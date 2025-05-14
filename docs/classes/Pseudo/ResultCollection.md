***

# ResultCollection





* Full name: `\Pseudo\ResultCollection`
* This class implements:
[`\Countable`](../Countable.md)



## Properties


### queries



```php
private array&lt;string,mixed&gt; $queries
```






***

## Methods


### count



```php
public count(): int
```












***

### addQuery



```php
public addQuery(string $sql, array&lt;int|string,mixed&gt;|null $params = null, mixed|null $results = null): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$sql` | **string** |  |
| `$params` | **array<int&#124;string,mixed>&#124;null** |  |
| `$results` | **mixed&#124;null** |  |





***

### exists



```php
public exists(string $sql): bool
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$sql` | **string** |  |





***

### getResult



```php
public getResult(string|\Pseudo\ParsedQuery $query): \Pseudo\Result|bool
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$query` | **string&#124;\Pseudo\ParsedQuery** |  |




**Throws:**

- [`PseudoException`](./Exceptions/PseudoException.md)

- [`Throwable`](../Throwable.md)



***


***
> Automatically generated on 2025-05-14
