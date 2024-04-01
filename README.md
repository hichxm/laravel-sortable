# Laravel Sortable

Laravel Sortable is a trait that adds sortable behaviour to an Eloquent model.

## Installation

You can install the package via composer:

```bash
composer require hichxm/laravel-sortable
```

## Usage

Create a new migration to add a new column to your table:

```php
Schema::table('addresses', function (Blueprint $table) {
    $table->orderColumn('order');
    
    // $table->dropOrderColumn('order'); // To drop the order column
});
```

Add the `Hichxm\LaravelSortable\HasSortableColumn` trait to your model:

```php
use Hichxm\LaravelSortable\HasSortableColumn;

class Address extends Model
{
    use HasSortableColumn;
}
```

By default, the trait will look for a column named `order`, 
but you can customize this by setting the `$sortableColumn` property on your model:

```php
class Address extends Model
{
    use HasSortableColumn;
    
    protected $sortableColumn = 'custom_order';
}
```

You can now use the following methods on your model to manage order:

```php
$addressOne = Address::find(1);
$addressTwo = Address::find(2);
$addressThree = Address::find(3);

Address::swapOrder($addressOne, $addressTwo); // Swap the order of the two addresses
Address::setOrder($addressOne, 2); // Set the order of the address
Address::setNewOrder([$addressOne, $addressTwo, $addressThree]); // Set the order of the addresses
```

You can also use the following methods to get query ordered results:

```php
$addressQuery = Address::query();

$addressQuery->ordered()->get(); // Get the addresses ordered

$addressQuery->ordered('desc')->get(); // Get the addresses ordered in descending order
$addressQuery->orderedDesc()->get(); // Get the addresses ordered in ascending order

$addressQuery->ordered('asc')->get(); // Get the addresses ordered in ascending order
$addressQuery->orderedAsc()->get(); // Get the addresses ordered in ascending order
```


## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.