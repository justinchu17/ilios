<?php
namespace Ilios\AuthenticationBundle\Tests\Voter;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\AuthenticationBundle\Voter\AamcResourceTypeVoter;
use Ilios\CoreBundle\Entity\AamcResourceType;
use Ilios\CoreBundle\Entity\AamcResourceTypeInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class AamcResourceTypeVoterTest
 * @package Ilios\AuthenticationBundle\Tests\Voter
 */
class AamcResourceTypeVoterTest extends AbstractVoterTestCase
{
    /**
     * @var AamcResourceTypeVoter
     */
    protected $voter;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->voter = new AamcResourceTypeVoter();
    }

    /**
     * @dataProvider testVoteOnViewProvider
     * @covers Ilios\AuthenticationBundle\Voter\AamcResourceTypeVoter::vote
     */
    public function testVoteOnView($token, AamcResourceTypeInterface $object, $expectedOutcome, $message)
    {
        $attributes = [AbstractVoter::VIEW];
        $this->assertEquals($expectedOutcome, $this->voter->vote($token, $object, $attributes), $message);
    }

    /**
     * @dataProvider testVoteOnCreateProvider
     * @covers Ilios\AuthenticationBundle\Voter\AamcResourceTypeVoter::vote
     * @no
     */
    public function testVoteOnCreate($token, AamcResourceTypeInterface $object, $expectedOutcome, $message)
    {
        $attributes = [AbstractVoter::CREATE];
        $this->assertEquals($expectedOutcome, $this->voter->vote($token, $object, $attributes), $message);
    }

    /**
     * @dataProvider testVoteOnEditProvider
     * @covers Ilios\AuthenticationBundle\Voter\AamcResourceTypeVoter::vote
     */
    public function testVoteOnEdit($token, AamcResourceTypeInterface $object, $expectedOutcome, $message)
    {
        $attributes = [AbstractVoter::EDIT];
        $this->assertEquals($expectedOutcome, $this->voter->vote($token, $object, $attributes), $message);
    }

    /**
     * @dataProvider testVoteOnDeleteProvider
     * @covers Ilios\AuthenticationBundle\Voter\AamcResourceTypeVoter::vote
     */
    public function testVoteOnDelete($token, AamcResourceTypeInterface $object, $expectedOutcome, $message)
    {
        $attributes = [AbstractVoter::DELETE];
        $this->assertEquals($expectedOutcome, $this->voter->vote($token, $object, $attributes), $message);
    }

    /**
     * @return array
     */
    public function testVoteOnDeleteProvider()
    {
        $data = [];
        $data[] = [
            $this->createTokenForUserWithRole('Developer'),
            new AamcResourceType(),
            VoterInterface::ACCESS_GRANTED,
            'Developer can delete AAMC resource types.',
        ];
        foreach (['Faculty', 'Course Director', 'Student', 'Former Student'] as $role) {
            $data[] = [
                $this->createTokenForUserWithRole($role),
                new AamcResourceType(),
                VoterInterface::ACCESS_DENIED,
                "{$role} cannot delete AAMC resource types.",
            ];
        }
        return $data;
    }

    /**
     * @return array
     */
    public function testVoteOnCreateProvider()
    {
        $data = [];
        $data[] = [
            $this->createTokenForUserWithRole('Developer'),
            new AamcResourceType(),
            VoterInterface::ACCESS_GRANTED,
            'Developer can create AAMC resource types.',
        ];
        foreach (['Faculty', 'Course Director', 'Student', 'Former Student'] as $role) {
            $data[] = [
                $this->createTokenForUserWithRole($role),
                new AamcResourceType(),
                VoterInterface::ACCESS_DENIED,
                "{$role} cannot create AAMC resource types.",
            ];
        }
        return $data;
    }

    /**
     * @return array
     */
    public function testVoteOnEditProvider()
    {
        $data = [];
        $data[] = [
            $this->createTokenForUserWithRole('Developer'),
            new AamcResourceType(),
            VoterInterface::ACCESS_GRANTED,
            'Developer can edit AAMC resource types.',
        ];
        foreach (['Faculty', 'Course Director', 'Student', 'Former Student'] as $role) {
            $data[] = [
                $this->createTokenForUserWithRole($role),
                new AamcResourceType(),
                VoterInterface::ACCESS_DENIED,
                "{$role} cannot edit AAMC resource types.",
            ];
        }
        return $data;
    }

    /**
     * @return array
     */
    public function testVoteOnViewProvider()
    {
        $data = [];
        foreach (['Developer', 'Faculty', 'Course Director', 'Student', 'Former Student'] as $role) {
            $data[] = [
                $this->createTokenForUserWithRole($role),
                new AamcResourceType(),
                VoterInterface::ACCESS_GRANTED,
                "{$role} can view AAMC resource types.",
            ];
        }
        return $data;
    }

    /**
     * Creates a mock token for a user with a given user-role.
     * @param string $role The user-role title.
     * @return \Mockery\Mock
     */
    protected function createTokenForUserWithRole($role)
    {
        return $this->createMockTokenWithUser($this->createMockUserWithUserRoles([$this->createMockUserRole($role)]));
    }
}
