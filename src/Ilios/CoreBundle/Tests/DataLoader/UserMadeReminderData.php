<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class UserMadeReminderData extends AbstractDataLoader
{
    protected function getData()
    {
        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        $arr = array();
        $arr[] = array(
            'id' => 1,
            'note' => $this->faker->text(149),
            'dueDate' => $dt->format('c'),
            'closed' => false,
            'user' => '2'
        );
        $arr[] = array(
            'id' => 2,
            'note' => $this->faker->text(149),
            'dueDate' => $dt->format('c'),
            'closed' => false,
            'user' => '2'
        );
        return $arr;
    }

    public function create()
    {
        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        return array(
            'id' => 3,
            'note' => $this->faker->text(149),
            'dueDate' => $dt->format('c'),
            'closed' => false,
            'user' => '2'
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
