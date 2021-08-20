<?php
require 'main_app/conexion.php';
Class Seguridad{
    $connect = conectar(); 
    $stmt ="select * from AXESO where USUARIO  = '$usuario' AND CLAVE = '$pass' "  ;  
    $query = ibase_prepare($stmt);  
    $v_query = ibase_execute($query);  
    $v_reg = ibase_fetch_row($v_query); 
    ibase_free_query($query); 
    private $usuario = null;
        
        function _construct()
            {
                session_start();
                if(isset($_SESSION['usuario'])) $this->$usuario=$_SESSION['usuario'];
        }
        
        public function getUsuario(){
            return $usuario;
        }
}
    
?>