<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class ReportData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->title(25),
            'subject' => $this->faker->title(25),
            'user' => '2'
        );
        
        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->title(25),
            'subject' => $this->faker->title(25),
            'prepositionalObject' => $this->faker->title(5),
            'prepositionalObjectTableRowId' => $this->faker->randomDigitNotNull(),
            'user' => '2'
        );

        $arr[] = array(
            'id' => 3,
            'title' => $this->faker->title(25),
            'subject' => $this->faker->title(25),
            'prepositionalObject' => $this->faker->title(5),
            'prepositionalObjectTableRowId' => $this->faker->randomDigitNotNull(),
            'user' => '2',
            'school' => '1',
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 4,
            'title' => $this->faker->title(25),
            'subject' => $this->faker->title(25),
            'prepositionalObject' => $this->faker->title(5),
            'prepositionalObjectTableRowId' => $this->faker->randomDigitNotNull(),
            'user' => '2'
        );

    }

    public function createInvalid()
    {
        return [];
    }
}
