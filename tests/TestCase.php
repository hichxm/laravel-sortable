<?php

namespace Hichxm\LaravelSortable\Test;

use Hichxm\LaravelSortable\LaravelSortableServiceProvider;
use Hichxm\LaravelSortable\Test\Models\Person;
use Illuminate\Database\Schema\Blueprint;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{

    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function setUpDatabase(): void
    {
        $this->app['db']->connection()->getSchemaBuilder()->create('persons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->orderColumn('order');
            $table->timestamps();
        });
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelSortableServiceProvider::class,
        ];
    }

    /**
     * Create a dummy person
     *
     * @param array $attributes
     * @return Person
     */
    protected function createDummyPerson(array $attributes = []): Person
    {
        /** @var Person $person */
        $person = Person::query()
            ->create(array_merge([
                'name' => 'John Doe',
            ], $attributes));

        return $person;
    }

    /**
     * @param int $count
     * @return Person[]
     */
    protected function createDummiesPersons(int $count = 10): array
    {
        $persons = [];

        for ($i = 0; $i < $count; $i++) {
            $persons[] = $this->createDummyPerson([
                'name' => 'Person ' . $i,
            ]);
        }

        return $persons;
    }

}