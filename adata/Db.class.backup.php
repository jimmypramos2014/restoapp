<?php
class Db {
	private $link;
	private $stmt;
	private $array;

	var $host = '';
    /**
     * Username used to connect to database
     */
    var $username = '';
    /**
     * Password used to connect to database
     */
    var $passwd = '';
    /**
     * Database to backup
     */
    var $dbName = '';

	private function conectar()
	{
		$host='localhost';
		$user='inuqorg_inroot';
		$password='123@abc';
		$db='inuqorg_restora';

		/*$host='localhost';
		$user='root';
		$password='';
		$db='restoapp';*/
		
		$this->host     = $host;
        $this->username = $user;
        $this->passwd   = $password;
        $this->dbName   = $db;
		
		$this->link=mysql_connect($host, $user, $password);

		mysql_select_db($db,$this->link);

		@mysql_query("SET NAMES 'utf8'");
	}	

	public function Db()
	{
		$this->conectar();
	}

	private function obtener_filas($stmt)
	{
		$fetchrow = array();

		while ($row = mysql_fetch_array($stmt))
			$fetchrow[] = $row;

		$this->array = $fetchrow;
		return $this->array;
	}

	private function lastID()
	{
		return mysql_insert_id($this->link);
	}

	public function ejecutar($sql)
	{
		$this->stmt=mysql_query($sql,$this->link);

		/*echo $sql;
		echo mysql_error($this->link);*/
		return $this->stmt;
	}
	
	public function show_tables($param='')
	{
		$strsql = ' SHOW TABLES ';
		$rs = $this->ejecutar($strsql);

		$resultado = $this->obtener_filas($rs);

		//echo $strsql;
		//echo mysql_error($this->link);

		return $resultado;
	}

	public function set_select($fields, $table, $where = false, $orderby = false, $groupby = false, $limit = false)
	{
		if (is_array($fields))
			$fields = '' . implode($fields, ', ') . '';

		$groupby = ($groupby) ? ' GROUP BY ' . $groupby : '';
		$orderby = ($orderby) ? ' ORDER BY ' . $orderby : '';
		$limit = ($limit) ? ' LIMIT ' . $limit : '';
		$where = ($where) ? ' WHERE ' . $where : '';
		$strsql = 'SELECT ' . $fields . ' FROM ' . $table . '' . $where . $groupby . $orderby . $limit;
		$rs = $this->ejecutar($strsql);
		$resultado = $this->obtener_filas($rs);

	 	/*echo $strsql;
		echo mysql_error($this->link);*/

		return $resultado;
	}

	public function set_insert(array $values, $table)
	{
		if (count($values) < 0)
			return false;

		foreach($values as $field => $val){
			$val = '\''.$val.'\'';
			$values[$field] = $val; 
		}

		$strsql = 'INSERT INTO '.$table.' ('.implode(array_keys($values), ', ').') VALUES ('.implode($values, ', ').')';
		$rs = $this->ejecutar($strsql);

		/*echo $strsql;
		echo mysql_error($this->link);*/

		if ($rs)
			return $this->lastID();
		else
			return 0;
	}

	public function set_update($values, $table, $where = false)
	{
		if (is_array($values)) {
			if (count($values) < 0)
				return false;

			$fields = array();

			foreach($values as $field => $val){
				$val = '\''.$val.'\'';
				$fields[] = $field.' = '.$val;
			}
			$strsql = 'UPDATE '.$table.' SET '.implode($fields, ', ');
		}
		else
			$strsql = 'UPDATE '.$table.' SET '.$values;

		$where = ($where) ? ' WHERE '.$where : '';
		$strsql = $strsql.$where;
		$rs = $this->ejecutar($strsql);

		//echo $strsql;

		//echo mysql_error($this->link);

		if ($rs)
			return $rs;
		else
			return 0;
	}

	public function set_delete($table, $where = false)
	{
		$where = ($where) ? ' WHERE ' . $where : '';
		$strsql = 'DELETE FROM '.$table.$where;
		$rs = $this->ejecutar($strsql);

		//echo $strsql;

		//echo mysql_error($this->link);

		if ($rs)
			return $rs;
		else
			return 0;
	}

	public function backupTables($tables, $outputDir)
    {
        try {
            /**
            * Tables to export
            */
            if($tables == '*'){
                $tables = array();
                $result = mysql_query('SHOW TABLES', $this->link);
                while($row = mysql_fetch_row($result))
                    $tables[] = $row[0];
            }
            else
                $tables = is_array($tables) ? $tables : explode(',', $tables);
 
            $sql = 'CREATE DATABASE IF NOT EXISTS '.$this->dbName.";\n\n";
            $sql .= 'USE '.$this->dbName.";\n\n";
 
            /**
            * Iterate tables
            */
            foreach($tables as $table){
                $result = mysql_query('SELECT * FROM '.$table,$this->link);
                $numFields = mysql_num_fields($result);
 
                $sql .= 'DROP TABLE IF EXISTS '.$table.';';
                $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table,$this->link));
                $sql.= "\n\n".$row2[1].";\n\n";
 
                for ($i = 0; $i < $numFields; $i++){
                    while($row = mysql_fetch_row($result)){
                        $sql .= 'INSERT INTO '.$table.' VALUES(';
                        for($j=0; $j<$numFields; $j++){
                            $row[$j] = addslashes($row[$j]);
                            $row[$j] = str_replace("\n","\\n",$row[$j]);
                            
                            if (isset($row[$j]))
                                $sql .= '"'.$row[$j].'"' ;
                            else
                                $sql.= '""';
 
                            if ($j < ($numFields-1))
                                $sql .= ',';
                        }
                        $sql.= ");\n";
                    }
                }
 
                $sql.="\n\n\n";
            }
            //echo $sql;
			//echo mysql_error($this->link);
        }
        catch (Exception $e){
            var_dump($e->getMessage());
            return false;
        }
        return $this->saveFile($sql, $outputDir);
    }
 
 	public function backupToExcel($table, $outputDir)
    {
    	$query = 'SELECT * FROM '.$table;

		// Execute the database query
		$result = mysql_query($query, $this->link) or die(mysql_error());

		// Instantiate a new PHPExcel object
		$objPHPExcel = new PHPExcel(); 
		// Set the active Excel worksheet to sheet 0
		$objPHPExcel->setActiveSheetIndex(0); 
		// Initialise the Excel row number
		$rowCount = 1; 

		//start of printing column names as names of MySQL fields  
		$column = 'A';
		for ($i = 1; $i < mysql_num_fields($result); $i++)  {
		    $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, mysql_field_name($result,$i));
		    $column++;
		}
		//end of adding column names  

		//start while loop to get data  
		$rowCount = 2;
		// Iterate through each result from the SQL query in turn
		// We fetch each database result row into $row in turn
		while($row = mysql_fetch_row($result)) {  
		    $column = 'A';
		    for($j=1; $j<mysql_num_fields($result);$j++) {  
		        if(!isset($row[$j]))  
		            $value = NULL;  
		        elseif ($row[$j] != '')  
		            $value = strip_tags($row[$j]);  
		        else  
		            $value = '';  

		        $objPHPExcel->getActiveSheet()->setCellValue($column.$rowCount, $value);
		        $column++;
		    }  
		    $rowCount++;
		}
		
		// Instantiate a Writer to create an OfficeOpenXML Excel .xlsx file
		$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel); 
		// Write the Excel file to filename some_excel_file.xlsx in the current directory
		$objWriter->save($outputDir);
    }
    /**
     * Save SQL to file
     * @param string $sql
     */
    protected function saveFile(&$sql, $outputDir = '.')
    {
        if (!$sql) return false;
 
        try {
            $handle = fopen($outputDir,'w+');
            fwrite($handle, $sql);
            fclose($handle);
        }
        catch (Exception $e) {
            var_dump($e->getMessage());
            return false;
        }
        return true;
    }
}
?>