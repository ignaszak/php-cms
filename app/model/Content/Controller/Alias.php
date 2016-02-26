<?php
namespace Content\Controller;

use Conf\DB\DBDoctrine;

class Alias
{

    /**
     *
     * @var EntityManager
     */
    private $_em;

    /**
     *
     * @var Entity
     */
    private $_entity;

    /**
     *
     * @var string
     */
    private $entityName;

    /**
     *
     * @param Entity $_entity
     * @throws \DomainException
     */
    public function __construct($_entity)
    {
        if (! is_object($_entity)) {
            throw new \DomainException(
                'Second argument passed to ' . __CLASS__ .
                '::aliasNotExistsInDB() must be an Entity instance'
            );
        } else {
            $this->_em = DBDoctrine::em();
            $this->_entity = $_entity;
            $this->entityName = get_class($_entity);
        }
    }

    /**
     *
     * @param string $string
     * @return string
     */
    public function getAlias(string $string): string
    {
        if (empty($this->_entity->getAlias())) {
            $alias = $this->createAliasFromString($string);
            return $this->renameAliasIfExistsInDB($alias);
        } else {
            return $this->_entity->getAlias();
        }
    }

    /**
     *
     * @param string $alias
     * @return string
     */
    private function renameAliasIfExistsInDB(string $alias): string
    {
        if ($this->isAliasNotExistsInDB($alias)) {
            return $alias;
        } else {
            $rename = "$alias-" . rand(1, 20);
            return $this->renameAliasIfExistsInDB($rename);
        }
    }

    /**
     *
     * @param string $string
     * @return string
     */
    private function createAliasFromString(string $string): string
    {
        $clean = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $string);
        $clean = preg_replace('/[^a-z\d ]/i', '', $clean);
        $clean = preg_replace('/\s\s+/', ' ', $clean);
        $clean = str_replace(' ', '-', $clean);
        return substr(strtolower($clean), 0, 100);
    }

    /**
     *
     * @param string $alias
     * @param Entity $_entity
     * @return boolean
     */
    private function isAliasNotExistsInDB(string $alias): bool
    {
        $query = $this->_em->getRepository($this->entityName)->findBy([
            'alias' => $alias
        ]);
        return count($query) ? false : true;
    }
}