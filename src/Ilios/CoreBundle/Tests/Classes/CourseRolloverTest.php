<?php
namespace Ilios\CliBundle\Tests\Command;

use Ilios\CliBundle\Command\RolloverCourseCommand;
use Ilios\CoreBundle\Classes\CourseRollover;
use Ilios\CoreBundle\Entity\Course;
use Ilios\CoreBundle\Entity\CourseClerkshipType;
use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\CourseLearningMaterial;
use Ilios\CoreBundle\Entity\InstructorGroup;
use Ilios\CoreBundle\Entity\LearningMaterial;
use Ilios\CoreBundle\Entity\MeshDescriptor;
use Ilios\CoreBundle\Entity\Objective;
use Ilios\CoreBundle\Entity\Offering;
use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\Session;
use Ilios\CoreBundle\Entity\SessionLearningMaterial;
use Ilios\CoreBundle\Entity\SessionType;
use Ilios\CoreBundle\Entity\Term;
use Ilios\CoreBundle\Entity\User;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;
use \DateTime;

/**
 * Class CourseRolloverTest
 * @package Ilios\CliBundle\Tests\Command
 */
class CourseRolloverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var m\MockInterface
     */
    protected $courseManager;

    /**
     * @var m\MockInterface
     */
    protected $learningMaterialManager;

    /**
     * @var m\MockInterface
     */
    protected $courseLearningMaterialManager;

    /**
     * @var m\MockInterface
     */
    protected $sessionManager;

    /**
     * @var m\MockInterface
     */
    protected $sessionLearningMaterialManager;

    /**
     * @var m\MockInterface
     */
    protected $offeringManager;

    /**
     * @var m\MockInterface
     */
    protected $objectiveManager;

    /**
     * @var CourseRollover
     */
    protected $service;


    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->courseManager = m::mock('Ilios\CoreBundle\Entity\Manager\ManagerInterface');
        $this->learningMaterialManager = m::mock('Ilios\CoreBundle\Entity\Manager\ManagerInterface');
        $this->courseLearningMaterialManager = m::mock('Ilios\CoreBundle\Entity\Manager\ManagerInterface');
        $this->sessionManager = m::mock('Ilios\CoreBundle\Entity\Manager\ManagerInterface');
        $this->sessionLearningMaterialManager = m::mock('Ilios\CoreBundle\Entity\Manager\ManagerInterface');
        $this->offeringManager = m::mock('Ilios\CoreBundle\Entity\Manager\ManagerInterface');
        $this->objectiveManager = m::mock('Ilios\CoreBundle\Entity\Manager\ManagerInterface');
        $this->service = new CourseRollover(
            $this->courseManager,
            $this->learningMaterialManager,
            $this->courseLearningMaterialManager,
            $this->sessionManager,
            $this->sessionLearningMaterialManager,
            $this->offeringManager,
            $this->objectiveManager
        );
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        unset($this->courseManager);
        unset($this->learningMaterialManager);
        unset($this->courseLearningMaterialManager);
        unset($this->sessionManager);
        unset($this->sessionLearningMaterialManager);
        unset($this->offeringManager);
        unset($this->objectiveManager);
        unset($this->service);
        m::close();
    }

    public function testRolloverWithEverything()
    {
        $course = $this->createTestCourseWithAssications();
        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newYear = $this->setupCourseManager($course, $newCourse);

        $newCourse->shouldReceive('setTitle')->with($course->getTitle())->once();
        $newCourse->shouldReceive('setYear')->with($newYear)->once();
        $newCourse->shouldReceive('setLevel')->with($course->getLevel())->once();
        $newCourse->shouldReceive('setExternalId')->with($course->getExternalId())->once();

        //@todo better comparison of startDate and newStartDate
        $newCourse->shouldReceive('setStartDate')->with(m::on(function (DateTime $newStartDate) use ($course) {
            return $newStartDate > $course->getStartDate();
        }))->once();

        //@todo better comparison of endDate and newEndDate
        $newCourse->shouldReceive('setEndDate')->with(m::on(function (DateTime $newEndDate) use ($course) {
            return $newEndDate > $course->getEndDate();
        }))->once();
        $newCourse->shouldReceive('setClerkshipType')->with($course->getClerkshipType())->once();
        $newCourse->shouldReceive('setSchool')->with($course->getSchool())->once();
        $newCourse->shouldReceive('setDirectors')->with($course->getDirectors())->once();
        $newCourse->shouldReceive('setTerms')->with($course->getTerms())->once();
        $newCourse->shouldReceive('setMeshDescriptors')->with($course->getMeshDescriptors())->once();

        foreach ($course->getObjectives() as $objective) {
            $newObjective = m::mock('Ilios\CoreBundle\Entity\Objective');
            $newObjective->shouldReceive('setTitle')->with($objective->getTitle())->once();
            $newObjective->shouldReceive('addCourse')->with($newCourse)->once();
            $newObjective->shouldReceive('setMeshDescriptors')->with($objective->getMeshDescriptors())->once();
            $this->objectiveManager
                ->shouldReceive('create')->once()
                ->andReturn($newObjective);
            $this->objectiveManager->shouldReceive('update')->once()->withArgs([$newObjective, false, false]);
        }

        foreach ($course->getLearningMaterials() as $learningMaterial) {
            $newLearningMaterial = m::mock('Ilios\CoreBundle\Entity\CourseLearningMaterial');
            $newLearningMaterial->shouldReceive('setLearningMaterial')
                ->with($learningMaterial->getLearningMaterial())->once();
            $newLearningMaterial->shouldReceive('setCourse')->with($newCourse)->once();
            $newLearningMaterial->shouldReceive('setNotes')->with($learningMaterial->getNotes())->once();
            $newLearningMaterial->shouldReceive('setPublicNotes')->with($learningMaterial->hasPublicNotes())->once();
            $newLearningMaterial->shouldReceive('setRequired')->with($learningMaterial->isRequired())->once();
            $newLearningMaterial->shouldReceive('setMeshDescriptors')
                ->with($learningMaterial->getMeshDescriptors())->once();
            $this->courseLearningMaterialManager
                ->shouldReceive('create')->once()
                ->andReturn($newLearningMaterial);
            $this->courseLearningMaterialManager->shouldReceive('update')->once()
                ->withArgs([$newLearningMaterial, false, false]);
        }

        foreach ($course->getSessions() as $session) {
            $newSession = m::mock('Ilios\CoreBundle\Entity\Session');
            $newSession->shouldReceive('setTitle')->with($session->getTitle())->once();
            $newSession->shouldReceive('setCourse')->with($newCourse)->once();
            $newSession->shouldReceive('setAttireRequired')->with($session->isAttireRequired())->once();
            $newSession->shouldReceive('setEquipmentRequired')->with($session->isEquipmentRequired())->once();
            $newSession->shouldReceive('setSessionType')->with($session->getSessionType())->once();
            $newSession->shouldReceive('setSupplemental')->with($session->isSupplemental())->once();
            $newSession->shouldReceive('setPublished')->with(false)->once();
            $newSession->shouldReceive('setPublishedAsTbd')->with(false)->once();
            $newSession->shouldReceive('setMeshDescriptors')->with($session->getMeshDescriptors())->once();
            $newSession->shouldReceive('setTerms')->with($session->getTerms())->once();
            $this->sessionManager
                ->shouldReceive('create')->once()
                ->andReturn($newSession);
            $this->sessionManager->shouldReceive('update')->withArgs([$newSession, false, false])->once();

            foreach ($session->getObjectives() as $objective) {
                $newObjective = m::mock('Ilios\CoreBundle\Entity\Objective');
                $newObjective->shouldReceive('setTitle')->with($objective->getTitle())->once();
                $newObjective->shouldReceive('addSession')->with($newSession)->once();
                $newObjective->shouldReceive('setMeshDescriptors')->with($objective->getMeshDescriptors())->once();
                $newObjective->shouldReceive('setParents')
                    ->with(m::on(function (Collection $collection) use ($objective) {
                        return count($collection) === count($objective->getParents());
                    }));
                $this->objectiveManager
                    ->shouldReceive('create')->once()
                    ->andReturn($newObjective);
                $this->objectiveManager->shouldReceive('update')->withArgs([$newObjective, false, false]);
            }

            foreach ($session->getLearningMaterials() as $learningMaterial) {
                $newLearningMaterial = m::mock('Ilios\CoreBundle\Entity\SessionLearningMaterial');
                $newLearningMaterial->shouldReceive('setLearningMaterial')
                    ->with($learningMaterial->getLearningMaterial())->once();
                $newLearningMaterial->shouldReceive('setSession')->with($newSession)->once();
                $newLearningMaterial->shouldReceive('setNotes')->with($learningMaterial->getNotes())->once();
                $newLearningMaterial->shouldReceive('setPublicNotes')
                    ->with($learningMaterial->hasPublicNotes())->once();
                $newLearningMaterial->shouldReceive('setRequired')->with($learningMaterial->isRequired())->once();
                $newLearningMaterial->shouldReceive('setMeshDescriptors')
                    ->with($learningMaterial->getMeshDescriptors())->once();
                $this->sessionLearningMaterialManager
                    ->shouldReceive('create')->once()
                    ->andReturn($newLearningMaterial);
                $this->sessionLearningMaterialManager->shouldReceive('update')->once()
                    ->withArgs([$newLearningMaterial, false, false]);
            }

            foreach ($session->getOfferings() as $offering) {
                $newOffering = m::mock('Ilios\CoreBundle\Entity\Offering');
                $newOffering->shouldReceive('setRoom')->once()->with($offering->getRoom());
                $newOffering->shouldReceive('setSite')->once()->with($offering->getSite());
                //@todo validate we're getting the right start date
                $newOffering->shouldReceive('setStartDate')->once();
                //@todo validate we're getting the right start date
                $newOffering->shouldReceive('setEndDate')->once();
                $newOffering->shouldReceive('setSession')->once()->with($newSession);
                $newOffering->shouldReceive('setInstructors')->once()->with($offering->getInstructors());
                $newOffering->shouldReceive('setInstructorGroups')->once()->with($offering->getInstructorGroups());
                $newOffering->shouldNotReceive('setLearnerGroups');
                $newOffering->shouldNotReceive('setLearners');

                $this->offeringManager->shouldReceive('create')->once()->andReturn($newOffering);
                $this->offeringManager->shouldReceive('update')->once()->withArgs([$newOffering, false, false]);
            }
        }

        $rhett = $this->service->rolloverCourse($course->getId(), $newYear, ['']);
        $this->assertSame($newCourse, $rhett);
    }

    public function testRolloverWithEmptyClerkshipType()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('setClerkshipType');
        $newYear = $this->setupCourseManager($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['']);
    }

    public function testRolloverWithNewStartDate()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessions()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutCourseLearningMaterials()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $course->addLearningMaterial(new CourseLearningMaterial());
        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('addLearningMaterial');
        $this->courseLearningMaterialManager->shouldNotReceive('create');
        $newYear = $this->setupCourseManager($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['skip-course-learning-materials' => true]);
    }

    public function testRolloverWithoutCourseObjectives()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $course->addObjective(new Objective());
        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('addObjective');
        $this->objectiveManager->shouldNotReceive('create');
        $newYear = $this->setupCourseManager($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['skip-course-objectives' => true]);
    }

    public function testRolloverWithoutCourseTerms()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $course->addTerm(new Term());
        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('setTerms');
        $newYear = $this->setupCourseManager($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['skip-course-terms' => true]);
    }

    public function testRolloverWithoutCourseMesh()
    {
        $course = $this->createTestCourse();
        $course->setSchool(new School());
        $course->addMeshDescriptor(new MeshDescriptor());
        $newCourse = m::mock('Ilios\CoreBundle\Entity\CourseInterface');
        $newCourse->shouldIgnoreMissing();
        $newCourse->shouldNotReceive('setMeshDescriptors');
        $newYear = $this->setupCourseManager($course, $newCourse);

        $this->service->rolloverCourse($course->getId(), $newYear, ['skip-course-mesh' => true]);
    }

    public function testRolloverWithoutSessionLearningMaterials()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessionObjectives()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessionTerms()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutSessionMesh()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutOfferings()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutInstructors()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithoutInstructorGroups()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverWithNewCourseTitle()
    {
        $this->markTestIncomplete();
    }

    // @todo test the hell out of this. use a data provider here. [ST 2016/06/17]
    public function testRolloverOffsetCalculation()
    {
        $this->markTestIncomplete();
    }

    public function testRolloverFailsOnDuplicate()
    {
        $course = $this->createTestCourse();
        $newYear = $course->getYear() + 1;
        $this->courseManager->shouldReceive('findOneBy')->withArgs([['id' => $course->getId()]])->andReturn($course);
        $this->courseManager
            ->shouldReceive('findBy')
            ->withArgs([['title' => $course->getTitle(), 'year' => $newYear]])
            ->andReturn(new Course());

        $this->setExpectedException(
            \Exception::class,
            "Another course with the same title and academic year already exists."
            . " If the year is correct, consider setting a new course title with '--new-course-title' option."
        );

        $this->service->rolloverCourse($course->getId(), $newYear, ['']);

    }

    public function testRolloverFailsOnYearPast()
    {
        $courseId = 10;
        $pastDate = new \DateTime();
        $pastDate->add(\DateInterval::createFromDateString('-2 year'));
        $year = $pastDate->format('Y');

        $this->setExpectedException(
            \Exception::class,
            "You cannot rollover a course to a new year or start date that is already in the past."
        );

        $this->service->rolloverCourse($courseId, $year, []);
    }

    public function testRolloverFailsOnMissingCourse()
    {
        $courseId = -1;
        $futureDate = new \DateTime();
        $futureDate->add(\DateInterval::createFromDateString('+2 year'));
        $year = $futureDate->format('Y');
        $this->courseManager->shouldReceive('findOneBy')->withArgs([['id' => $courseId]])->andReturn(false);

        $this->setExpectedException(\Exception::class, "There are no courses with courseId {$courseId}.");

        $this->service->rolloverCourse($courseId, $year, []);

    }

    public function testRolloverOfCourseObjectives()
    {
        $this->markTestIncomplete();
    }

    /**
     * Gets a basic filled out course
     *
     * @return Course
     */
    protected function createTestCourse()
    {
        $course = new Course();
        $course->setId(10);
        $course->setTitle('test course');
        $course->setLevel(1);
        $course->setYear(2015);
        $course->setStartDate(new DateTime('yesterday'));
        $course->setEndDate(new DateTime('tomorrow'));
        $course->setExternalId('I45');
        $course->setLocked(true);
        $course->setArchived(true);
        $course->setPublished(true);
        $course->setPublishedAsTbd(true);

        return $course;
    }

    /**
     * Gets a course with a bunch of relationsips attached
     * @return Course
     */
    protected function createTestCourseWithAssications()
    {
        $course = $this->createTestCourse();

        $course->setClerkshipType(new CourseClerkshipType());
        $course->setSchool(new School());

        $courseObjective1 = new Objective();
        $courseObjective1->setId(808);
        $courseObjective1->setTitle('test course objective1');
        $courseObjective1->addMeshDescriptor(new MeshDescriptor());
        $course->addObjective($courseObjective1);
        $courseObjective2 = new Objective();
        $courseObjective2->setId(42);
        $courseObjective2->setTitle('test course objective2');
        $course->addObjective($courseObjective2);

        $courseTerm1 = new Term();
        $courseTerm1->setId(808);
        $course->addTerm($courseTerm1);

        $lm = new LearningMaterial();

        $courseLearningMaterial1 = new CourseLearningMaterial();
        $courseLearningMaterial1->setLearningMaterial($lm);
        $courseLearningMaterial1->setId(808);
        $courseLearningMaterial1->addMeshDescriptor(new MeshDescriptor());
        $courseLearningMaterial1->setNotes('notes');
        $courseLearningMaterial1->setPublicNotes(true);
        $courseLearningMaterial1->setRequired(false);
        $course->addLearningMaterial($courseLearningMaterial1);

        $session1 = new Session();
        $session1->setSessionType(new SessionType());
        $sessionObjective1 = new Objective();
        $sessionObjective1->setId(99);
        $sessionObjective1->setTitle('test session learning material');
        $sessionObjective1->addMeshDescriptor(new MeshDescriptor());
        $sessionObjective1->addParent($courseObjective1);
        $sessionObjective1->addParent($courseObjective2);
        $session1->addObjective($sessionObjective1);

        $sessionLearningMaterial1 = new SessionLearningMaterial();
        $sessionLearningMaterial1->setLearningMaterial($lm);
        $sessionLearningMaterial1->setId(808);
        $sessionLearningMaterial1->addMeshDescriptor(new MeshDescriptor());
        $sessionLearningMaterial1->setNotes('notes');
        $sessionLearningMaterial1->setPublicNotes(true);
        $sessionLearningMaterial1->setRequired(false);
        $session1->addLearningMaterial($sessionLearningMaterial1);

        $sessionTerm1 = new Term();
        $sessionTerm1->setId(808);
        $session1->addTerm($sessionTerm1);

        $user = new User();

        $offering1 = new Offering();
        $offering1->setRoom('111b');
        $offering1->setSite('Off Campus');
        $offering1->setStartDate(new DateTime('8am'));
        $offering1->setEndDate(new DateTime('9am'));
        $offering1->addInstructor($user);

        $instructorGroup = new InstructorGroup();
        $instructorGroup->addUser($user);
        $offering1->addInstructorGroup($instructorGroup);

        $session1->addOffering($offering1);

        $course->addSession($session1);

        return $course;
    }

    /**
     * Setup the course manager mock to do basic stuff we need in most tests
     *
     * @param CourseInterface $course
     * @param CourseInterface $newCourse
     *
     * @return int
     */
    protected function setupCourseManager(CourseInterface $course, CourseInterface $newCourse)
    {
        $newYear = $course->getYear() + 1;
        $this->courseManager->shouldReceive('findOneBy')
            ->withArgs([['id' => $course->getId()]])->andReturn($course)->once();
        $this->courseManager
            ->shouldReceive('findBy')
            ->withArgs([['title' => $course->getTitle(), 'year' => $newYear]])
            ->andReturn(false)->once();
        $this->courseManager->shouldReceive('update')->withArgs([$newCourse, false, false])->once();

        $this->courseManager
            ->shouldReceive('create')->once()
            ->andReturn($newCourse);

        $this->courseManager->shouldReceive('flushAndClear')->once();

        return $newYear;
    }
}