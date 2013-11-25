<?php /*%%SmartyHeaderCode:8722527b66ebc0fb63-90503463%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c1330b55f35665f430ce9b8167feebce9b68bc0a' => 
    array (
      0 => 'template\\index.tpl',
      1 => 1383818121,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8722527b66ebc0fb63-90503463',
  'variables' => 
  array (
    'model' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.15',
  'unifunc' => 'content_527b66ebc59f01_76478177',
  'cache_lifetime' => '120',
),true); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_527b66ebc59f01_76478177')) {function content_527b66ebc59f01_76478177($_smarty_tpl) {?><!DOCTYPE html>
<html>
<head>
  <title></title>
</head>
<body>
object(mvc\Model)#27 (4) {
  ["@attributes"]=>
  array(1) {
    ["request"]=>
    string(32) "0b794a03744a03800313ca0f2e291294"
  }
  ["data"]=>
  object(mvc\Model)#41 (2) {
    ["arraypruebas"]=>
    array(3) {
      [0]=>
      string(8) "un texto"
      [1]=>
      string(10) "otro texto"
      [2]=>
      string(1) "3"
    }
    ["otroarraypruebas"]=>
    object(mvc\Model)#42 (1) {
      ["@attributes"]=>
      array(2) {
        ["txt"]=>
        string(13) "Ejemplo texto"
        ["numero"]=>
        string(4) "2134"
      }
    }
  }
  ["messages"]=>
  object(mvc\Model)#52 (1) {
    ["message"]=>
    array(2) {
      [0]=>
      object(mvc\Model)#42 (1) {
        ["@attributes"]=>
        array(2) {
          ["type"]=>
          string(4) "info"
          ["msg"]=>
          string(20) "Entrando en el Index"
        }
      }
      [1]=>
      object(mvc\Model)#43 (1) {
        ["@attributes"]=>
        array(2) {
          ["type"]=>
          string(5) "error"
          ["msg"]=>
          string(16) "Prueba de Error "
        }
      }
    }
  }
  ["request"]=>
  object(mvc\Model)#53 (1) {
    ["@attributes"]=>
    array(4) {
      ["id"]=>
      string(32) "0b794a03744a03800313ca0f2e291294"
      ["module"]=>
      string(1) "/"
      ["controller"]=>
      string(5) "index"
      ["action"]=>
      string(7) "default"
    }
  }
}

</body>
</html><?php }} ?>
