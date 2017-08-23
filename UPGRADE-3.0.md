UPGRADE FROM 2.x to 3.0
=======================

## Interfaces

The `Pager/PagerInterface` implements `\Iterator` and `\Countable`.
All public methods of `Pager\BasePager` were moved to `Pager/PagerInterface`.

## Type hinting and API closing

Now that only PHP 7.1 is supported, many signatures have changed: type hinting was
added for the parameters or the return value. Also all classes were made final to 
allow slightly modification in upcoming minor releases.

