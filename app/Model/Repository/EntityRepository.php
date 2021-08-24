<?php

namespace App\Model\Repository;

use Nette\Database\Explorer;
use Nette\Database\Table\Selection;
use Nette\SmartObject;

/**
 * Class EntityRepository
 * @property-read Explorer $database
 *
 * @package App\Model\Repository
 */
class EntityRepository
{
    use SmartObject;

    /** @var string  */
    protected string $tableName;

    /** @var Explorer */
    private Explorer $database;


    /**
     * EntityRepository constructor.
     * @param Explorer $explorer
     */
    public function __construct(Explorer $explorer)
    {
        $this->database = $explorer;
    }


    /**
     * @return Explorer
     */
    public function getDatabase(): Explorer
    {
        return $this->database;
    }


    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }


    /**
     * @return \Nette\Database\Table\Selection
     */
    public function getSelection(): Selection
    {
        return $this->database->table(self::getTableName());
    }


}