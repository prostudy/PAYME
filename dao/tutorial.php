<?php

function simpleDataBaseTest(){
	$database = new Database();

	try{
		//$database->query('select * from mxd_node where type = :type  order by nid desc limit 2');
		//$database->bind(':type', 'ficha');
		//$database->query('select * from tabla1');
		$database->query('select pagetitle,introtext from modx_site_content order by id desc limit 100');
		$rows = $database->resultset(); //$row = $database->single();
		//echo $database->rowCount();
		/*echo json_encode($rows);
		echo "<pre>";
		
		print_r($rows);
		echo "</pre>";*/
	}catch(PDOException $e){
		echo $e->getMessage();
	}finally{
		$database->closeConnection();
		$database = null;
	}

	return $rows;
}

function simpleDataBase(){
	$database = new Database();
	
	try{
		//$database->query('select * from mxd_node where type = :type  order by nid desc limit 2');
		//$database->bind(':type', 'ficha');
		$database->query('select pagetitle from modx_site_content order by id asc limit 7');
		$rows = $database->resultset(); //$row = $database->single();
		//echo $database->rowCount();
		/*echo "<pre>";
		print_r($rows);
		echo "</pre>";*/
	}catch(PDOException $e){	
		echo $e->getMessage();
	}finally{
		$database->closeConnection();
		$database = null;
	}

	return $rows;
}

function TransactionDataBase(){
	$database = new Database();
	$database->beginTransaction();
	
	try{
		$database->query("INSERT INTO `drupal_mxd`.`mxd_media_node` (`serie`, `nid`, `uid`, `fid`, `caption`, `location`, `state`, `keywords`, `uploaded`, `highlight`, `deleted`, `deletedby`) VALUES (NULL, :nid, :uid, '9002', 'hola', 'hola', '0', NULL, '', '0', NULL, '0')");
		
		$database->bind(':nid', 9002);
		$database->bind(':uid', 9002);
		$database->execute();
		
		echo $database->lastInsertId();
		$database->debugDumpParams();
		
		$database->endTransaction();
	}catch(PDOException $e){
		$database->cancelTransaction();

    	echo $e->getMessage();
    }finally{
    	$database->closeConnection();
    	$database = null;
    }
    
}

//TransactionDataBase();
//simpleDataBase();
//simpleDataBaseTest();
?>