<?php

namespace sql\mysql;

use sql\Connection;
use sql\BlobHandler;
use sql\SQLException;

/**
 * Class MySQLBlobHandler
 *
 * This class extends BlobHandler providing specific methods for updating blobs on a MySQL database.
 */
class MySQLBlobHandler extends BlobHandler {

	/***
	 * Updates the given record on the database with the blobdata
	 *
	 * @param $blobData The binary data
	 * @param $keyValue The key value to update
	 *
	 * @return bytes written
	 */
	public function updateOn( $blobData, $keyValue ){

		//initialize Blob
		$initSql = $this->getInitializeSql();

		$stmt = $this->getConnection()->prepare( $initSql );
		$stmt->bind( ':key', $keyValue );
		$stmt->execute();
		unset($stmt);

		$blobSql = $this->getBlobSql();

		$sqlSize = strlen($blobSql);
		$blobSize = strlen($blobData)+1024*1024;
		$oldMaxPacket = $this->getMaxPacket();
		//fix to the allowed paket that e need
		$stmt=$this->getConnection()->prepare("set global max_allowed_packet = $blobSize");
		$stmt->execute();
		unset($stmt);
		//update
		$stmt = $this->getConnection()->prepare( $blobSql );
		$stmt->bind( ':key', $keyValue );
		$stmt->bind( ':blobValue', $blobData);
		$res=$stmt->execute();
		unset($stmt);
		//refix the allowed packet!
		$stmt=$this->getConnection()->prepare("set global max_allowed_packet = $oldMaxPacket");
		$stmt->execute();
		unset($stmt);
		

		return $blobSize;

	}

	private function getInitializeSql() {
		return sprintf( "update `%s`.`%s` set `%s`='' where `%s`=:key",$this->getDB(), $this->getTable()
		, $this->getBlobName(), $this->getPrimaryKey() );
	}

	private function getBlobSql() {
		$update= sprintf( "update `%s`.`%s` set `%s`=:blobValue where `%s`=:key", $this->getDB(),$this->getTable()
		, $this->getBlobName(), $this->getPrimaryKey() );
		return $update;
	}

	private function getMaxPacket() {
		$stmt = $this->getConnection()->prepare( "show variables like :var" );
		$stmt->bind(':var','max_allowed_packet');
		$rs = $stmt->execute();
		$max = 0;
		if ($rs->next()){
			$max = $rs->Value;
		}
		else{
			throw new Exception('Could not obtain max allowed packet');
		}

		return $max;
	}

}

