<?php

namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\User;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tests for Entity Objective
 */
class UserTest extends EntityBase
{
    /**
     * @var User
     */
    protected $object;

    /**
     * Instantiate a Objective object
     */
    protected function setUp()
    {
        $this->object = new User;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'lastName',
            'firstName',
            'email'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setLastName('Andrews');
        $this->object->setFirstName('Julia');
        $this->object->setEmail('sanders@ucsf.edu');
        $this->validate(0);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAlerts());
        $this->assertEmpty($this->object->getDirectedCourses());
        $this->assertEmpty($this->object->getInstructorGroups());
        $this->assertEmpty($this->object->getInstructedLearnerGroups());
        $this->assertEmpty($this->object->getOfferings());
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getReminders());
        $this->assertEmpty($this->object->getRoles());
        $this->assertEmpty($this->object->getLearnerGroups());
        $this->assertEmpty($this->object->getLearningMaterials());
        $this->assertEmpty($this->object->getCohorts());
        $this->assertEmpty($this->object->getAuditLogs());
        $this->assertEmpty($this->object->getReports());
        $this->assertEmpty($this->object->getPendingUserUpdates());
        $this->assertEmpty($this->object->getPermissions());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setLastName
     * @covers Ilios\CoreBundle\Entity\User::getLastName
     */
    public function testSetLastName()
    {
        $this->basicSetTest('lastName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setFirstName
     * @covers Ilios\CoreBundle\Entity\User::getFirstName
     */
    public function testSetFirstName()
    {
        $this->basicSetTest('firstName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setMiddleName
     * @covers Ilios\CoreBundle\Entity\User::getMiddleName
     */
    public function testSetMiddleName()
    {
        $this->basicSetTest('middleName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setPhone
     * @covers Ilios\CoreBundle\Entity\User::getPhone
     */
    public function testSetPhone()
    {
        $this->basicSetTest('phone', 'phone');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setEmail
     * @covers Ilios\CoreBundle\Entity\User::getEmail
     */
    public function testSetEmail()
    {
        $this->basicSetTest('email', 'email');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setAddedViaIlios
     * @covers Ilios\CoreBundle\Entity\User::isAddedViaIlios
     */
    public function testSetAddedViaIlios()
    {
        $this->booleanSetTest('addedViaIlios');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setEnabled
     * @covers Ilios\CoreBundle\Entity\User::isEnabled
     */
    public function testSetEnabled()
    {
        $this->booleanSetTest('enabled');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setCampusId
     * @covers Ilios\CoreBundle\Entity\User::getCampusId
     */
    public function testSetCampusId()
    {
        $this->basicSetTest('campusId', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setOtherId
     * @covers Ilios\CoreBundle\Entity\User::getOtherId
     */
    public function testSetOtherId()
    {
        $this->basicSetTest('otherId', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setExamined
     * @covers Ilios\CoreBundle\Entity\User::isExamined
     */
    public function testSetExamined()
    {
        $this->booleanSetTest('examined');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setUserSyncIgnore
     * @covers Ilios\CoreBundle\Entity\User::isUserSyncIgnore
     */
    public function testSetUserSyncIgnore()
    {
        $this->booleanSetTest('userSyncIgnore');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setIcsFeedKey
     * @covers Ilios\CoreBundle\Entity\User::generateIcsFeedKey
     */
    public function testSetIcsFeedKey()
    {
        $this->basicSetTest('icsFeedKey', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setSchool
     * @covers Ilios\CoreBundle\Entity\User::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addReminder
     */
    public function testAddReminder()
    {
        $this->entityCollectionAddTest('reminder', 'UserMadeReminder');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setReminders
     * @covers Ilios\CoreBundle\Entity\User::getReminders
     */
    public function testSetReminders()
    {
        $this->entityCollectionSetTest('reminder', 'UserMadeReminder');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addPermission
     */
    public function testAddPermission()
    {
        $this->entityCollectionAddTest('permission', 'Permission');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setPermissions
     * @covers Ilios\CoreBundle\Entity\User::getPermissions
     */
    public function testSetPermissions()
    {
        $this->entityCollectionSetTest('permission', 'Permission');
    }


    /**
     * @covers Ilios\CoreBundle\Entity\User::addDirectedCourse
     */
    public function testAddDirectedCourse()
    {
        $this->entityCollectionAddTest('directedCourse', 'Course', false, false, 'addDirector');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getDirectedCourses
     */
    public function testGetDirectedCourses()
    {
        $this->entityCollectionSetTest('directedCourse', 'Course', false, false, 'addDirector');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addLearnerGroup
     */
    public function testAddLearnerGroup()
    {
        $this->entityCollectionAddTest('learnerGroup', 'LearnerGroup', false, false, 'addUser');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getLearnerGroups
     */
    public function testSetLearnerGroups()
    {
        $this->entityCollectionSetTest('learnerGroup', 'LearnerGroup', false, false, 'addUser');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addInstructedLearnerGroup
     */
    public function testAddInstructedLearnerGroup()
    {
        $this->entityCollectionAddTest('instructedLearnerGroup', 'LearnerGroup', false, false, 'addInstructor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getInstructedLearnerGroups
     */
    public function testGetInstructedLearnerGroups()
    {
        $this->entityCollectionSetTest('instructedLearnerGroup', 'LearnerGroup', false, false, 'addInstructor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addInstructorGroup
     */
    public function testAddInstructorGroup()
    {
        $this->entityCollectionAddTest('instructorGroup', 'InstructorGroup', false, false, 'addUser');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getInstructorGroups
     */
    public function testSetInstructorGroups()
    {
        $this->entityCollectionSetTest('instructorGroup', 'InstructorGroup', false, false, 'addUser');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addOffering
     */
    public function testAddOffering()
    {
        $this->entityCollectionAddTest('offering', 'Offering', false, false, 'addLearner');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getOfferings
     */
    public function testSetOfferings()
    {
        $this->entityCollectionSetTest('offering', 'Offering', false, false, 'addLearner');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addDirector');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getProgramYears
     */
    public function testSetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addDirector');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addAlert
     */
    public function testAddAlert()
    {
        $this->entityCollectionAddTest('alert', 'Alert', false, false, 'addInstigator');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getAlerts
     */
    public function testSetAlerts()
    {
        $this->entityCollectionSetTest('alert', 'Alert', false, false, 'addInstigator');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addRole
     */
    public function testAddRole()
    {
        $this->entityCollectionAddTest('role', 'UserRole');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getRoles
     */
    public function testSetRoles()
    {
        $this->entityCollectionSetTest('role', 'UserRole');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addLearningMaterial
     */
    public function testAddLearningMaterial()
    {
        $this->entityCollectionAddTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getLearningMaterials
     */
    public function testSetLearningMaterials()
    {
        $this->entityCollectionSetTest('learningMaterial', 'LearningMaterial');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addReport
     */
    public function testAddReport()
    {
        $this->entityCollectionAddTest('report', 'Report');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getReports
     */
    public function testSetReports()
    {
        $this->entityCollectionSetTest('report', 'Report');
    }


    /**
     * @covers Ilios\CoreBundle\Entity\User::addCohort
     */
    public function testAddCohort()
    {
        $this->entityCollectionAddTest('cohort', 'Cohort');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getCohorts
     */
    public function testSetCohorts()
    {
        $this->assertTrue(method_exists($this->object, 'setPrimaryCohort'));
        $this->assertTrue(method_exists($this->object, 'getPrimaryCohort'));

        $obj = m::mock('Ilios\CoreBundle\Entity\Cohort');
        $this->object->addCohort($obj);
        $this->object->setPrimaryCohort($obj);
        $obj2 = m::mock('Ilios\CoreBundle\Entity\Cohort');
        $this->object->setCohorts(new ArrayCollection([$obj2]));
        $this->assertNull($this->object->getPrimaryCohort());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addInstructedOffering
     */
    public function testAddInstructedOffering()
    {
        $this->entityCollectionAddTest('instructedOffering', 'Offering', false, false, 'addInstructor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getInstructedOfferings
     */
    public function testSetInstructedOffering()
    {
        $this->entityCollectionSetTest('instructedOffering', 'Offering', false, false, 'addInstructor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::addInstructorIlmSession
     */
    public function testAddInstructorIlmSessions()
    {
        $this->entityCollectionAddTest('instructorIlmSession', 'IlmSession', false, false, 'addInstructor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::getInstructorIlmSessions
     */
    public function testGetInstructorIlmSessions()
    {
        $this->entityCollectionSetTest('instructorIlmSession', 'IlmSession', false, false, 'addInstructor');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\User::setPrimaryCohort
     * @covers Ilios\CoreBundle\Entity\User::getPrimaryCohort
     */
    public function testSetPrimaryCohort()
    {
        $this->assertTrue(method_exists($this->object, 'setPrimaryCohort'));
        $this->assertTrue(method_exists($this->object, 'getPrimaryCohort'));

        $obj = m::mock('Ilios\CoreBundle\Entity\Cohort');
        $this->object->addCohort($obj);
        $this->object->setPrimaryCohort($obj);
        $this->assertSame($obj, $this->object->getPrimaryCohort());
        $this->assertTrue($this->object->getCohorts()->contains($obj));
    }
}
