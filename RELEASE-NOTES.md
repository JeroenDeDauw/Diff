# Diff release notes

[![Latest Stable Version](https://poser.pugx.org/diff/diff/version.png)](https://packagist.org/packages/diff/diff)

## Version 0.8 (dev)

#### Additions

* Added Diff\ArrayComparer\ArrayComparer interface
* Added NativeArrayComparer, ArrayComparer adapter for array_diff
* Added StrictArrayComparer, containing the "strict mode" logic from ListDiffer
* Added StrategicArrayComparer, implementation of ArrayComparer that takes a ValueComparer as strategy

#### Improvements

* MapPatcher will now report conflicts for remove operations that specify a value to be removed
different from the value in the structure being patched.

#### Removals

* Removed obsolete tests/phpunit.php test runner

## Version 0.7 (2013-07-16)

#### Improvements

* Added extra tests for MapPatcher and ListPatcher
* Added extra tests for Diff
* Added extra tests for MapDiffer
* Added @covers tags to the unit tests to improve coverage report accuracy

#### Removals

* Removed static methods from ListDiff and MapDiff (all deprecated since 0.4)
* Removed DiffOpTestDummy

#### Bug fixes

* MapPatcher will now no longer stop patching after the first remove operation it encounters
* MapPatcher now always treats its top level input diff as a map diff
* Fixed several issues in ListPatcherTest

## Version 0.6 (2013-05-08)

#### Compatibility changes

* The tests can now run independently of MediaWiki
* The tests now require PHPUnit 3.7 or later

#### Additions

* Added phpunit.php runner in the tests directory
* Added Diff\Comparer\ValueComparer interface with CallbackComparer and StrictComparer implementations
* Added MapPatcher::setValueComparer to facilitate patching maps containing objects
* Added PHPUnit configuration file using the new tests/bootstrap.php

#### Removals

* GenericArrayObject has been removed from this package.
  Diff derives from ArrayObject rather than GenericArrayObject.
  Its interface has not changed expect for the points below.
* The getObjectType method in Diff (previously defined in GenericArrayObject)
  is now private rather than public.
* Adding a non-DiffOp element to a Diff will now result in an InvalidArgumentException
  rather than a MWException.
* Removed Diff\Exception

## Version 0.5 (2013-02-26)

#### Additions

* Added DiffOpFactory
* Added DiffOp::toArray
* Added CallbackListDiffer
* Added MapDiffer::setComparisonCallback

#### Deprecations

* Hard deprecated ListDiff, MapDiff and IDiff

#### Removals

* Removed Diff::getApplicableDiff

## Version 0.4 (2013-01-08)

#### Additions

* Split off diffing code from MapDiff and ListDiff to dedicated Differ classes
* Added dedicated Patcher classes, which are used for the getApplicableDiff functionality

#### Deprecations

* Deprecated ListDiff:newFromArrays and MapDiff::newFromArrays
* Deprecated ListDiff::newEmpty and MapDiff::newEmpty
* Deprecated Diff::getApplicableDiff
* Soft deprecated DiffOp interface in favour of DiffOp
* Soft deprecated IDiff interface in favour of Diff
* Soft deprecated MapDiff and ListDiff in favour of Diff

#### Removals

* Removed parentKey functionality from Diff
* Removed constructor from Diff interface
* Removed Diff::newEmpty

## Version 0.3 (2012-11-21)

* Improved entry point and setup code. Diff.php is now the main entry point for both MW extension and standalone library
* ListDiffs with only add operations can now be applied on top of bases that do not have their key
* Added Diff::removeEmptyOperations
* Improved type hinting
* Improved test coverage
    * Added constructor tests for MapDiff and ListDiff
    * Added extra tests for Diff and MapDiff
    * Test coverage is now 100%
* Removed static method from Diff interface

## Version 0.2 (2012-11-01)

* Fixed tests to work with PHP 5.4 and above
* Added translations
* Added some profiling calls

## Version 0.1 (2012-9-25)

Initial release with these features:

* Classes to represent diffs or collections of diff operations: Diff, MapDiff, ListDiff
* Classes to represent diff operations: Diff, MapDiff, ListDiff, DiffOpAdd, DiffOpChange, DiffOpRemove
* Methods to compute list and maps diffs
* Support for recursive diffs of arbitrary depth
* Works as MediaWiki extension or as standalone library
