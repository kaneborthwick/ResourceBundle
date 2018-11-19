<?php

namespace ResourceBundle\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr;
use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Towersystems\Resource\Model\ResourceInterface;
use Towersystems\Resource\Repository\RepositoryInterface;

class EntityRepository extends BaseEntityRepository implements RepositoryInterface {

	/**
	 * {@inheritdoc}
	 */
	public function add(ResourceInterface $resource) {
		$this->_em->persist($resource);
		$this->_em->flush();
	}

	/**
	 * {@inheritdoc}
	 */
	public function remove(ResourceInterface $resource) {
		if (null !== $this->find($resource->getId())) {
			$this->_em->remove($resource);
			$this->_em->flush();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function createPaginator(array $criteria = [], array $sorting = [], $queryBuilder = null, $complex = false) {
		$queryBuilder = $queryBuilder ?: $this->createQueryBuilder('o');

		if ($complex) {
			$this->applyComplexCriteria($queryBuilder, $criteria);
		} else {
			$this->applyCriteria($queryBuilder, $criteria);
		}

		$this->applySorting($queryBuilder, $sorting);
		return $this->getPaginator($queryBuilder);
	}

	/**
	 * {@inheritdoc}
	 */
	public function count(array $criteria = []) {
		$queryBuilder = $this->createQueryBuilder('o');
		$this->applyCriteria($queryBuilder, $criteria);
		$queryBuilder->select('count(o.id)');
		return $queryBuilder->getQuery()->getSingleScalarResult();
	}

	/**
	 * @param QueryBuilder $queryBuilder
	 *
	 * @return Pagerfanta
	 */
	protected function getPaginator(QueryBuilder $queryBuilder) {
		return new Pagerfanta(new DoctrineORMAdapter($queryBuilder, false, false));
	}

	/**
	 * @param array $objects
	 *
	 * @return Pagerfanta
	 */
	protected function getArrayPaginator($objects) {

	}

	/**
	 * @param QueryBuilder $queryBuilder
	 * @param array $criteria
	 */
	protected function applyCriteria(QueryBuilder $queryBuilder, array $criteria = []) {

		foreach ($criteria as $property => $value) {
			if (!in_array($property, array_merge($this->_class->getAssociationNames(), $this->_class->getFieldNames()))) {
				continue;
			}

			$name = $this->getPropertyName($property);

			if (null === $value) {
				$queryBuilder->andWhere($queryBuilder->expr()->isNull($name));
			} elseif (is_array($value)) {
				$queryBuilder->andWhere($queryBuilder->expr()->in($name, $value));
			} elseif ('' !== $value) {
				$parameter = str_replace('.', '_', $property);
				$queryBuilder
					->andWhere($queryBuilder->expr()->eq($name, ':' . $parameter))
					->setParameter($parameter, $value)
				;
			}
		}
	}

	public function applyQuerySearch(QueryBuilder $queryBuilder, $query = "", $columns = []) {

		if ($query) {
			foreach ($columns as $property) {
				$name = $this->getPropertyName($property);

				$queryBuilder->andWhere($queryBuilder->expr()->like($name, "%" . $query . "%"));
			}
		}

		return $queryBuilder;
	}

	public function applyComplexCriteria(QueryBuilder $qb, array $data = []) {
		if (count($data)) {
			$criteria = Criteria::create();
			foreach ($data as $criterion) {
				[$field, $operator, $value] = $criterion;
				$criteria->andWhere(Criteria::expr()->$operator($field, $value));
			}
			$qb->addCriteria($criteria);
		}
	}

	/**
	 * [findById description]
	 * @param  int    $id [description]
	 * @return [type]     [description]
	 */
	public function findById(int $id) {
		return $this->findOneBy(['id' => $id]);
	}

	/**
	 * Finds entities by a set of criteria.
	 *
	 * @param array      $criteria
	 * @param array|null $orderBy
	 * @param int|null   $limit
	 * @param int|null   $offset
	 *
	 * @return array The objects.
	 */
	public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null, $array = false) {
		$persister = $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName);
		return $persister->loadAll($criteria, $orderBy, $limit, $offset);
	}

	/**
	 * @param QueryBuilder $queryBuilder
	 * @param array $sorting
	 */
	protected function applySorting(QueryBuilder $queryBuilder, array $sorting = []) {
		foreach ($sorting as $property => $order) {
			if (!in_array($property, array_merge($this->_class->getAssociationNames(), $this->_class->getFieldNames()))) {
				continue;
			}

			$name = $this->getPropertyName($property);
			$queryBuilder->addOrderBy($name, $order);
		}
	}

	/**
	 * @param string $name
	 *
	 * @return string
	 */
	protected function getPropertyName($name) {
		if (false === strpos($name, '.')) {
			return 'o' . '.' . $name;
		}

		return $name;
	}

}