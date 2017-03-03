<?php
/**
 * My_Model_Resource_Db_Table_Abstract
 */
abstract class My_Model_Resource_Db_Table_Abstract extends Zend_Db_Table_Abstract implements My_Model_Resource_Db_Interface
{
	/**
     * Save a row to the database
     *
     * @param array             $info The data to insert/update
     * @param Zend_DB_Table_Row $row Optional The row to use
     * @return mixed The primary key
     */
    public function saveRow($info, $row = null)
    {
        if (null === $row) {
            $row = $this->createRow();
        }
        
        $columns = $this->info('cols');
        foreach ($columns as $column) {
            if (array_key_exists($column, $info)) {
                $row->$column = $info[$column];
            }
        }
        
        return $row->save();
    }

	public function deleteRow($row)
	{
		if (!$row instanceof $this->_rowClass) {
			return false;
		}
		
		// foreach ($row->getMeta() as $metaRow) {
		// 			if (!$metaRow->delete()) {
		// 				return false;
		// 			}
		// 		}
		
		return $row->delete();
	}
	
	public function saveMeta($ref_id = null, $info = null)
	{
		if ($ref_id ===  null ||$info === null) {
			return false;
		}
		
		$metaTable = new Resource_Meta();
		$table = $this->info('name');
		
		foreach ($info as $key => $value) {
			
			if (!$metaTable->saveMetaData($ref_id, $table, $key, $value))
				return false;
		}
		
		return true;
	}
	
	public function saveMetaRow($ref_id, $table, $key, $value)
	{
		$metaTable = new Resource_Meta();
		if (!$metaTable->saveMetaData($ref_id, $table, $key, $value)) {
			return false;
		}
		return true;
	}
	
	public function generateUniqueUrl($name)
	{
		$url = strtolower($name);

        $filters = array(
            // replace & with 'and' for readability
            '/&+/' => 'and',

            // replace non-alphanumeric characters with a hyphen
            '/[^a-z0-9]+/i' => '-',

            // replace multiple hyphens with a single hyphen
            '/-+/'          => '-'
        );


        // apply each replacement
        foreach ($filters as $regex => $replacement)
            $url = preg_replace($regex, $replacement, $url);
		
		// restrict the length of the URL
        $url = trim(substr($url, 0, 35));

        // remove hyphens from the start and end of string
        $url = trim($url, '-');
		
		// set a default value just in case
        if (strlen($url) == 0)
            $url = $this->info('name');


        // find similar URLs
        $query = sprintf("select url from %s where url like ?",
                         $this->_name);

        $query = $this->getAdapter()->quoteInto($query, $url . '%');
        $result = $this->getAdapter()->fetchCol($query);

        // if no matching URLs then return the current URL
        if (count($result) == 0 || !in_array($url, $result))
            return $url;

        // generate a unique URL
        $i = 2;
        do {
            $_url = $url . '-' . $i++;
        } while (in_array($_url, $result));
		
        return $_url;
	}
}
