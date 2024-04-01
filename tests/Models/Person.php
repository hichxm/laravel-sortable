<?php

namespace Hichxm\LaravelSortable\Test\Models;

use Hichxm\LaravelSortable\Traits\HasSortableColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Person
 *
 * @property int $id
 * @property string $name
 * @property int $order
 *
 */
class Person extends Model
{
    use HasSortableColumn;

    protected $table = 'persons';

    protected $fillable = [
        'name',
        'order',
    ];
}