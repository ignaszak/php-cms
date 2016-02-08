<?php
namespace Content\Controller;

class Factory
{

    /**
     *
     * @var Controller
     */
    private $_controller;

    /**
     *
     * @param Controller $_controller
     */
    public function __construct(Controller $_controller)
    {
        $this->_controller = $_controller;
    }

    /**
     *
     * @param string $name
     * @param array $arguments
     * @return Factory
     */
    public function __call(string $name, array $arguments): Factory
    {
        $this->_controller->saveEntitySetter($name, $arguments);

        return $this;
    }

    /**
     *
     * @param string $string
     * @return string
     */
    public function getAlias(string $string): string
    {
        $_alias = new Alias($this->_controller->getEntity());
        return $_alias->getAlias($string);
    }

    /**
     * @return \Entity
     */
    public function entity()
    {
        return $this->_controller->getEntity();
    }

    /**
     *
     * @param string $entityName
     * @param int $by
     * @return Factory
     */
    public function setReference(string $entityName, int $by): Factory
    {
        $this->_controller->setReference($entityName, $by);

        return $this;
    }

    /**
     *
     * @param int $id
     * @return Factory
     */
    public function find(int $id): Factory
    {
        $this->_controller->find($id);

        return $this;
    }

    /**
     *
     * @param array $array
     * @return Factory
     */
    public function findBy(array $array): Factory
    {
        $this->_controller->findBy($array);

        return $this;
    }

    /**
     *
     * @param array $array
     * @return Factory
     */
    public function findOneBy(array $array): Factory
    {
        $this->_controller->findOneBy($array);

        return $this;
    }

    /**
     *
     * @return Factory
     */
    public function insert(): Factory
    {
        $this->_controller->insert();

        return $this;
    }

    /**
     *
     * @return Factory
     */
    public function remove(): Factory
    {
        $this->_controller->remove();

        return $this;
    }

    /**
     *
     * @return Factory
     */
    public function update(array $array = []): Factory
    {
        $this->_controller->update($array);

        return $this;
    }
}
