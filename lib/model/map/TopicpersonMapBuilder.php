<?php


/**
 * This class adds structure of 'topicPerson' table to 'propel' DatabaseMap object.
 *
 *
 * This class was autogenerated by Propel 1.3.0-dev on:
 *
 * Sat May  9 14:52:00 2009
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    lib.model.map
 */
class TopicpersonMapBuilder implements MapBuilder {

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'lib.model.map.TopicpersonMapBuilder';

	/**
	 * The database map.
	 */
	private $dbMap;

	/**
	 * Tells us if this DatabaseMapBuilder is built so that we
	 * don't have to re-build it every time.
	 *
	 * @return     boolean true if this DatabaseMapBuilder is built, false otherwise.
	 */
	public function isBuilt()
	{
		return ($this->dbMap !== null);
	}

	/**
	 * Gets the databasemap this map builder built.
	 *
	 * @return     the databasemap
	 */
	public function getDatabaseMap()
	{
		return $this->dbMap;
	}

	/**
	 * The doBuild() method builds the DatabaseMap
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function doBuild()
	{
		$this->dbMap = Propel::getDatabaseMap(TopicpersonPeer::DATABASE_NAME);

		$tMap = $this->dbMap->addTable(TopicpersonPeer::TABLE_NAME);
		$tMap->setPhpName('Topicperson');
		$tMap->setClassname('Topicperson');

		$tMap->setUseIdGenerator(true);

		$tMap->addForeignKey('PERSON_ID', 'PersonId', 'INTEGER', 'person', 'ID', true, null);

		$tMap->addForeignKey('TOPIC_ID', 'TopicId', 'INTEGER', 'topic', 'ID', true, null);

		$tMap->addPrimaryKey('ID', 'Id', 'INTEGER', true, null);

	} // doBuild()

} // TopicpersonMapBuilder
