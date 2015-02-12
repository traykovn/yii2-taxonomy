<?php
/**
 * Created by PhpStorm.
 * User: Nikola nb
 * Date: 19.10.2014
 * Time: 10:46 ч.
 */

namespace nkostadinov\taxonomy;

use nkostadinov\taxonomy\components\exceptions\TermNotDefinedException;
use nkostadinov\taxonomy\components\terms\BaseTerm;
use nkostadinov\taxonomy\models\TaxonomyDef;
use nkostadinov\taxonomy\models\TaxonomyTerms;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\db\Migration;
use yii\db\Schema;
use yii\log\Logger;

class Taxonomy extends Component {

    /* @var Connection The db connection component */
    public $db = 'db';
    public $table = 'taxonomy';
    private $_taxonomy = [];

    public function isTermInstalled($termName)
    {
        $term = $this->getTerm($termName);
        return $term->isInstalled();
    }

    public function addTerm($term, $object_id, $params)
    {
        $term = $this->getTerm($term);
        $term->addTerm($object_id, $params);
    }

    public function addTermArray($term, $object_id, $array)
    {
        $term = $this->getTerm($term);
        foreach($array as $k => $v)
            $term->addTerm($object_id, [ 'name' => $k, 'value' => $v ]);
    }

    public function removeTerm($term, $object_id, $params = [])
    {
        $term = $this->getTerm($term);
        return $term->removeTerm($object_id, $params);
    }

    public function getTerms($term, $object_id, $name = null)
    {
        $term = $this->getTerm($term);
        return $term->getTerms($object_id, $name);
    }

    /**
     * @param $termName
     * @return BaseTerm
     * @throws InvalidConfigException
     * @throws TermNotDefinedException
     */
    public function getTerm($termName)
    {
        if(!isset($this->_taxonomy[$termName])) {
            $tax = TaxonomyDef::findOne(['name' => $termName]);
            \Yii::getLogger()->log("Initialising term $termName", Logger::LEVEL_INFO, 'nkostadinov.taxonomy.terms');
            $this->_taxonomy[$termName] = \Yii::createObject($tax->attributes);
        }
        return $this->_taxonomy[$termName];
    }

    public function isInstalled()
    {
        $res = \Yii::$app->db->getTableSchema(TaxonomyDef::tableName(), true) !== null;
        return $res;
    }

    public function install()
    {
        $migration = new Migration();
        $migration->createTable(TaxonomyDef::tableName(), [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING,
            'class' => Schema::TYPE_STRING,
            'created_at' => Schema::TYPE_TIMESTAMP . ' DEFAULT CURRENT_TIMESTAMP',
            'total_count' => Schema::TYPE_BIGINT . ' DEFAULT 0',
            'data_table' => Schema::TYPE_STRING,
            'ref_table' => Schema::TYPE_STRING,
        ]);

        $migration->createIndex('unq_name', TaxonomyDef::tableName(), 'name', true);

        $migration->createTable(TaxonomyTerms::tableName(), [
            'id' => Schema::TYPE_BIGPK,
            'taxonomy_id' => Schema::TYPE_INTEGER,
            'term' => Schema::TYPE_STRING,
            'total_count' => Schema::TYPE_BIGINT . ' DEFAULT 0',
            'parent_id' => Schema::TYPE_BIGINT,
        ]);

        if ($migration->db->driverName === 'mysql') {
            $migration->addForeignKey('fk_TaxonomyTerm_Taxonomy', TaxonomyTerms::tableName(), 'taxonomy_id',
                TaxonomyDef::tableName(), TaxonomyDef::primaryKey());
        }
    }
} 