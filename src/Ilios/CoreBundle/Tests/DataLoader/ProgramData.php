<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class ProgramData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->title(15),
            'shortTitle' => $this->faker->title(5),
            'duration' => 3,
            'publishedAsTbd' => false,
            'published' => true,
            'school' => "1",
            'programYears' => ["1", "2"],
            'curriculumInventoryReports' => ["1", "2", "3"]
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->title(15),
            'shortTitle' => $this->faker->title(5),
            'duration' => 4,
            'publishedAsTbd' => true,
            'published' => false,
            'school' => "1",
            'programYears' => ["3"],
            'curriculumInventoryReports' => []
        );

        $arr[] = array(
            'id' => 3,
            'title' => $this->faker->title(15),
            'shortTitle' => $this->faker->title(5),
            'duration' => 4,
            'publishedAsTbd' => false,
            'published' => false,
            'school' => "2",
            'programYears' => ["4"],
            'curriculumInventoryReports' => []
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 4,
            'title' => $this->faker->title(15),
            'shortTitle' => $this->faker->title(5),
            'duration' => 4,
            'publishedAsTbd' => true,
            'published' => true,
            'school' => "1",
            'programYears' => [],
            'curriculumInventoryReports' => []
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
