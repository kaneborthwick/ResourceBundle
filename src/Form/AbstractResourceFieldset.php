<?php

namespace ResourceBundle\Form;

use ResourceBundle\Hydrator\DoctrineObjectHydrator;
use Zend\Form\Fieldset;
use Zend\Form\Form;

abstract class AbstractResourceFieldset extends Fieldset
{

    /**
     * [$em description]
     * @var [type]
     */
    protected $em;

    /**
     * [$metafield description]
     * @var [type]
     */
    protected $registry;

    /**
     * [__construct description]
     * @param [type] $em       [description]
     * @param [type] $resource [description]
     */
    public function __construct($em, $name)
    {
        $this->em = $em;
        parent::__construct($name);
        $this->setHydrator(new DoctrineObjectHydrator($em));

        $this->add([
            'name' => 'id',
            'options' => [
                'label' => 'Id',
            ],
            'type' => 'Text',
        ]);
    }

    protected function resolveEntityName($class)
    {
        $metadata = $this->em->getClassMetadata($class);
        return $metadata->name;
    }
}
