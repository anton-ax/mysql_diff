<?php 
class Diff {

	public static function generate($from, $to){
		$sql = '';
		foreach ($from as $table => $data) {
			// if table not exist
			if (!array_key_exists($table, $to)) {
				$sql .= self::tableSQL($table, $data);
			} else {
				$toColumns = $to[$table]["columns"];
				foreach ($data["columns"] as $key => $value) {
					// if columns not exist
					if (!array_key_exists($key, $toColumns)){
						$sql .= "ALTER TABLE ".$table. " ADD ".$value["Field"];
						$sql .= " ".$value["Type"];
						if ($value['Null']=='NO') {
							$sql .= ' NOT NULL ';
							if ($value['Default']) {
								$sql .= ' DEFAULT \''.$value['Default'].'\'';
							}
							if ($value['Extra']) {
								$sql .= ' AUTO_INCREMENT';
							}
						}
						$sql .= ';';
					}else {
						$toColumn = $toColumns[$key];
						// if column not equal
						if(implode('', $value) != implode('', $toColumn)){
							$sql .= "ALTER TABLE ".$table. " MODIFY ".$value["Field"];
							$sql .= " ".$value["Type"];
							if ($value['Null']=='NO') {
								$sql .= ' NOT NULL ';
								if ($value['Default']) {
									$sql .= ' DEFAULT \''.$value['Default'].'\'';
								}
							}
							if ($value['Extra']) {
								$sql .= ' AUTO_INCREMENT';
							}
							$sql .= ';';
						}
					}
				}
			}
		}
		return $sql;
	}

	private static function tableSQL($table, $data) {
		$engine = $data["engine"];
		$columns = $data["columns"];
		$collate = $data["collate"];
		$primary = NULL;
		$sql = "CREATE TABLE `".$table.'` (';
		foreach ($columns as $key => $value) {
			if ($value['Key']=='PRI') {
				$primary = 'PRIMARY KEY(`'.$value['Field'].'`)';
			}
			$sql .= "`".$value['Field']."` ".$value['Type'];
			if ($value['Null']=='NO') {
				$sql .= ' NOT NULL ';
				if ($value['Default']) {
					$sql .= ' DEFAULT \''.$value['Default'].'\'';
				}
			}
			if ($value['Extra']) {
				$sql .= ' AUTO_INCREMENT';
			}
			$sql .= ',';
		}		
		if ($primary) {
			$sql .= $primary;
		}
		$sql .= ") ENGINE=".$engine.' COLLATE = '.$collate.";";
		return $sql;
	}
}