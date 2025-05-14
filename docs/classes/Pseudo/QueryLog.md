***

# QueryLog





* Full name: `\Pseudo\QueryLog`
* This class implements:
[`\IteratorAggregate`](../IteratorAggregate.md), [`\ArrayAccess`](../ArrayAccess.md), [`\Countable`](../Countable.md)



## Properties


### queries



```php
private array&lt;int|string,\Pseudo\ParsedQuery&gt; $queries
```






***

## Methods


### count



```php
public count(): int
```












***

### getIterator



```php
public getIterator(): \Traversable
```












***

### offsetExists



```php
public offsetExists(mixed $offset): bool
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **mixed** |  |





***

### offsetGet



```php
public offsetGet(mixed $offset): \Pseudo\ParsedQuery
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **mixed** |  |





***

### offsetSet



```php
public offsetSet(mixed $offset, mixed $value): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **mixed** |  |
| `$value` | **mixed** |  |





***

### offsetUnset



```php
public offsetUnset(mixed $offset): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$offset` | **mixed** |  |





***

### addQuery



```php
public addQuery(string $sql): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$sql` | **string** |  |





***

### getQueries



```php
public getQueries(): array&lt;int|string,\Pseudo\ParsedQuery&gt;
```












***


***
> Automatically generated on 2025-05-14
