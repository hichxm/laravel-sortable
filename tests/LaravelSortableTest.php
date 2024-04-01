<?php

namespace Hichxm\LaravelSortable\Test;

use Hichxm\LaravelSortable\Test\Models\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;

class LaravelSortableTest extends TestCase
{
    public function test_it_sets_the_order_column_on_creation()
    {
        $this->createDummiesPersons(10);

        foreach (Person::all() as $index => $person) {
            /** @var Person $person */

            $this->assertEquals($person->order, $index + 1);
        }
    }

    public function test_set_new_order_method_working_with_ids()
    {
        $persons = [
            $this->createDummyPerson(['name' => 'Person 1']),
            $this->createDummyPerson(['name' => 'Person 2']),
            $this->createDummyPerson(['name' => 'Person 3']),
        ];

        Person::setNewOrder([3, 1, 2]);

        $this->assertEquals(2, $persons[0]->refresh()->order);
        $this->assertEquals(3, $persons[1]->refresh()->order);
        $this->assertEquals(1, $persons[2]->refresh()->order);
    }

    public function test_set_new_order_method_working_with_models()
    {
        $persons = [
            $first = $this->createDummyPerson(['name' => 'Person 1']),
            $second = $this->createDummyPerson(['name' => 'Person 2']),
            $third = $this->createDummyPerson(['name' => 'Person 3']),
        ];

        Person::setNewOrder([$third, $first, $second]);

        $this->assertEquals(2, $persons[0]->refresh()->order);
        $this->assertEquals(3, $persons[1]->refresh()->order);
        $this->assertEquals(1, $persons[2]->refresh()->order);
    }

    public function test_set_new_order_method_working_with_callback()
    {
        $persons = [
            $first = $this->createDummyPerson(['name' => 'Person 1']),
            $second = $this->createDummyPerson(['name' => 'Person 2']),
            $third = $this->createDummyPerson(['name' => 'Person 3']),
            $this->createDummyPerson(['name' => 'not a person']),
        ];

        Person::setNewOrder([$third, $first, $second], 10, function (Builder $builder) {
            $builder->where('name', 'LIKE', 'Person%');
        });

        $this->assertEquals(11, $first->refresh()->order);
        $this->assertEquals(12, $second->refresh()->order);
        $this->assertEquals(10, $third->refresh()->order);
        $this->assertEquals(4, $persons[3]->refresh()->order);
    }

    public function test_set_order_method()
    {
        $person = $this->createDummyPerson();

        $person->setOrder(10);

        $this->assertEquals(10, $person->refresh()->order);
    }

    public function test_ordered_scope()
    {
        $this->createDummiesPersons(10);

        $this->assertEquals(1, Person::query()->ordered('asc')->first()->order);
        $this->assertEquals(10, Person::query()->ordered('desc')->first()->order);
    }

    public function test_drop_order_column()
    {
        $this->createDummiesPersons();

        $this->assertDatabaseHas('persons', ['order' => 1]);

        $this->app['db']->connection()->getSchemaBuilder()->table('persons', function (Blueprint $table) {
            $table->dropOrderColumn('order');
        });

        $this->assertDatabaseMissing('persons', ['order' => 1]);
        $this->assertDatabaseCount('persons', 10);
    }

    public function test_swap_order_method()
    {
        $persons = [
            $first = $this->createDummyPerson(['name' => 'Person 1']),
            $second = $this->createDummyPerson(['name' => 'Person 2']),
            $third = $this->createDummyPerson(['name' => 'Person 3']),
        ];

        Person::swapOrder($first, $third);

        $this->assertEquals(3, $first->refresh()->order);
        $this->assertEquals(2, $second->refresh()->order);
        $this->assertEquals(1, $third->refresh()->order);
    }

}