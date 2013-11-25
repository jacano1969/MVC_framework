<?php

namespace mvc\views\renderers;

use mvc\App;
use mvc\Model;
use mvc\views\View;
use mvc\views\ViewRenderer;
use io\File;

/**
 * Smarty View Renderer. This renderer
 */
class SmartyViewRenderer extends ViewRenderer {

	protected  $ext = '.tpl';

	/**
	 * This View Renderer doesn't have any real view data.
	 *
	 * @param string $data
	 * @param string $uri
	 * @return string
	 */
	public function getViewData( $data, $uri=null ) {
		return $data;
	}

	/**
	 * Renders the provided Model as an XML document.
	 *
	 * @param Controller $controller
	 * @param Model $model
	 * @param View $view
	 */
	public function render( Model $model, View $view ) {
		$config_smarty=App::getContext()->getSmartyConfig();
		try{

			// need this to avoid Smarty rely on spl autoload function,
			// this has to be done since we need the Yii autoload handler
			if (!defined('SMARTY_SPL_AUTOLOAD')) {
				define('SMARTY_SPL_AUTOLOAD', 0);
			} elseif (SMARTY_SPL_AUTOLOAD !== 0) {
				throw new CException('ESmartyViewRenderer cannot work with SMARTY_SPL_AUTOLOAD enabled. Set SMARTY_SPL_AUTOLOAD to 0.');
			}

			require(APP_PATH . '/clases/'.$config_smarty['path'].'/sysplugins/smarty_internal_data.php');
			require(APP_PATH . '/clases/'.$config_smarty['path'].'/Smarty.class.php');

			spl_autoload_unregister('smartyAutoload');
			registerAutoloader('smartyAutoload');

			$smarty = new \Smarty();
			$smarty->setTemplateDir($config_smarty['template_path']);
			$smarty->setCompileDir($config_smarty['compile_path']);
			$smarty->setCacheDir($config_smarty['cache_path']);

			$smarty->force_compile = $config_smarty['force_compile'];
			$smarty->debugging = App::getContext()->getDebug();
			$smarty->debugging = $config_smarty['debug'];
			$smarty->caching = $config_smarty['caching'];
			$smarty->cache_lifetime = $config_smarty['cache_lifetime'];

			$filetemplate=isset($model->request->getAttributes()['filetemplate'])?$model->request->getAttributes()['filetemplate']:$model->request->getAttributes()['controller'];

//			$smarty->testInstall();
//			$smarty->createTemplate($filetemplate.$this->getExt(),null,null,$model,false);

//			$smarty->registered_objects('model', $model);
			$smarty->assignByRef('model', $model);
			$smarty->display($filetemplate.$this->getExt());

		} catch ( Exception $e ) {
				$this->Exception($e);
		}


    }

	public function getExt(){
		return $this->ext;
	}
	public function setExt(string $extension){
		$this->ext=$extension;
	}

	/**
	 * Renders the provided Exception and return the result
	 *
	 * @param \Exception $exception
	 * @return string
	 */
	public function Exception( \Exception $exception ) {
		return $exception->getMessage();
	}
}