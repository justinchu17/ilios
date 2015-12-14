<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class CompetencyRepository
 * @package Ilios\CoreBundle\Entity\Repository
 */
class CompetencyRepository extends EntityRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('DISTINCT c')->from('IliosCoreBundle:Competency', 'c');

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('c.' . $sort, $order);
            }
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->leftJoin('c.children', 'se_subcompetency');
            $qb->leftJoin('c.objectives', 'se_py_objective');
            $qb->leftJoin('se_py_objective.children', 'se_course_objective');
            $qb->leftJoin('se_course_objective.children', 'se_session_objective');
            $qb->leftJoin('se_session_objective.sessions', 'se_session');
            $qb->leftJoin('se_subcompetency.objectives', 'se_py_objective2');
            $qb->leftJoin('se_py_objective2.children', 'se_course_objective2');
            $qb->leftJoin('se_course_objective2.children', 'se_session_objective2');
            $qb->leftJoin('se_session_objective2.sessions', 'se_session2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('se_session.id', ':sessions'),
                    $qb->expr()->in('se_session2.id', ':sessions')
                )
            );
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('sessionTypes', $criteria)) {
            $ids = is_array($criteria['sessionTypes']) ? $criteria['sessionTypes'] : [$criteria['sessionTypes']];
            $qb->leftJoin('c.children', 'st_subcompetency');
            $qb->leftJoin('c.objectives', 'st_py_objective');
            $qb->leftJoin('st_py_objective.children', 'st_course_objective');
            $qb->leftJoin('st_course_objective.children', 'st_session_objective');
            $qb->leftJoin('st_session_objective.sessions', 'st_session');
            $qb->leftJoin('st_session.sessionType', 'st_sessionType');
            $qb->leftJoin('st_subcompetency.objectives', 'st_py_objective2');
            $qb->leftJoin('st_py_objective2.children', 'st_course_objective2');
            $qb->leftJoin('st_course_objective2.children', 'st_session_objective2');
            $qb->leftJoin('st_session_objective2.sessions', 'st_session2');
            $qb->leftJoin('st_session2.sessionType', 'st_sessionType2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('st_sessionType.id', ':sessionTypes'),
                    $qb->expr()->in('st_sessionType2.id', ':sessionTypes')
                )
            );
            $qb->setParameter(':sessionTypes', $ids);
        }

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->leftJoin('c.children', 'c_subcompetency');
            $qb->leftJoin('c.objectives', 'c_py_objective');
            $qb->leftJoin('c_py_objective.children', 'c_course_objective');
            $qb->leftJoin('c_course_objective.courses', 'c_course');
            $qb->leftJoin('c_subcompetency.objectives', 'c_py_objective2');
            $qb->leftJoin('c_py_objective2.children', 'c_course_objective2');
            $qb->leftJoin('c_course_objective2.courses', 'c_course2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('c_course.id', ':courses'),
                    $qb->expr()->in('c_course2.id', ':courses')
                )
            );
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('topics', $criteria)) {
            $ids = is_array($criteria['topics']) ? $criteria['topics'] : [$criteria['topics']];
            $qb->leftJoin('c.children', 't_subcompetency');
            $qb->leftJoin('c.objectives', 't_py_objective');
            $qb->leftJoin('t_py_objective.children', 't_course_objective');
            $qb->leftJoin('t_course_objective.courses', 't_course');
            $qb->leftJoin('t_course.topics', 't_topic');
            $qb->leftJoin('t_course_objective.children', 't_session_objective');
            $qb->leftJoin('t_session_objective.sessions', 't_session');
            $qb->leftJoin('t_session.topics', 't_topic2');
            $qb->leftJoin('t_subcompetency.objectives', 't_py_objective2');
            $qb->leftJoin('t_py_objective2.children', 't_course_objective2');
            $qb->leftJoin('t_course_objective2.courses', 't_course2');
            $qb->leftJoin('t_course2.topics', 't_topic3');
            $qb->leftJoin('t_course_objective2.children', 't_session_objective2');
            $qb->leftJoin('t_session_objective2.sessions', 't_session2');
            $qb->leftJoin('t_session2.topics', 't_topic4');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('t_topic.id', ':topics'),
                    $qb->expr()->in('t_topic2.id', ':topics'),
                    $qb->expr()->in('t_topic3.id', ':topics'),
                    $qb->expr()->in('t_topic4.id', ':topics')
                )
            );
            $qb->setParameter(':topics', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('c.school', 'sc_school');
            $qb->andWhere($qb->expr()->in('sc_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['schools']);
        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['sessionTypes']);
        unset($criteria['topics']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("c.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }
        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }
}