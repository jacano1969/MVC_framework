<?php
/**
 * Clase de base de datos
 *
 * @package default
 */


class CDatabase {

	var $dblink = null;

	static	$database_config = array (
			"link_cms" => array ("database" => "iwprueba_webs", "host" => "localhost", "user" => "iwprueba_iw", "password" => "dekkker"),
			"link_dp" => array ("database" => "iwdomini_iwdominios", "host" => "localhost", "user" => "iwdomini_iwdomin", "password" => "oi54tlkjflkf"),
			"link_host" => array ("database" => "iwhosting", "host" => "localhost", "user" => "iwhosting", "password" => "dekkker"),
			"link_iw" => array ("database" => "", "host" => "10.13.120.254", "user" => "imp", "password" => "imp"),
			"crones" => array ("database" => "impresiones_new", "host" => "10.13.120.253", "user" => "imp", "password" => "imp"),
			"link_mb" => array ("database" => "mobusi_new", "host" => "localhost", "user" => "mob", "password" => "mob"),
			"link_mobusi" => array ("database" => "mobusi", "host" => "10.13.120.252", "user" => "imp", "password" => "imp"),
			"link_mobusi_new" => array ("database" => "mobusi_new", "host" => "10.13.120.252", "user" => "mob", "password" => "mob"),
			"media_new" => array ("database" => "media_new", "host" => "10.13.120.217", "user" => "imp", "password" => "imp"),
			"codex_mobusi" => array ("database" => "mobusi_new", "host" => "10.13.120.217", "user" => "imp", "password" => "imp"),

			"iw_adserver_new" => array ("database" => "iwadserver_new", "host" => "10.13.120.252", "user" => "imp", "password" => "imp"),
			"iw_adserver_local" => array ("database" => "iwadserver_new", "host" => "localhost", "user" => "imp", "password" => "imp"),
			"codex_iwadserver" => array ("database" => "iwadserver_new", "host" => "10.13.120.217", "user" => "imp", "password" => "imp"),

			"impresiones" => array ("database" => "impresiones", "host" => "10.13.120.254", "user" => "imp", "password" => "imp"),
		);

	/**
	 * Constructor
	 *
	 * @param string $tipo
	 * @param bool $log  (optional)
	 */
	public function __construct($tipo="", $log=false) {
		if ($tipo <> "") $this->dblink = $this->Conectar($tipo);
	}


	/**
	 *
	 *
	 * @param unknown $tipo
	 * @return unknown
	 */
	public function Conectar($tipo) {
		$database = CDatabase::$database_config[$tipo];
		$linku=mysql_connect($database["host"], $database["user"], $database["password"]);
		if (isset($this)) $this->log("Conecting to: ", $database);
		if (!empty($database["database"])) mysql_select_db($database["database"], $linku);
		return $linku;
	}

	/**
	 * Devuelve un
	 */

	public function ConectarFrontal($ip, $user, $pass, $database) {
		$linku=mysql_connect($ip, $user, $pass);
		mysql_select_db($database, $linku);
		if (isset($this))  {
			$this->dblink = $linku;
			$this->log("Conecting to frontal: ", $ip);
		}
	}


	/**
	 *
	 *
	 * @param unknown $dato
	 * @param unknown $tabla
	 * @param unknown $filtro
	 * @return unknown
	 */
	public function SacaDato($dato, $tabla, $filtro, $mysql=null) {
		if (is_null($mysql)) $mysql = $this->dblink;
		$sql = "SELECT ".$dato." FROM ".$tabla." WHERE ".$filtro." LIMIT 1";

		if (isset($this)) $result=$this->PConsulta($sql, $mysql);
		else $result=CConfig::PConsulta($sql, $mysql);

		if (is_null($result) || !$result) return false;
		if (mysql_num_rows($result)) $dato=mysql_result($result, 0, 0);
		else $dato=false;
		if (isset($this)) if ($this->log) echo $sql."<br>";

		return $dato;
	}


	/**
	 *
	 *
	 * @param unknown $dato
	 * @param unknown $tabla
	 * @param unknown $filtro
	 * @return unknown
	 */
	public function SacaDatos($dato, $tabla, $filtro="", $mysql=null) {
		if (is_null($mysql)) $mysql = $this->dblink;
		$sql = "SELECT ".$dato." FROM ".$tabla." WHERE ".$filtro." LIMIT 1";
		
		if (isset($this)) $result=$this->PConsulta($sql, $mysql);
		else $result=CConfig::PConsulta($sql, $mysql);

		if (is_null($result) || !$result) return false;
		if (mysql_num_rows($result)) $datos=mysql_fetch_assoc($result);
		else $datos=false;
		if (isset($this)) if ($this->log) echo $sql."<br>";

		return $datos;
	}


	/**
	 *
	 *
	 * @param unknown $datos
	 * @param unknown $tabla
	 * @param unknown $filtro
	 * @return unknown
	 */
	public function SacaArray($datos, $tabla, $filtro, $mysql=null) {
		if (is_null($mysql)) $mysql = $this->dblink;
		$campos=explode(",", $datos); $indice=$campos[0]; $value=$campos[1];

		if (isset($this)) $result=$this->PConsulta("SELECT ".$indice.", ".$value." FROM ".$tabla." WHERE ".$filtro." ORDER by ".$indice."", $mysql);
		else $result=CConfig::PConsulta("SELECT ".$indice.", ".$value." FROM ".$tabla." WHERE ".$filtro." ORDER by ".$indice."", $mysql);

		if (is_null($result)) return false;
		for ($i=0;$i<mysql_num_rows($result);$i++) $array[mysql_result($result, $i, 0)]=mysql_result($result, $i, 1);
		return $array;
	}

	/**
	 *
	 *
	 * @param unknown $dato
	 * @param unknown $tabla
	 * @param unknown $filtro
	 * @return unknown
	 */
	public function SacaDatoLista($dato, $tabla, $filtro, $mysql=null) {
		if (is_null($mysql)) $mysql = $this->dblink;

		if (isset($this)) $result=$this->PConsulta("SELECT ".$dato." FROM ".$tabla." WHERE ".$filtro."", $mysql);
		else $result=CConfig::PConsulta("SELECT ".$dato." FROM ".$tabla." WHERE ".$filtro."", $mysql);

		if (is_null($result)) return false;
		for ($i=0;$i<mysql_num_rows($result);$i++) {
			if ($i==0) $datos=mysql_result($result, $i, 0);
			else $datos.=",".mysql_result($result, $i, 0);
		}
		return $datos;
	}


	/**
	 *
	 *
	 * @param unknown $cadena
	 * @return unknown
	 */
	public function PConsulta($cadena, $mysql=null, $nolog=false) {
		if (is_null($mysql)) $mysql = $this->dblink;
		$result=mysql_query($cadena, $mysql);
		if (!$result) {
			if (!$nolog) {
				$err = mysql_error();
				$errno = mysql_errno();
				if (!empty($err) && !in_array($errno, array(1062))) {
					$sql = " insert into `mysql_error` (`sql`, errno, errstr, backtrace, get, server) values (".
							" '". mysql_escape_string($cadena). "', ".
							$errno. ", ".
							" '". mysql_escape_string($err). "', ".
							" '". mysql_escape_string(preg_replace('/\s\s+/', ' ', str_replace(array("\n", "\t", "\r"), '', print_r(debug_backtrace(), true)))). "',".
							" '". mysql_escape_string(preg_replace('/\s\s+/', ' ', str_replace(array("\n", "\t", "\r"), '', print_r($_GET, true)))). "',".
							" '". mysql_escape_string(preg_replace('/\s\s+/', ' ', str_replace(array("\n", "\t", "\r"), '', print_r($_SERVER, true)))). "'".
							");";
					mysql_query($sql, $mysql);
				}
			}
		}
		if (isset($this)) $this->log($cadena);
		return $result;
	}


	/**
	 *
	 *
	 * @param unknown $tabla
	 * @param unknown $campos
	 * @param unknown $filtro
	 */
	public function PModifica($tabla, $campos, $filtro, $mysql=null) {
		$cadena="UPDATE $tabla SET ";

		// concatenamos las claves y valores
		while ($registro=each($campos)) {
			$cadena.=$registro["key"]."='".mysql_escape_string($registro["value"])."',";
		}
		$cadena=substr($cadena, 0, -1);

		// concatenamos el filtro
		$cadena.=" WHERE $filtro";
		if (isset($this)) {
			if ($this->log) echo $cadena."<br>";
			$this->PConsulta($cadena, $mysql);
		} else 	$bResult=CConfig::PConsulta($cadena, $mysql);
	}


	/**
	 *
	 *
	 * @param unknown $tabla
	 * @param unknown $campos
	 */
	public function PInserta($tabla, $campos, $mysql=null) {
		if (is_null($mysql)) $mysql = $this->dblink;
		$cadena="INSERT INTO $tabla (";

		// concatenamos las claves
		while ($registro=each($campos)) {
			$cadena.=$registro["key"].",";
		}
		$cadena=substr($cadena, 0, -1);

		// concatenamos los valores
		$cadena.=") VALUES (";
		reset($campos);
		while ($registro=each($campos)) {
			$cadena.="'".mysql_escape_string($registro["value"])."',";
		}
		$cadena=substr($cadena, 0, -1);
		$cadena.=")";

		if (isset($this)) {
			if ($this->log) echo $cadena."<br>";
			$bResult=$this->PConsulta($cadena, $mysql);
		} else $bResult=CConfig::PConsulta($cadena, $mysql);
		
	}

	/**
	 *
	 *
	 * @param unknown $tabla
	 * @param unknown $campos
	 */
	public function PInsertUpdate($tabla, $insert, $update, $mysql=null) {
		if (is_null($mysql)) $mysql = $this->dblink;
		$cadena=" INSERT INTO $tabla (". implode(",", array_keys($insert)).")";


		$values = array();
		foreach (array_values($insert) as $value) $values[]="'". mysql_escape_string($value)."'";
		$cadena.=" VALUES (". implode(",", $values). ")";

		$cadena.=" ON DUPLICATE KEY UPDATE ". implode(",", $update);

		if (isset($this)) {
			if ($this->log) echo $cadena."<br>";
			return $this->PConsulta($cadena, $mysql);
		} else return CConfig::PConsulta($cadena, $mysql);
	}


	/**
	 *
	 *
	 * @param unknown $tabla
	 * @param unknown $campos
	 * @param unknown $log    (optional)
	 */
	public function PReplace($tabla, $campos, $log=0, $mysql=null) {
		if (is_null($mysql)) $mysql = $this->dblink;
		$cadena="REPLACE INTO $tabla (";

		// concatenamos las claves
		while ($registro=each($campos)) {
			$cadena.=$registro["key"].",";
		}
		$cadena=substr($cadena, 0, -1);

		// concatenamos los valores
		$cadena.=") VALUES (";
		reset($campos);
		while ($registro=each($campos)) {
			if ($registro["key"]==$registro["value"]) $cadena.=$registro["value"].",";
			else $cadena.="'".mysql_escape_string($registro["value"])."',";
		}
		$cadena=substr($cadena, 0, -1);
		$cadena.=")";

		if ($log==1) echo $cadena;
		if (isset($this)) {
			$bResult=$this->PConsulta($cadena, $mysql);
		} else $bResult=CConfig::PConsulta($cadena, $mysql);
		
	}

	public function DesConectar($mysql=null) {
		if (is_null($mysql)) $mysql = $this->dblink;
		mysql_close($mysql);
	}

#--------------------------------
# Funciones antiguas
#

	public function ConectarIW($link_iw) {
		$link_iw=mysql_connect("10.13.120.254", "imp", "imp");
	}


	public function log ($msg, $arrdata = null) {
		return;
		if (!defined("LOG_TO_FILE")) return;
		$f = fopen(sprintf("/tmp/%s_database.log", date("Ymd")), "a+");
		$logline = date("Ymd H.i:s"). " ". $msg;
		if (!is_null($arrdata)) $logline.=  str_replace(array("\n", "\t", "\r"), '', print_r($arrdata, true));
		fwrite ($f, $logline. "\n");
		fclose($f);
	}


}


?>
