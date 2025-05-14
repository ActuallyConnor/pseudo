# Pseudo

## Introduction

Pseudo is a system for mocking PHP's PDO database connections. When writing unit tests for PHP applications, one
frequently has the need to test code that interacts with a database. However, in the true spirit of a unit test, the
database should be abstracted, as we can assume with some degree of certainty that things like network links to the
database server, the database connection drivers, and the database server and software itself are "going to work", and
they are outside the scope of our unit tests.

Enter Pseudo. Pseudo allows you to have "fake" interactions with a database that produce predefined results every time.
This has 2 main advantages over actually interacting with a database. First, it saves having to write data fixtures in
another format, ensuring the data schema availability, loading the fixtures in, and then later cleaing and resetting
them between tests. Second, and somewhat as a result of the first, tests can run *significantly* faster because they are
essentially talking to an in-memory object structure rather than incurring all the overhead of connecting and
interacting with an actual database.

The general idea is that Pseudo implements all the classes in the PDO system by inheriting from them and then
overriding their methods. During your test, at the point where you would inject a PDO object into your data layer, you
can now inject a Pseudo\Pdo object transparently, giving yourself 100% flexibility to control what your application now
*thinks* is the database. In your unit test, you can express the mocks for your test in terms of SQL statements and
arrays of result data.

Find the package on [packagist.org](https://packagist.org/packages/pseudo/pseudo)

## Documentation

https://pseudo-pdo.org

### Documentation Generation

This project uses [readthedocs](https://readthedocs.io) and [mkdocs](https://mkdocs.org)

To load the local documentation page setup mkdocs locally and run:
```shell
$ mkdocs serve
```

You can edit the `mkdocs.yaml` file to change the pages that show up in the sidebar.

To generate the docs, setup [phpDocumentor](https://phpdoc.org/) locally and then run:

```shell
$ phpdoc --directory=src --target=docs --template="vendor/saggre/phpdocumentor-markdown/themes/markdown"
```

## Installation

```
composer require --dev pseudo/pseudo
```

### Usage

#### Something you may want to test

```php
<?php
class ObjectsModel {
    public function __construct(private readonly PDO $pdo)
    {
    }
    
    public function getObjectsByFoo(string $foo): array
    {
        $statement = $this->pdo->prepare('SELECT id FROM objects WHERE foo = :foo');

        $statement->execute(['foo' => 'bar']);
        
        $objects = $statement->fetchAll();
        
        if (!$objects) {
            throw new RuntimeException('Entity not found');
        }
        
        return $objects;
    }
}
```

#### Tests with Pseudo

```php
<?php

class ObjectsModelTest extends \PHPUnit\Framework\TestCase {
    public function testGetObjectsByFoo(): void {
        $pdo = new Pseudo\Pdo();

        $objectsModel = new \ObjectsModel($pdo);

        $pdo->mock(
          "SELECT id FROM objects WHERE foo = :foo'", 
          ['foo' => 'bar'],
          [['id' => 1, 'foo' => 'bar']]
        );

        $objects = $objectsModel->getObjectsByFoo('bar');
        
        $this->assertEquals([['id' => 1, 'foo' => 'bar']], $objects);
    }
}
```

### Supported features

The internal storage of mocks and results are associative arrays. Pseudo attempts to implement as much of the standard
PDO feature set as possible, so varies different fetch modes, bindings, parameterized queries, etc. all work as you'd
expect them to.

### Not implemented / wish-list items

* The transaction api is implemented to the point of managing current transaction state, but transactions have no actual
  effect
* Anything related to scrolling cursors has not been implemented, and this includes the fetch modes that might require
  them
* Pseudo isn't strict-mode compatible, which means tests might fail due to unexpected errors with signatures and
  offsets, etc. (I'd happily accept a pull request to fix this!)

## Tests

Pseudo has a fairly robust test suite written with PHPUnit. If you'd like to run the tests, simply run
`./vendor/bin/phpunit` in the root folder. The tests have no external library dependencies (other than phpunit) and
should require no additional setup or bootstrapping to run.

## Requirements

Pseudo internals currently target PHP 8.1 and above. It has no external dependencies aside from the PDO extension,
which seems rather obvious.

Pseudo is built and tested with error reporting set to ```E_ALL & ~(E_NOTICE | E_DEPRECATED | E_STRICT)```. If you are
running in a stricter error reporting mode, your tests will most likely fail due to strict mode method signature
violations. (This is on the known issues / to do list)

## Contributing

We are committed to a transparent development process and highly appreciate any contributions. Whether you are helping
us fix bugs, proposing new features, improving our documentation or spreading the word - we would love to have you as a
part of the community. Please refer to our contribution guidelines and code of conduct.

- Bug Report: If you see an error message or encounter an issue while using Pseudo, please create a bug report.

- Feature Request: If you have an idea or if there is a capability that is missing and would make development easier and
  more robust, please submit a feature request.

- Documentation Request: If you're reading the Pseudo docs and feel like you're missing something, please submit a
  documentation request.

## License

The Pseudo is open-sourced software licensed under the MIT license.
