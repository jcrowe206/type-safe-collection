TypeSafe Collections
===================

[![Build Status](https://travis-ci.org/jcrowe206/type-safe-collection.svg?branch=master)](https://travis-ci.org/jcrowe206/type-safe-collection)


TypeSafeCollection is a thin wrapper around [Illuminate's Collection object](https://github.com/illuminate/support/blob/master/Collection.php) that allows for easy enforcement of type protection in your collections.  
                               
```php

use JCrowe\TypeSafeCollection\TypeSafeCollection;

class MovieLibrary extends TypeSafeCollection {

    // list of classes that can be added to the collection
    protected $allowedClasses = [Watchable::class, Rentable::class];
}

$myLibrary = new MovieLibrary([
    new WatchableMovie(),
    new RentableDVD(),
    new ReadableBook() // throws \InvalidArgumentProvided exception
]);


$myLibrary = new MovieLibrary();

$myLibarry->push(new RentableDVD());

$myLibrary->push(new ReadableBook()); // throws \InvalidArgumentProvided exception 

```

#### Custom checks

```php

class MovieLibrary extends TypeSafeCollection {

    // list of classes that can be added to the collection
    protected $allowedClasses = [Watchable::class, Rentable::class];
    
    
    // this function will be called whenever a new  
    // element is being added to the collection
    protected function onAddNewElement($element) 
    {
        if (!$element->isAvailable()) {
            
            return false; // or throw exception
        }
    }
}

```

## Installation

```
composer require jcrowe\type-safe-collection
```

```json
{
    "require": {
        "jcrowe/type-safe-collection": "~1.0"
    }
}
```
