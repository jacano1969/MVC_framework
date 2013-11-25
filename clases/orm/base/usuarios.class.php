<?php
/**
 * This file has been auto-generated by ORM Generator
 * 
 * @version $Rev$
 */

namespace orm\base;

use \mvc\App;
use \sql\ConnectionFactory;
use \orm\ActiveRecord;

/**
 * Auto-generated usuarios class. DO NOT MODIFY THIS FILE. It will be recreated each time you regenerate the ORM.
 *
 */
class usuarios extends ActiveRecord {


	protected static $conn = null;

	protected static $schema = 'impresionesweb';

	protected static $table = 'usuarios';

	protected static $fields = array(
			  'idusuario'            => 'idusuario'
			, 'login'                => 'login'
			, 'password'             => 'password'
			, 'estado'               => 'estado'
			, 'perfil'               => 'perfil'
			, 'nombre'               => 'nombre'
			, 'email'                => 'email'
			, 'idioma'               => 'idioma'
			, 'opciones'             => 'opciones'
			, 'monederoActual'       => 'monedero_actual'
			, 'monederoFacturacion'  => 'monedero_facturacion'
			, 'tipo'                => 'tipo'
			, 'empresa'             => 'empresa'
			, 'direccion'           => 'direccion'
			, 'localidad'           => 'localidad'
			, 'provincia'           => 'provincia'
			, 'codigoPostal'        => 'codigo_postal'
			, 'pais'                => 'pais'
			, 'cif'                 => 'cif'
			, 'nif'                 => 'nif'
			, 'fechaRegistro'       => 'fecha_registro'
			, 'fechaUltimoAcceso'   => 'fecha_ultimo_acceso'
			, 'ipUltimoAcceso'      => 'ip_ultimo_acceso'
			, 'telefono'            => 'telefono'
			, 'movil'               => 'movil'
			, 'url'                 => 'url'
			, 'cuentaBancaria'      => 'cuenta_bancaria'
			, 'banco'               => 'banco'
			, 'direccionBanco'      => 'direccion_banco'
			, 'swift'               => 'swift'
			, 'iban'                => 'iban'
			, 'cuentaPaypal'        => 'cuenta_paypal'
			, 'metodoPago'          => 'metodo_pago'
			, 'tipoPago'            => 'tipo_pago'

	);

	protected static $pkFields = array(
			  'idusuario'           
			);

	protected static $fkFields = array(
			);

	protected static $sequences = array(
			  'auto_increment        ' => 'idusuario'
			);

	protected $values = array(
			  'idusuario'            => null
			, 'login'                => null
			, 'password'             => null
			, 'estado'               => null
			, 'perfil'               => null
			, 'nombre'               => null
			, 'email'                => null
			, 'idioma'               => null
			, 'opciones'             => null
			, 'monederoActual'       => null
			, 'monederoFacturacion'  => null
			, 'tipo'                => null
			, 'empresa'             => null
			, 'direccion'           => null
			, 'localidad'           => null
			, 'provincia'           => null
			, 'codigoPostal'        => null
			, 'pais'                => null
			, 'cif'                 => null
			, 'nif'                 => null
			, 'fechaRegistro'       => null
			, 'fechaUltimoAcceso'   => null
			, 'ipUltimoAcceso'      => null
			, 'telefono'            => null
			, 'movil'               => null
			, 'url'                 => null
			, 'cuentaBancaria'      => null
			, 'banco'               => null
			, 'direccionBanco'      => null
			, 'swift'               => null
			, 'iban'                => null
			, 'cuentaPaypal'        => null
			, 'metodoPago'          => null
			, 'tipoPago'            => null
	);

	protected $fkValues = array(
			);

	/**
	 * Performs static initialization
	 */
	public static function __static() {
		self::$conn = ConnectionFactory::getConnection('DEFAULT');
	}

}