<?php

namespace ResourceBundle\Request;
use Psr\Http\Message\ServerRequestInterface;
use Towersystems\Resource\Metadata\MetadataInterface;

class RequestConfiguration {

	const HEADER_SERIALIZATION_GROUP = 'serialization-group';

	/**
	 * [$metadata description]
	 * @var [type]
	 */
	protected $metadata;

	/**
	 * [$request description]
	 * @var [type]
	 */
	protected $request;

	/**
	 * [$parameters description]
	 * @var [type]
	 */
	protected $parameters;

	/**
	 * [__construct description]
	 * @param [type]                 $metadata  [description]
	 * @param ServerRequestInterface $request [description]
	 */
	public function __construct(MetadataInterface $metadata, ServerRequestInterface $request, $parameters) {
		$this->metadata = $metadata;
		$this->request = $request;
		$this->parameters = $parameters;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getMetadata() {
		return $this->metadata;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRequest() {
		return $this->request;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isLimited() {
		return isset($this->parameters['limit']) ? (bool) $this->parameters['limit'] : true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getLimit() {
		$limit = null;
		if ($this->isLimited()) {
			$limit = isset($this->parameters['limit']) ? (int) $this->parameters['limit'] : 10;
		}
		return $limit;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasOrderBy() {
		return isset($this->parameters['order_by']) ? (bool) $this->parameters['order_by'] : false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getOrderBy() {
		$orderBy = isset($this->parameters['order_by']) ? $this->parameters['order_by'] : [];
		return $orderBy;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPage() {
		return isset($this->parameters['page']) ? (int) $this->parameters['page'] : 1;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isPaginated() {
		return isset($this->parameters['paginate']) ? (bool) $this->parameters['paginate'] : true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPaginationMaxPerPage() {
		return isset($this->parameters['limit']) ? (int) $this->parameters['limit'] : 10;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCriteria(array $criteria = []) {
		$parameterCriteria = isset($this->parameters["criteria"]) ? $this->parameters["criteria"] : [];
		$defaultCriteria = array_merge($parameterCriteria, $criteria);
		return $defaultCriteria;
	}

	/**
	 * {@inheritdoc}
	 */
	public function isComplex() {
		return isset($this->parameters['complex']) ? (bool) $this->parameters['complex'] : false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasStateMachine() {
		return isset($this->parameters['state_machine']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getStateMachineGraph() {
		$options = isset($this->parameters['state_machine']) ? $this->parameters['state_machine'] : [];
		return isset($options['graph']) ? $options['graph'] : null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getStateMachineTransition() {
		$options = isset($this->parameters['state_machine']) ? $this->parameters['state_machine'] : [];
		return isset($options['transition']) ? $options['transition'] : null;
	}

	/**
	 * @return array
	 */
	public function getSerializationGroups(): array{
		return $this->request->getHeader(Self::HEADER_SERIALIZATION_GROUP);
	}

}