***

# Result





* Full name: `\Pseudo\Result`



## Properties


### rows



```php
private array&lt;int|string,array&lt;int|string,mixed&gt;&gt; $rows
```






***

### executionResult



```php
private ?bool $executionResult
```






***

### isParameterized



```php
private bool $isParameterized
```






***

### errorCode



```php
private string $errorCode
```






***

### errorInfo



```php
private string $errorInfo
```






***

### affectedRowCount



```php
private int $affectedRowCount
```






***

### insertId



```php
private int $insertId
```






***

### rowOffset



```php
private int $rowOffset
```






***

### params



```php
private array&lt;int|string,mixed&gt; $params
```






***

## Methods


### __construct



```php
public __construct(array&lt;int|string,array&lt;int|string,mixed&gt;&gt;|null $rows = null, array&lt;int|string,mixed&gt;|null $params = null, bool|null $executionResult = null): mixed
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$rows` | **array<int&#124;string,array<int&#124;string,mixed>>&#124;null** |  |
| `$params` | **array<int&#124;string,mixed>&#124;null** |  |
| `$executionResult` | **bool&#124;null** |  |





***

### addRow



```php
public addRow(array&lt;int|string,mixed&gt; $row, array&lt;int|string,mixed&gt; $params = null): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$row` | **array<int&#124;string,mixed>** |  |
| `$params` | **array<int&#124;string,mixed>** |  |




**Throws:**

- [`PseudoException`](./Exceptions/PseudoException.md)



***

### setParams



```php
public setParams(array&lt;int|string,mixed&gt; $params, bool $parameterize = false): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$params` | **array<int&#124;string,mixed>** |  |
| `$parameterize` | **bool** |  |





***

### getRows



```php
public getRows(array&lt;int|string,mixed&gt; $params = []): array&lt;int|string,array&lt;int|string,mixed&gt;&gt;|array&lt;int|string,mixed&gt;
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$params` | **array<int&#124;string,mixed>** |  |




**Throws:**

- [`PseudoException`](./Exceptions/PseudoException.md)



***

### getRowIfExists

Returns the next row if it exists, otherwise returns false

```php
private getRowIfExists(array&lt;int|string,mixed&gt; $rows): false|array&lt;int|string,mixed&gt;
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$rows` | **array<int&#124;string,mixed>** | Rows to get row from |


**Return Value:**

Next row (false if it doesn't exist)




***

### nextRow

Returns the next available row if it exists, otherwise returns false

```php
public nextRow(): false|array&lt;int|string,mixed&gt;
```












***

### setInsertId



```php
public setInsertId(int $insertId): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$insertId` | **int** |  |





***

### getInsertId



```php
public getInsertId(): int
```












***

### setErrorCode



```php
public setErrorCode(string $errorCode): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$errorCode` | **string** |  |




**Throws:**

- [`PseudoException`](./Exceptions/PseudoException.md)



***

### getErrorCode



```php
public getErrorCode(): string
```












***

### setErrorInfo



```php
public setErrorInfo(string $errorInfo): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$errorInfo` | **string** |  |





***

### getErrorInfo



```php
public getErrorInfo(): string
```












***

### setAffectedRowCount



```php
public setAffectedRowCount(int $affectedRowCount): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$affectedRowCount` | **int** |  |





***

### getAffectedRowCount



```php
public getAffectedRowCount(): int
```












***

### isOrdinalArray



```php
public isOrdinalArray(array&lt;int|string,mixed&gt; $arr): bool
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$arr` | **array<int&#124;string,mixed>** |  |





***

### reset



```php
public reset(): void
```












***

### hasExecutionResult



```php
public hasExecutionResult(): bool
```












***

### getExecutionResult



```php
public getExecutionResult(): bool
```











**Throws:**

- [`LogicException`](./Exceptions/LogicException.md)



***

### stringifyParameterSet



```php
private stringifyParameterSet(array&lt;int|string,mixed&gt; $params): string
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$params` | **array<int&#124;string,mixed>** |  |





***

### initializeParameterizedRows



```php
private initializeParameterizedRows(string $parameterKey, array&lt;int|string,mixed&gt; $row): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$parameterKey` | **string** |  |
| `$row` | **array<int&#124;string,mixed>** |  |





***

### addNonParameterizedRow



```php
private addNonParameterizedRow(array&lt;int|string,mixed&gt; $row): void
```








**Parameters:**

| Parameter | Type | Description |
|-----------|------|-------------|
| `$row` | **array<int&#124;string,mixed>** |  |




**Throws:**

- [`PseudoException`](./Exceptions/PseudoException.md)



***


***
> Automatically generated on 2025-05-14
