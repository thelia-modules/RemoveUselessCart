# Remove Useless Cart

Add two ways to remove carts uselessly filling database : from the administration panel or from command line.

## Installation

### Manually

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is RemoveUselessCart.
* Activate it in your thelia administration panel

### Composer

Add it in your main thelia composer.json file

```
composer require thelia/remove-useless-cart-module:~0.1
```

## Usage

**Important:** be aware that this operation may take a while with huge databases. Do it in many times or wait for success or error message patiently.

### Administration panel

- Click 'Configure' in front of the module name
- Select a date from which you want carts to be removed
- Check if you want to remove all carts, even those with products linked
- Click OK

### CLI

The module adds a new Thelia command :

```
php Thelia carts:remove [arguments] [options]
```

#### Arguments

```date``` optional (required if --day option isn't set) | *yyyy-mm-dd* formated date. Date from which you want to remove carts.

```time``` optional | *hh:mm:ss* formated time. Use it after ```date```, separated by a space, for your date to be more specific.

#### Options

```--day=, -d``` value required - instead of giving a date, use this option to tell from how many days ago carts have to be removed.

```--all, -a``` use this option if you want to remove all carts, even those with products linked.
