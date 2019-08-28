<?php 

namespace App\Database;

use PDOStatement;
use RuntimeException;
use App\Database\Driver;

class Model {
    
    /** @var Driver */
    protected $driver;

    /** @var array */
    protected $fillable = [];

    /** @var string|null */
    protected $table = null;

    /** @var string */
    protected $primaryKey = 'id';

    /** @var array */
    protected $pendingQuery = [
        'selects' => ['*'],
        'updates' => [],
        'wheres' => [],
        'orders' => [],
        'limit' => null,
        'offset' => null,
    ];

    /** @var string */
    protected $binding = 'select';

    /**
     * Get the value of driver
     */ 
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Set the value of driver
     *
     * @return  self
     */ 
    public function setDriver($driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * Set query binding to select mode
     *
     * @param string $fields
     * @return self
     */
    public function select($fields = '*') {
        $this->binding = 'select';

        $this->pendingQuery['selects'] = is_array($fields) ? $fields : [$fields];

        return $this;
    }

    public function insert(array $input) {
        $this->binding = 'insert';

        $this->pendingQuery['inserts'] = $input;

        $this->runQuery();

        return $this->driver->getRowCount();
    }

    public function update(array $input) {
        $this->binding = 'update';

        $this->pendingQuery['updates'] = $input;

        $this->runQuery();

        return $this->driver->getRowCount();
    }

    /**
     * add where to pending command
     *
     * @param array $input
     * @return self
     */
    public function where(array $input) {
        $this->pendingQuery['wheres'] = $input;

        return $this;
    }

    /**
     * set query limit
     *
     * @param integer $value
     * @return self
     */
    public function take(int $value) {
        $this->pendingQuery['limit'] = $value;

        return $this;
    }

    /**
     * set query offset
     *
     * @param integer $value
     * @return self
     */
    public function skip(int $value) {
        $this->pendingQuery['offset'] = $value;

        return $this;
    }

    /**
     * Set order by query
     *
     * @param string $field
     * @param string $sort
     * @return self
     */
    public function orderBy($field, $sort = 'asc') {
        array_push($this->pendingQuery['orders'], "{$field} {$sort}");

        return $this;
    }

    /**
     * get / fetch query
     *
     * @return array
     */
    public function get() {
        return $this->runQuery()->fetchAll();
    }

    /**
     * get alias
     *
     * @return array
     */
    public function all() {
        return $this->get();
    }

    /**
     * Find record by it's id
     *
     * @param mixed $id
     * @return array|null
     */
    public function find($id) {
        $this->where([
            $this->primaryKey => $id,
        ]);

        $data = $this->get();

        if(!count($data)) {
            return null;
        }

        return $data[0];
    }

    /**
     * get table count
     *
     * @return int
     */
    public function count() {
        list($whereSql, $values) = $this->getWheres();

        $sql = "SELECT count(*) AS count FROM {$this->getTable()}" . $whereSql;

        $result = $this->driver->query($sql, $values)->fetchAll();

        return $result[0]['count'];
    }

    /**
     * Run query by binding value
     *
     * @return PDOStatement
     */
    protected function runQuery() {
        if (!isset($this->driver)) {
            throw new RuntimeException('Set driver first!');
        }

        $method = "{$this->binding}RunQuery";

        return $this->{$method}();
    }

    /**
     * get filtered fillable fields
     * 
     * @param array $input
     * @return array
     */
    protected function getFillable(array $input) {
        $fillable = $this->fillable;
        return array_filter($input, function($value, $key) use ($fillable) {
            return in_array($key, $fillable);
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * get wheres data
     *
     * @return array
     */
    protected function getWheres() {
        $wheres = $this->pendingQuery['wheres'];
        $values = null;
        $whereSql = null;

        if (count($wheres)) {
            $mappedFields = array_map(function($value) {
                return "{$value}=?";
            }, array_keys($wheres));

            $fields = implode(' AND ', $mappedFields);

            $whereSql = " WHERE {$fields}";
    
            $values = array_values($wheres);
        }

        return [$whereSql, $values];
    }

    /**
     * run select query
     *
     * @return PDOStatement
     */
    protected function selectRunQuery() {
        $selectFields = implode(',', $this->pendingQuery['selects']);

        list($whereSql, $values) = $this->getWheres();

        $limitSql = null;
        if($this->pendingQuery['limit']) {
            $limitSql = " LIMIT {$this->pendingQuery['limit']}";
        }

        $offsetSql = null;
        if($this->pendingQuery['offset']) {
            $offsetSql = " OFFSET {$this->pendingQuery['offset']}";
        }

        $orderBySql = null;
        if(count($this->pendingQuery['orders'])) {
            $orderBySql = " ORDER BY " . implode(',', $this->pendingQuery['orders']);
        }

        if($this->pendingQuery)

        $sql = "SELECT {$selectFields} FROM {$this->getTable()}" . $whereSql . $limitSql . $offsetSql;

        return $this->driver->query($sql, $values);
    }

    /**
     * run prepared insert Query
     *
     * @return PDOStatement
     */
    protected function insertRunQuery() {
        $fillable = $this->getFillable($this->pendingQuery['inserts']);
        $fields = implode(',', array_keys($fillable));
        $valueMarks = implode(',', array_map(function($value) {
            return '?';
        }, $fillable));
        $values = array_values($fillable);

        $sql = "INSERT INTO {$this->getTable()} ({$fields}) VALUES ({$valueMarks})";

        return $this->driver->query($sql, $values);
    }

    /**
     * run insert Query
     *
     * @return PDOStatement
     */
    protected function updateRunQuery() {
        $fillable = $this->getFillable($this->pendingQuery['updates']);

        $fields = implode(',', array_map(function($value) {
            return "{$value}=?";
        }, array_keys($fillable)));
        $values = array_values($fillable);

        list($whereSql, $whereValues) = $this->getWheres();

        if($whereSql) {
            $values = array_merge($values, $whereValues);
        }

        $sql = "UPDATE {$this->getTable()} SET {$fields}" . $whereSql;

        return $this->driver->query($sql, $values);
    }

    /**
     * Get the value of table
     * 
     * @return string
     */ 
    public function getTable()
    {
        $className = get_class($this);
        $className = explode('\\', $className);
        $className = array_pop($className);

        return fromCamelCaseToSnakeCase($className);
    }

}
