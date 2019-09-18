Historian
=========

*Event Sourcing made simple, and in PHP!*

> NOTE: This library is under active development! Do not use.

## Installation

## Concepts

### Aggregate Root
Aggregate Roots have proper definitions inside DDD that are much more related
to object oriented programming. In Historian, however, aggregate roots
are mere **state** containers that are uniquely identified and versioned.
For this reason, aggregates in Historian have just 5 relevant props: `id`,
`version`, `createdAt`, `lastUpdated` and `state`. This last property
is the most important one, because is the one that holds all the data in the
aggregate, inside an array.

This does not mean, however, that you cannot expose object oriented
constructs to client classes consuming the data of or interacting with
your aggregate root. Since in DDD everything inside an aggregate is
a value object, you can instantiate them easily.

## Usage