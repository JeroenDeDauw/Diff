# Diff

Diff is a small PHP library with value objects to represent diffs and service objects to do
various types of operations. These include creating a diff between two data structures,
applying a diff onto a data structure and merging multiple diffs into one.

[![Build Status](https://secure.travis-ci.org/wikimedia/mediawiki-extensions-Diff.png?branch=master)](http://travis-ci.org/wikimedia/mediawiki-extensions-Diff)
[![Coverage Status](https://coveralls.io/repos/wikimedia/mediawiki-extensions-Diff/badge.png?branch=master)](https://coveralls.io/r/wikimedia/mediawiki-extensions-Diff?branch=master)
[![Dependency Status](https://www.versioneye.com/package/php--diff--diff/badge.png)](https://www.versioneye.com/package/php--diff--diff)

[![Latest Stable Version](https://poser.pugx.org/diff/diff/version.png)](https://packagist.org/packages/diff/diff)
[![Download count](https://poser.pugx.org/diff/diff/d/total.png)](https://packagist.org/packages/diff/diff)

## Requirements

* PHP 5.3 or later

## Installation

You can use [Composer](http://getcomposer.org/) to download and install
this package as well as its dependencies. Alternatively you can simply clone
the git repository and take care of loading yourself.

### Composer

To add this package as a local, per-project dependency to your project, simply add a
dependency on `diff/diff` to your project's `composer.json` file.
Here is a minimal example of a `composer.json` file that just defines a dependency on
Diff 1.0:

    {
        "require": {
            "diff/diff": "1.0.*"
        }
    }

### Manual

Get the Diff code, either via git, or some other means. Also get all dependencies.
You can find a list of the dependencies in the "require" section of the composer.json file.
Load all dependencies and the load the Diff library by including its entry point:
Diff.php.

## Usage

The Diff library can be subdivided into 3 components.

### DiffOp

First and foremost are the objects to represent diffs. Diffs are represented by the Diff class, which
is an aggregate for diff operations, think "add" and "remove", that all extend from DiffOp. Diff
itself is a DiffOp, since the library supports recursion, and thus has the need to be able to
represent nested diffs.

The available DiffOps are:

* DiffOpAdd - addition of a value (newValue)
* DiffOpChange - modification of a value (oldValue, newValue)
* DiffOpRemove - removal of a value (oldValue)
* Diff - a collection of diff operations

These can all be found in <code>includes/diffop</code>.

The Diff class can be set to be either associative or non-assocative. In case of the later, only
DiffOpAdd and DiffOpRemove are allowed in it.

### Differ

Often you need to construct a diff between two arrays, rather then creating it manually. To do this,
the Diff library includes a few classes implementing the Differ interface, which contains one very
simple method.

```php
/**
 * Takes two arrays, computes the diff, and returns this diff as an array of DiffOp.
 *
 * @since 0.1
 *
 * @param array $oldValues The first array
 * @param array $newValues The second array
 *
 * @throws Exception
 * @return DiffOp[]
 */
public function doDiff( array $oldValues, array $newValues );
```

The Differs that come with the library are:

* ListDiffer: Differ that only looks at the values of the arrays (and thus ignores key differences).
* MapDiffer: Differ that does an associative diff between two arrays, with the option to do this recursively.

Both Differ objects come with a few options that can be used to change their behaviour.

All differ functionality can be found in <code>includes/differ</code>.

### Patcher

The third component that comes with the Diff library is a set of classes implementing the Patcher
interface. This interface contains a single simple method:

```php
/**
 * Applies the applicable operations from the provided diff to
 * the provided base value.
 *
 * @since 0.1
 *
 * @param array $base
 * @param Diff $diffOps
 *
 * @return array
 */
public function patch( array $base, Diff $diffOps );
```

Diff comes with two clases implementing the Patcher interface:

* ListPatcher: Applies non-associative diffs to a base. With default options does the reverse of ListDiffer
* MapPatcher: Applies diff to a base, recursivly if needed. With default options does the reverse of MapDiffer

All classes part of the patcher component can be found in <code>includes/patcher</code>

### ValueComparer

Added in 0.6

TODO

### ArrayComparer

Added in 0.8

TODO

## Examples

#### DiffOp

```php
// Constructing an empty diff
$diff = new Diff();

// Adding a single add-operation to the diff
$diff[] = new DiffOpAdd( 'added value' );

// Adding a single change-operation to the diff for key "awesomeness"
$diff['awesomeness'] = new DiffOpChange( 9000, 9001 );

// Getting an array with the change operations from the Diff
$changeOps = $diff->getChanges();

// Creating a new diff with a set op DiffOps, and specifying that it is an associative diff
$diff = new Diff( $changeOps, true );

// Looping over the diff operations that make up the diff
foreach ( $diff as $diffOp ) {}

// Removing the "awesomeness" operation from the diff
unset( $diff['awesomeness'] );

// Adding a non-associative diff with one add operation to the diff for the "recursion" key
$diff['recursion'] = new Diff( array( DiffOpAdd( 42 ) ), false );

// Counting the number of diff operations that make up the diff
count( $diff );
```

#### Differ

```php
$oldValues = array( 0, 1, 2, 42, 9001, 'foobar' );
$newValues = array( 0, 0, 23, 'foobar', 1, 2 );

$differ = new ListDiffer();

$diffOps = $differ->doDiff( $oldValues, $newValues );

// This is the result
$diffOps = array(
    DiffOpRemove( 42 ),
    DiffOpRemove( 9001 )
    DiffOpAdd( 0 ),
    DiffOpAdd( 23 )
);
```

```php
$oldValues = array( 'a' => 0, 'b' => array( 'c' => 0, 'd' => 1 ) );
$newValues = array( 'a' => 1, 'b' => array( 'c' => 10, 'd' => 1 ), 'e' => 42 );

$differ = new MapDiffer();

$diffOps = $differ->doDiff( $oldValues, $newValues );

// This is the result
$diffOps = array(
    'a' => DiffOpChange( 0, 1 ),
    'b' => Diff( array( 'c' => new DiffOpChange( 0, 10 ) ) ),
    'e' => DiffOpAdd( 42 )
);
```

#### Patcher

```php
$oldValues = array( 0, 1, 2, 42, 9001, 'foobar' );
$diff = new Diff( array(
    'a' => DiffOpChange( 0, 1 ),
    'b' => Diff( array( 'c' => new DiffOpChange( 0, 10 ) ) ),
    'e' => DiffOpAdd( 42 )
) );

$patcher = new ListPatcher();

$newValues = $patcher->patch( $oldValues, $diff );

// This is the result
$diffOps = array( 0, 0, 23, 'foobar', 1, 2 );
```

```php
$oldValues = array( 'a' => 0, 'b' => array( 'c' => 0, 'd' => 1 ) );
$diff = new Diff( array(
    'a' => DiffOpChange( 0, 1 ),
    'b' => Diff( array( 'c' => new DiffOpChange( 0, 10 ) ) ),
    'e' => DiffOpAdd( 42 )
) );

$differ = new MapPatcher();

$newValues = $patcher->patch( $oldValues, $diff );

// This is the result
$newValues = array( 'a' => 1, 'b' => array( 'c' => 10, 'd' => 1 ), 'e' => 42 );
```

## Links

* [Diff on Packagist](https://packagist.org/packages/diff/diff)
* [Diff on Ohloh](https://www.ohloh.net/p/mwdiff)
* [Diff on MediaWiki.org](https://www.mediawiki.org/wiki/Extension:Diff)
* [TravisCI build status](https://travis-ci.org/wikimedia/mediawiki-extensions-Diff)
* [Latest version of the readme file](https://github.com/wikimedia/mediawiki-extensions-Diff/blob/master/README.md)
