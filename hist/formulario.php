<?php 
require 'main_app/main.php';

/*include_once "seguridad.php";
    $seguridad=new Seguridad();
    if($seguridad->getUsuario()==null){
        header('Location:index.php');
        exit;
    }
*/
if( isset($_REQUEST['logout']) ){
    unset($_SESSION['usuario']);
    header('Location:index.php');
}



if ( isset($_GET["validaciones"]) && !empty($_GET["validaciones"]) ){
    
    $connect = conectar();
    
    if ( $_GET["validaciones"] == "recibo_entrega" ){
        
        $intRecibo = isset($_GET["recibo"]) ? intval($_GET["recibo"]) : 0;
        
        $boolExisteTicket = false;
        
        if ($intRecibo){
            
            $stmt ="select * from TICKETS where DT_NUMERO  = '{$intRecibo}' "  ;  
            $query = ibase_prepare($stmt);  
            $v_query = ibase_execute($query);  
            $v_reg = ibase_fetch_row($v_query); 
            ibase_free_query($query); 
            if ( is_array($v_reg)  &&  count($v_reg) > 0){  
                $boolExisteTicket = true;
            }
            
        }
        
        $strTextoFinal = $boolExisteTicket ? "Y" : "N";
        
        //print $strTextoFinal;
        
    }

    elseif ( $_GET["validaciones"] == "cliente" ){
        
        $strBusqueda = isset($_GET["term"]) ? utf8_decode(trim($_GET["term"])) : "";
        $strBusqueda = strtoupper($strBusqueda);
        
        $arrInfo = array();
        $stmt ="select niu, nombre from CLIENTES where UPPER(nombre) LIKE '%{$strBusqueda}%' "  ;  
        $query = ibase_prepare($stmt);  
        $v_query = ibase_execute($query);  
        while ( $rTMP = ibase_fetch_assoc($v_query) ){
            
            $arrInfo[$rTMP["NIU"]]["key"] = $rTMP["NIU"];
            $arrInfo[$rTMP["NIU"]]["nombre"] = trim($rTMP["NOMBRE"]);
            
            
        }
        ibase_free_result($v_query);
        
        $result = array();
        if ( is_array($arrInfo) && ( count($arrInfo) > 0 ) ){
            
            reset($arrInfo);
            foreach ( $arrInfo  as $rTMP["key"] => $rTMP["value"] ){
                
                $arrTMP = array();
                $arrTMP["id"] = utf8_encode($rTMP["key"]);
                $arrTMP["value"] = utf8_encode($rTMP["key"]." - ".$rTMP["value"]["nombre"]);
                $arrTMP["niu"] = utf8_encode($rTMP["key"]);
                                                                                                                                 
                array_push($result, $arrTMP);
                
            }
        }
        
        print json_encode($result);
        
    }
    elseif ( $_GET["validaciones"] == "producto" ){
        
        $strBusqueda = isset($_GET["term"]) ? utf8_decode(trim($_GET["term"])) : "";
        $strBusqueda = strtoupper($strBusqueda);
        
        $strFecha = isset($_GET["fecha"]) ? trim($_GET["fecha"]) : "";
        
        $arrInfo = array();
        
        if ( !empty($strFecha) ){
            
            $boolFirstTime = true;
            $sinPrecio = 0;
            $stmt = "SELECT PRECIO FROM PRECOMB WHERE DESDE <= '{$strFecha}' AND HASTA >= '{$strFecha}' ORDER BY DESCRIP DESC";
            $query = ibase_prepare($stmt);  
            $v_query = ibase_execute($query);  
            while ( $rTMP = ibase_fetch_assoc($v_query) ){

                if ( $boolFirstTime ){
                    $sinPrecio = floatval($rTMP["PRECIO"]);
                }
                
                $boolFirstTime = false;
                
            }
            ibase_free_result($v_query);
            
            $stmt ="SELECT O.PROD_NIU, O.DESCRIP, O.CODIGO, O.EXISTENC, O.NUMORDEN, O.PROVEEDOR, O.NIU_COMBUSTIBLES, O.CTRLEXIS, O.EXISTENCD, O.EXISTENCQ, O.MONEDA, O.TASACOMP, O.PCOSTOQ, O.PCOSTOD, P.PRECIO
                    FROM PRODUCTO2 O
                    JOIN PRECOMB P
                    ON O.NIU_COMBUSTIBLES = P.NIU_COMBUSTIBLES
                    WHERE O.EXISTENC > 0.001 AND P.DESDE <= '{$strFecha}' AND P.HASTA >= '{$strFecha}' OR O.CTRLEXIS = 0 ORDER BY 2 LIKE '%{$strBusqueda}%'"  ;  
            $query = ibase_prepare($stmt);  
            $v_query = ibase_execute($query);  
            while ( $rTMP = ibase_fetch_assoc($v_query) ){

                $arrInfo[$rTMP["PROD_NIU"]]["id"] = $rTMP["PROD_NIU"];
                $arrInfo[$rTMP["PROD_NIU"]]["nombre"] = trim($rTMP["DESCRIP"]);
                $arrInfo[$rTMP["PROD_NIU"]]["codigo"] = trim($rTMP["CODIGO"]);
                $arrInfo[$rTMP["PROD_NIU"]]["existenc"] = $rTMP["EXISTENC"];
                $arrInfo[$rTMP["PROD_NIU"]]["numorden"] = $rTMP["NUMORDEN"];
                $arrInfo[$rTMP["PROD_NIU"]]["proveedor"] = $rTMP["PROVEEDOR"];
                $arrInfo[$rTMP["PROD_NIU"]]["galones"] = $rTMP["EXISTENC"]; 
                $arrInfo[$rTMP["PROD_NIU"]]["precio"] = $sinPrecio; 
                $arrInfo[$rTMP["PROD_NIU"]]["combustibles"] = $rTMP["NIU_COMBUSTIBLES"];
                $arrInfo[$rTMP["PROD_NIU"]]["ctrlexis"] = $rTMP["CTRLEXIS"];
                $arrInfo[$rTMP["PROD_NIU"]]["existencd"] = $rTMP["EXISTENCD"];
                $arrInfo[$rTMP["PROD_NIU"]]["existencq"] = $rTMP["EXISTENCQ"];
                $arrInfo[$rTMP["PROD_NIU"]]["moneda"] = $rTMP["MONEDA"];
                $arrInfo[$rTMP["PROD_NIU"]]["tasacomp"] = $rTMP["TASACOMP"];
                $arrInfo[$rTMP["PROD_NIU"]]["pcostoq"] = $rTMP["PCOSTOQ"];
                $arrInfo[$rTMP["PROD_NIU"]]["pcostod"] = $rTMP["PCOSTOD"];
                $arrInfo[$rTMP["PROD_NIU"]]["precio"] = $rTMP["PRECIO"];

            }
            ibase_free_result($v_query);
            
        }
            
        $result = array();
        
        if ( is_array($arrInfo) && ( count($arrInfo) > 0 ) ){
            
            reset($arrInfo);
             foreach ( $arrInfo  as $rTMP["key"] => $rTMP["value"] ){
                
                $strVAlorHidden = $rTMP["key"]." - ".$rTMP["value"]["codigo"]." - ".$rTMP["value"]["nombre"]." - ".$rTMP["value"]["existenc"]." - ".$rTMP["value"]["galones"]." - ".$rTMP["value"]["precio"]." - ".$rTMP["value"]["combustibles"]." - ".$rTMP["value"]["ctrlexis"]." - ".$rTMP["value"]["existencd"]." - ".round($rTMP["value"]["existencq"], 2)." - ".$rTMP["value"]["moneda"]." - ".$rTMP["value"]["tasacomp"];

                $arrTMP = array();
                $arrTMP["id"] = utf8_encode($rTMP["key"]);
                $arrTMP["value"] = utf8_encode($rTMP["value"]["numorden"]." - ".$rTMP["value"]["codigo"]." - ".$rTMP["value"]["existenc"]);
                $arrTMP["valor_hidden"] = utf8_encode($strVAlorHidden);
                $arrTMP["galon"] = utf8_encode($rTMP["value"]["galones"]);
                $arrTMP["galon"] = utf8_encode($rTMP["value"]["galones"]);
                $arrTMP["pcostoquetzal"] = utf8_encode($rTMP["value"]["pcostoq"]);
                $arrTMP["pcostodolar"] = utf8_encode($rTMP["value"]["pcostod"]);
                $arrTMP["precio"] = utf8_encode($rTMP["value"]["precio"]);

                array_push($result, $arrTMP);

            }
        }
        
        print json_encode($result);
        
    }
    elseif ( $_GET["validaciones"] == "aeronave" ){
        
        $strBusqueda = isset($_GET["term"]) ? utf8_decode(trim($_GET["term"])) : "";
        $intClienteNiU = isset($_GET["idClienteNiU"]) ? intval($_GET["idClienteNiU"]) : "";
        $strBusqueda = strtoupper($strBusqueda);
        
        $arrInfo = array();
        $stmt ="select niu, tipo, matricula 
                from AERONAVES 
                where niu_cliente = '$intClienteNiU' 
                and UPPER(tipo) 
                LIKE '%{$strBusqueda}%' "  ;  
        $query = ibase_prepare($stmt);  
        $v_query = ibase_execute($query);  
        while ( $rTMP = ibase_fetch_assoc($v_query) ){
            
            $arrInfo[$rTMP["NIU"]]["id"] = $rTMP["NIU"];
            $arrInfo[$rTMP["NIU"]]["tipo"] = trim($rTMP["TIPO"]);
            $arrInfo[$rTMP["NIU"]]["matricula"] = trim($rTMP["MATRICULA"]);
            
        }
        ibase_free_result($v_query);
        
        $result = array();
        if ( is_array($arrInfo) && ( count($arrInfo) > 0 ) ){
            
            reset($arrInfo);
             foreach ( $arrInfo  as $rTMP["key"] => $rTMP["value"] ){
                
                $arrTMP = array();
                $arrTMP["id"] = utf8_encode($rTMP["key"]);
                $arrTMP["value"] = utf8_encode($rTMP["value"]["matricula"]);
                $arrTMP["tipo"] = utf8_encode($rTMP["value"]["tipo"]);
                $arrTMP["niu"] = utf8_encode($rTMP["key"]);
                                                                                                                                 
                array_push($result, $arrTMP);
                
            }
        }
        
        print json_encode($result);
        
    }
    elseif ( $_GET["validaciones"] == "eliminar" ){
        
        $intNiU = isset($_GET["key"]) ? intval($_GET["key"]) : 0;
        
        if ( intval($intNiU) ){
            
            $stmt = "DELETE FROM TICKETS WHERE DT_NIU = '{$intNiU}'";
            $query = ibase_prepare($stmt);  
            $v_query = ibase_execute($query);
            
        }
        
    }
    
    die();
    
}
else if ( isset($_GET["busqueda_registro"]) && ( $_GET["busqueda_registro"] == "true" ) ){
    $connect = conectar();
    $strFecha1 = isset($_POST["fecha_uno"]) ? trim($_POST["fecha_uno"]) : "";
    $strFecha2 = isset($_POST["fecha_dos"]) ? trim($_POST["fecha_dos"]) : "";
    $strBuqueda = isset($_POST["busqueda"]) ? trim($_POST["busqueda"]) : "";
    $strFechaUno = date ("Y/m/d", strtotime($strFecha1));
    $strFechaDos = date ("Y/m/d", strtotime($strFecha2));
    
    $strFilter = "";
    if ( !empty($strBuqueda) ){
        $strFilter = " AND (UPPER(C.NOMBRE) LIKE '%{$strBuqueda}%' OR UPPER(P.PROVEEDOR) LIKE '%{$strBuqueda}%' OR UPPER(T.DT_NUMERO) LIKE '%{$strBuqueda}%' ) ";
        
        
    }
    
    $arrInfo = array();
    $stmt ="SELECT T.DT_NIU, T.NIU_CLIENTE, T.DT_NUMERO, T.FECHA_DT, T.NOMBRE, T.CANTIDAD, T.PRODUCTO ,T.CAMION , T.MATRICULA, C.NIU, C.NOMBRE, A.DT_NIU, A.PROD_NIU, P.PROD_NIU, P.CODIGO, P.PROVEEDOR
            FROM TICKETS T
            LEFT JOIN CLIENTES C
            ON C.NIU = T.NIU_CLIENTE
            LEFT JOIN AJUSTE_ENTRE_PEDIDOS A
            ON A.DT_NIU = T.DT_NIU
            LEFT JOIN PRODUCTO2 P
            ON P.PROD_NIU = A.PROD_NIU
            WHERE   P.PROVEEDOR IS NOT NULL
            AND  T.FECHA_DT >= '{$strFechaUno}'
            AND  T.FECHA_DT <= '{$strFechaDos}'
            {$strFilter}
            ORDER BY T.FECHA_DT DESC "  ;  
    
    
    $query = ibase_prepare($stmt);  
    $v_query = ibase_execute($query); 
    $intCount = 0;
    while($rTMP = ibase_fetch_assoc($v_query)){
        /*print '<pre>';
        print_r($rTMP);
        print '</pre>';*/
        $arrInfo[$intCount]["NIU"] = $rTMP["NIU"];
        $arrInfo[$intCount]["DT_NIU"] = $rTMP["DT_NIU"];
        $arrInfo[$intCount]["DT_NUMERO"] = $rTMP["DT_NUMERO"];
        $arrInfo[$intCount]["NOMBRE"] = $rTMP["NOMBRE"];
        $arrInfo[$intCount]["FECHA_DT"] = $rTMP["FECHA_DT"];
        $arrInfo[$intCount]["PROVEEDOR"] = $rTMP["PROVEEDOR"];
        $arrInfo[$intCount]["CANTIDAD"] = $rTMP["CANTIDAD"];
        $arrInfo[$intCount]["CAMION"] = $rTMP["CAMION"];
        $arrInfo[$intCount]["MATRICULA"] = $rTMP["MATRICULA"];
        $intCount++;
    }
        
    ?>
    <div class="col-md-12">
        
        <?php
        if ( is_array($arrInfo) && ( count($arrInfo) > 0 ) ){
            
            reset($arrInfo);
            foreach ( $arrInfo as $key => $value ){
              $intId = isset($value['DT_NIU'])?$value['DT_NIU']:0;
              ?>
                <div id="DivContenedor_<?php print $intId; ?>" class="panel panel-primary">
                  <div class="panel-heading  alert-info">
                    <h4 class="panel-title">
                        <a align="left" data-toggle="collapse" href="#collapse1_<?php print $intId; ?>"><?php echo $value['FECHA_DT']; ?> - <?php echo $value['DT_NUMERO']; ?> - </a> <a href="dt.php?id=<?php echo $value['DT_NUMERO']; ?>" class="btn btn-sm btn-warning right">P</a>
                    </h4>
                  </div>
                  <div id="collapse1_<?php print $intId; ?>" class="panel-collapse collapse alert-info">
                  <div class="panel-body">
                  </div>
                        <table style="width:100%">
                            <tr>
                                <td><b>No. DT:  </b><?php echo $value['DT_NUMERO']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Cliente:  </b><?php echo $value['NOMBRE']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Proveedor:  </b><?php echo $value['PROVEEDOR']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Galones:  </b><?php echo $value['CANTIDAD']; ?></td>
                           </tr>
                           <tr>
                                <td><b>Camion:  </b><?php echo $value['CAMION']; ?></td>
                           </tr>
                           
                        </table>
                        <br>
                    </div>
                </div>
            <?php
            }
            
        }
    else{
        ?>
        <div class="col-md-12">
            <div class="alert alert-danger" role="alert">
              NO EXISTE
            </div>
        </div>
        <?php
    }
            
        ?>
        
    </div>
    <?php
    
        
    
    
    die();
}

if ( isset($_POST["hidFormulario"]) ){
    $connect = conectar();
    
    $strDtNiu = "0";
    $strDtNumero = isset($_POST["recibo_entrega"]) ? trim($_POST["recibo_entrega"]) : "";
    $strFecha = isset($_POST["fecha_despacho"]) ? trim($_POST["fecha_despacho"]) : "";
    $strFechaOp = date("Y-m-d");
    
    $strTipo = isset($_POST["tipo_aeronave"]) ? trim($_POST["tipo_aeronave"]) :  "";
    $strMatricula = isset($_POST["matricula_aeronave"]) ? trim($_POST["matricula_aeronave"]) :  "";
    $intNiuAeronave = isset($_POST["niuAeronave"]) ? trim($_POST["niuAeronave"]) : 0;
    $intClienteNiu = isset($_POST["hidClienteNiu"]) ? trim($_POST["hidClienteNiu"]) : 0;
    
    $strLugarDes = isset($_POST["lugar_despacho"]) ? trim($_POST["lugar_despacho"]) : "";
    $strHoraIni = isset($_POST["hora_inicio_servicio"]) ? trim($_POST["hora_inicio_servicio"]) : "";
    $strHoraFin = isset($_POST["hora_final_servicio"]) ? trim($_POST["hora_final_servicio"]) : "";
    $strEntrega = isset($_POST["entrega"]) ? trim($_POST["entrega"]) : "";;
    $strDesc = isset($_POST["descrip"]) ? trim($_POST["descrip"]) : "";;
    $strEstado = "INI";
    $strCamion = isset($_POST["camion"]) ? trim($_POST["camion"]) : "";
    $strOwener = $_SESSION['usuario'];
    
    $strCliente = isset($_POST["hidCliente"]) ? trim($_POST["hidCliente"]) : "";
    $intPcostoQ = isset($_POST["pcostoq"]) ? trim($_POST["pcostoq"]) : "";
    $intPcostoD = isset($_POST["pcostod"]) ? trim($_POST["pcostod"]) : "";
    $arrSplit = explode(" - ", $strCliente);
    $intNiUCliente = $arrSplit[0];
    $strCliente = $arrSplit[1];
    
    $intContadorProductos = 0;
    $sinSumaPCosto = 0;
    $sinSumatoriaCantidad = 0;
    $boolFirstTime = true;
    $sinPCostoNuevo = 0;
    $sinPCostoNuevoDos = 0;
    $boolHayExistencias = false;
    reset($_POST);
    while ( $rTMP = each($_POST) ){
        
        $arrExplode = explode("_", $rTMP["key"]);
        
        if ( $arrExplode[0] == "hidproducto" ){
            $intContadorProductos++;
            
            $intCantidad = isset($_POST["galones_{$arrExplode[1]}"]) ? floatval($_POST["galones_{$arrExplode[1]}"]) : 0;
            
            $strNombre = isset($_POST["hidproducto_{$arrExplode[1]}"]) ? trim($_POST["hidproducto_{$arrExplode[1]}"]) : "";
            $arrSplit = explode(" - ", $strNombre);
            //print '</pre>';
            //print_r($arrSplit);
            //print '</pre>';
            $strNombreProducto = $arrSplit[2];
            $intProdNiU = $arrSplit[0];
            $sinPCosto = $arrSplit[5];
            $sinTasaCambio = $arrSplit[11];
            $sinValorC = $arrSplit[9];
            $sinPCostOD = $sinPCosto;
            $sinCrtExis = $arrSplit[7];
            $sinValorCD = $arrSplit[8];
            $intMoneda = $arrSplit[10];
            
            $sinSumaPCosto += $sinPCosto;
            $sinSumatoriaCantidad += $intCantidad;
            
            if ( $boolFirstTime && ( $sinCrtExis == 1 ) ){
                $sinPCostoNuevo = $sinPCosto;
                $boolFirstTime = false;
                $boolHayExistencias = true;
            }
            
            
            
        }
        
    }
    
    $sinPromedio = ($sinSumaPCosto/$intContadorProductos);
    $sinPromedio = floatval($sinPromedio);
    $sinPromedio = round($sinPromedio, 5);
    
    $sinSumatoriaCantidad = intval($sinSumatoriaCantidad);
    
    $boolMayor1 = ($intContadorProductos>1) ? true : false;
    //print ($boolHayExistencias)?'$boolHayExistencias: true ':'$boolHayExistencias : false';
    // $boolHayExistencias
    //if ( $boolHayExistencias ){
        
    $boolOnlyFirstTime = true;
    $idDtNumero = 0;
    reset($_POST);
    while ( $rTMP = each($_POST) ){

        $arrExplode = explode("_", $rTMP["key"]);
       // print '<pre>';
       // print_r($arrExplode);
       // print '</pre>';
        if ( $arrExplode[0] == "hidproducto" ){

            $intCantidad = $boolMayor1 ? $sinSumatoriaCantidad : (isset($_POST["galones_{$arrExplode[1]}"]) ? floatval($_POST["galones_{$arrExplode[1]}"]) : 0);

            $strNombre = isset($_POST["hidproducto_{$arrExplode[1]}"]) ? trim($_POST["hidproducto_{$arrExplode[1]}"]) : "";
            $arrSplit = explode(" - ", $strNombre);

            $strNombreProducto = $arrSplit[2];
            $intProdNiU = $arrSplit[0];
            $intTiketProdNiU = 0;
            //$sinPCosto = $boolMayor1 ? $sinPromedio : $arrSplit[5];
            $sinPCosto = $sinPCostoNuevo;
            $sinTasaCambio = $arrSplit[11];
            $sinNewValorC = ($intCantidad * $intPcostoQ);
            $sinPCostOD = $sinPCostoNuevoDos;
            $sinNewValorCD = ($intCantidad * $intPcostoD);
            $sinCrtExis = $arrSplit[7];
            $intMoneda = $arrSplit[10];

            if ( $boolOnlyFirstTime ){
                $intOldId = 0;
                $query = ibase_prepare('select max(DT_NIU) as OldId from TICKETS');  
                $v_query = ibase_execute($query);
                while($rTMP = ibase_fetch_assoc($v_query)){
                    $intOldId = $rTMP["OLDID"];
                }
            
            
                $stmt = "execute procedure GRABAR_TICKETS01 ('{$strDtNiu}','{$strFechaOp}','{$strDtNumero}','{$strCliente}','{$intNiUCliente}','{$strNombreProducto}','{$intCantidad}','{$strFecha}','{$strTipo}','{$strMatricula}','{$strLugarDes}','{$strHoraIni}','{$strHoraFin}','{$strCamion}','{$strEntrega}','{$strDesc}','{$strOwener}','{$intTiketProdNiU}','{$sinPCosto}','{$sinTasaCambio}','{$sinNewValorC}','{$sinPCostOD}','{$strEstado}','{$sinNewValorCD}','{$intMoneda}','{$intNiuAeronave}')";
                //print $stmt;
                
                //print '<pre>';
                //print_r($stmt. "                     stmt");
                //print '</pre><br>';
                
                $query = ibase_prepare($stmt);  
                $v_query = ibase_execute($query);
                
                $intNewId = 0;
                $query = ibase_prepare('select max(DT_NIU) as NewId from TICKETS');  
                $v_query = ibase_execute($query);
                while($rTMP = ibase_fetch_assoc($v_query)){
                    $intNewId = $rTMP["NEWID"];
                }
                if( $intNewId !=  $intOldId && $intNewId  > 0){
                    $idDtNumero = $intNewId;   
                }
            
                $boolOnlyFirstTime = false;
            
                
                //print 'ID PUTOS'.$idDtNumero.'<br><br><br><br><br><br><br>';
            }

            $sinPCosto = $arrSplit[5];
            $intCantidadAj = isset($_POST["galones_{$arrExplode[1]}"]) ? floatval($_POST["galones_{$arrExplode[1]}"]) : 0;
            $sinValorC = ($intCantidadAj * $intPcostoQ);
            $sinValorCD = ($intCantidadAj * $intPcostoD);
            
           // print '<pre>';
            //print_r($boolHayExistencias. "                     boolHayExistencias");
            //print '</pre><br>';
            
            //print '<pre>';
            //print_r($intMoneda . "                     intMoneda");
            //print '</pre><br>';
            
            //print '<pre>';
            //print_r($idDtNumero . "                     idDtNumero");
            //print '</pre><br>';
            
            if ( $boolHayExistencias ){
                 
                
                if($intMoneda == 1){

                                    /*  
                                    $stmt ="execute procedure ACTUALIZA_EXISTECIA_PRODUCTO2Q
                                            (:VCANTIDAD, :VVALORC, :VPCOSTOQ, :VPROD_NIU, :VDT_NIU, :VPCOSTODIA)
                                            RETURNING_VALUES
                                            ('{$intCantidad}','{$sinValorC}','{$sinPCosto}','{$intProdNiU}','{$strDtNumero}','{$sinPCosto}')";
                                    */

                                    $stmt ="execute procedure ACT_EXISTENCIA_PRODUCTO2QAPP
                                            ('{$intCantidadAj}','{$sinValorC}','{$sinPCosto}','{$intProdNiU}','{$idDtNumero}','{$sinPCosto}','{$sinCrtExis}')";

                                    //echo   $stmt . "    stmt ---------------- moneda 1<br>";
                                    //print '<br>'.$stmt;
                                    
                                    $query = ibase_prepare($stmt);  
                                    $v_query = ibase_execute($query);

                              /* echo   $intCantidad . "    intCantidad----CANTIDAD1<br>";
                                echo   $sinValorC . "      sinValorC----VALORC1<br>";
                                echo   $sinPCosto . "        sinPCosto----PCOSTO1<br>";
                                echo   $intProdNiU . "                 intProdNiU----PROD_NIU1<br>";
                                echo   $strDtNumero . "  strDtNumero----DT_NUMERO <br>";
                                echo   $sinCrtExis . "  sinCrtExis----CRTEXIS <br>";
                                echo   "///////////////////////////////////////////////////////////<br>";*/

                }
                if($intMoneda == 2){

                                    /*
                                    $stmt ="execute procedure ACTUALIZA_EXISTECIA_PRODUCTO2D
                                            (:VCANTIDAD, :VVALORDC, :VPCOSTOD, :VPROD_NIU, :VDT_NIU, :VPCOSTODIA)
                                            RETURNING_VALUES
                                            ('{$intCantidad}','{$sinValorCD}','{$sinPCostOD}','{$intProdNiU}','{$strDtNumero}','{$sinPCostOD}')";
                                    */

                                    $stmt ="execute procedure ACT_EXISTENCIA_PRODUCTO2DAPP
                                            ('{$intCantidadAj}','{$sinValorCD}','{$sinPCostOD}','{$intProdNiU}','{$idDtNumero}','{$sinPCostOD}','{$sinCrtExis}')";
                                    //echo   $stmt . "    stmt ---------------- moneda 2<br>";
                                    $query = ibase_prepare($stmt);  
                                    $v_query = ibase_execute($query);

                             /*  echo   $intCantidad . "    intCantidad----CANTIDAD2<br>";
                                echo   $sinValorCD . "                   sinValorCD----VALORDC2<br>";
                                echo   $sinPCostOD . "      sinPCostOD----PCOSTOD2<br>";
                                echo   $intProdNiU . "                 intProdNiU----PROD_NIU2<br>";
                                echo   $strDtNumero . "  strDtNumero----DT_NUMERO2 <br>";
                                echo   $sinCrtExis . "  sinCrtExis----CRTEXIS <br>";
                                echo   "///////////////////////////////////////////////////////////<br>";*/

                }
                
            }
            
            
            

                
/*
            echo   $strDtNiu . "   strDtNiu----DT_NIU<br>";
            echo   $strFechaOp . "    strFechaOp----FECHA_OP<br>"; 
            echo   $strDtNumero . "  strDtNumero----DT_NUMERO <br>";
            echo   $strCliente . "     strCliente----NOMBRE<br>";
            echo   $intNiUCliente . "     intNiUCliente----NIU_CLIENTE<br>";
            echo   $strNombreProducto . "       strNombreProducto----PRODUCTO<br>";
            echo   $intCantidad . "    intCantidad----CANTIDAD<br>";
            echo   $strFecha . "  strFecha----FECHA_DT<br>";
            echo   $strTipo . "   strTipo----TIPO<br>";
            echo   $strMatricula . "    strMatricula----MATRICULA<br>";
            echo   $strLugarDes . "   strLugarDes----LUGARDES<br>";
            echo   $strHoraIni . "   strHoraIni----HORAINI<br>";
            echo   $strHoraFin . "     strHoraFin----HORAFIN<br>";
            echo   $strCamion . "       strCamion----CAMION<br>";
            echo   $strEntrega . "     strEntrega----ENTREGA<br>";
            echo   $strDesc . "           strDesc----OBSERVAC<br>";
            echo   $strOwener . "                  strOwener----OWNER<br>";
            echo   $intProdNiU . "                 intProdNiU----PROD_NIU<br>";
            echo   $sinPCosto . "        sinPCosto----PCOSTO<br>";
            echo   $sinTasaCambio . "     sinTasaCambio----TASACAMBIO<br>";
            echo   $sinValorC . "      sinValorC----VALORC<br>";
            echo   $sinPCostOD . "      sinPCostOD----PCOSTOD<br>";
            echo   $strEstado . "       strEstado----ESTADO<br>";
            echo   $sinValorCD . "                   sinValorCD----VALORDC<br>";
            echo   $intMoneda . "      intMoneda----MONEDA<br>";
            echo   $intNiuAeronave . "      iID AERONAVE<br>";*/

        }

    }
        
    //}
    
        
    
   //die('test prueba'); 
}

?>
<!DOCTYPE html>

<html lang"es">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <link rel="stylesheet" href="css/estilos.css">
    <link rel="shortcut icon" href="images/discolsa.ico">
    <head>
       <title>INGRESO DT</title>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1">
       <style>
            body {font-family: Arial;}
           
           center{
               text-align: center;
           }
           .tab button.active {

                background-color: #b8daff;

            }
            .tab button:hover {

                background-color: #b8daff;

            }
        </style>
       
        <?php
        load_header();
        ?>
   </head>
  
   <body>
         <div class="tab alert-info ">
             
              <button class="tablinks" href="logout.php"><a href="logout.php" class="alert alert-danger">SALIR</a></button>
             <button class="tablinks alert-primary" onclick="openCity(event, 'ing')" >INGRESOS </button>
              <button class="tablinks alert-primary" onclick="openCity(event, 'reg')" >REGISTROS</button>
              
        </div>
        
        <div id="ing" class="tabcontent">
      <form action="formulario.php" method="post" class="alert-primary">
           <input type="hidden" name="hidFormulario" value="1">
          <P></P>
          <div class="form-group row">
            <label for="inputPassword" class="col col-form-label">Recibo De Entrega:</label>
            <div class="col">
              <input type="tel" id="recibo_entrega" name="recibo_entrega" onchange="fntValidacionRecibo();" class="form-control" required>
              <div id="divErrorRecciboEntrega" style="display:none;" class="alert alert-danger">El número de recibo que desea colocar ya está siendo utilizado.</div>
            </div>
          </div>   
          <div class="form-group row">
            <label for="inputPassword" class="col col-form-label">Fecha De Despacho:</label>
            <div class="col">
              <input type="date" name="fecha_despacho" id="fecha_despacho" onchange="fntCambioFecha();" class="form-control form-control-lg" required>
            </div>
            
          </div>
          <div class="form-group row">
            <label for="inputPassword" class="col col-form-label">Cliente:</label>
            <div class="col">
          <input type="text" name="cliente" id="cliente"  onfocus="fntAutoCompleteCliente();" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="form-control" required>
           <input type="hidden" name="hidCliente" id="hidCliente">
           <input type="hidden" name="hidClienteNiu" id="hidClienteNiu">
            </div>
          </div>        
          
          
          <label for="lname" class="text-center">Producto:</label><img src="images/green-plus-sign-md.png" onclick="fntAddProductos();" style="cursor:pointer;" width="20" height="20">

          <table id="tbProductos" width="100%">
              <tbody>
                  
              </tbody>
          </table>
         <div class="form-group row">
            <label for="inputPassword" class="col col-form-label">Cantidad:</label>
            <div class="col">
              <input type="tel" name="cantidad" id="cantidad" step="any" class="form-control" required>
            </div>
          </div>      
          
          <label for="lname">Aeronave:</label>
          <div class="form-row">
            <div class="col">
              <input type="text" name="matricula_aeronave" onfocus="fntAutoCompleteAeronave();" id="matricula_aeronave" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="form-control" required>
              <input type="hidden" name="niuAeronave" id="niuAeronave" value="">

            </div>
            <div class="col">
              <input type="text" name="tipo_aeronave" id="tipo_aeronave" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="form-control" required>
            </div>
          </div>
          <label for="lname">Lugar De Despacho:</label>
          <input type="text" name="lugar_despacho" maxlength="30" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" class="form-control"  required>
          <label for="lname">Hora Inicio / Fin de Servicio:</label>
          <div class="form-row">
            <div class="col">
              <input type="time" name="hora_inicio_servicio" class="form-control form-control-lg" required>
            </div>
            <div class="col">
              <input type="time" name="hora_final_servicio" class="form-control form-control-lg" required>
            </div>
          </div> <p></p>
          
          <div class="form-group row">
            <label for="inputPassword" class="col col-form-label">Camion:</label>
            <div class="col">
              <input type="text" name="camion" style="text-transform:uppercase;" class="form-control" required>
            </div>
          </div>
          
          <div class="form-group row">
            <label for="inputPassword" class="col col-form-label">Entrega:</label>
            <div class="col">
              <input type="text" name="entrega" style="text-transform:uppercase;" maxlength="4" class="form-control" required>
            </div>
          </div>
          
          <div class="form-group row">
            <label for="inputPassword" class="col col-form-label">Observacion:</label>
            <div class="col">
              <input type="text" name="descrip" style="text-transform:uppercase;" class="form-control">
            </div>
          </div>
                    
          <input type="hidden" name="pcostoq" id="pcostoq" value="">
          <input type="hidden" name="pcostod" id="pcostod" value="">
          <p></p>
          <input type="submit" class="btn btn-primary" value="ENVIAR" >
      </form>
      </div>
    
      <div id="reg" class="tabcontent">
      <div class="container">
          
          <div class="panel-group">
          
       
           <?php
            $connect = conectar();
              
              
              
            ?>
        <div class="form-row alert-info">
            <p></p>
            <div class="col">
                <br><input type="date" name="fecha_uno_registro" id="fecha_uno_registro" class="form-control form-control-lg"  value="<?php print date("Y-m-01"); ?>" onchange="fntBusquedaRegistro()">
            </div>
            <div class="col">
                <br><input type="date" name="fecha_dos_registro" id="fecha_dos_registro"  class="form-control form-control-lg" value="<?php print date("Y-m-d"); ?>" onchange="fntBusquedaRegistro() "><br>
            </div><br>
            <div class="col-12">
                <input type="text" name="buscar_registro"  id="buscar_registro"  class="form-control form-control-lg" placeholder="Busqueda por nombre y código" onkeyup="fntBusquedaRegistro()"><br>
            </div>
            <hr>
        </div>
            <div class="col-md-12">&nbsp;</div>
            <div class="col-md-12">
                <div id="divContentResultRegistro">&nbsp;</div>
                
            </div>
            <div class="col-md-12">&nbsp;</div>
            
            <script>
                function fntBusquedaRegistro(){
                    
                    var strFechaUno = $("#fecha_uno_registro").val();
                    var strFechaDos = $("#fecha_dos_registro").val();
                    var strBusqueda = $("#buscar_registro").val();
                    
                    //alert(strFechaUno + "                                  strFechaUno");
                    //alert(strFechaDos + "                                  strFechaDos");
                    //alert(strBusqueda + "                                  strBusqueda");
                    
                    $.ajax({
                      
                          url: "formulario.php?busqueda_registro=true",
                        data: {
                                fecha_uno:strFechaUno,
                                fecha_dos: strFechaDos,
                                busqueda: strBusqueda,
                            },
                          async: true,
                          global: false,
                          type: "post",
                          dataType: "html",
                            success: function(data) {

                              $("#divContentResultRegistro").html("");
                              $("#divContentResultRegistro").html(data);


                              return false;
                          }
                      });
                      
                }  
            
            </script>
            
           <?php
          
            ?>
            
            </div>
      </div>
      </div>
      
      <div class="imgcontainer">
            <img src="images/discolsa.jpg" alt="Avatar" class="avatar" style="width:100%;">
      </div>
    
      <script>

          function fntEliminar(intIndex){
              
              $.ajax({
                      
                  url: "formulario.php?validaciones=eliminar&key="+intIndex,
                  async: true,
                  global: false,

                  success: function(data) {
                      
                      $("#DivContenedor_"+intIndex).remove();


                      return false;
                  }
              });
              
          }
          
          function openCity(evt, cityName) {
                var i, tabcontent, tablinks;
                    tabcontent = document.getElementsByClassName("tabcontent");
                for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                }
                    tablinks = document.getElementsByClassName("tablinks");
                for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                }
                    document.getElementById(cityName).style.display = "block";
                    evt.currentTarget.className += " active";
            }
          
          function fntValidacionRecibo(){
              
                
              var objReciboEntrega = document.getElementById("recibo_entrega");
              var intRecibo = objReciboEntrega.value*1;
              
              var objdivErrorRecciboEntrega = document.getElementById("divErrorRecciboEntrega");
              objdivErrorRecciboEntrega.style.display = "none";
                
                
              if ( !isNaN(intRecibo) && (intRecibo>0) ){
                                                          
                  $.ajax({
                      
                      url: "formulario.php?validaciones=recibo_entrega&recibo="+intRecibo,
                      async: true,
                      global: false,

                      success: function(data) {
                          
                          if (data == "Y"){
                              objdivErrorRecciboEntrega.style.display = "";
                              objReciboEntrega.value = "";
                          }
                            
                            

                          return false;
                      }
                  });
                    
              }
              else{
                  objdivErrorRecciboEntrega.style.display = "";
                  objReciboEntrega.value = "";
              }
                
              return false;
            }
          
          
          function fntAutoCompleteCliente(){
              
              $( "#cliente" ).autocomplete({
                                                                 
                 source: "formulario.php?validaciones=cliente",
                 minLength: 2,
                 select: function( event, ui ) {
                     
                     var objhidCliente = document.getElementById("hidCliente");
                     var objhidClienteNiu = document.getElementById("hidClienteNiu");
                     var objcliente = document.getElementById("cliente");
                     
                     objhidCliente.value = ui.item.value;
                     objhidClienteNiu.value = ui.item.niu;
                     objcliente.value = ui.item.value;
                     
                 }
             });
          }
          
          var intProductos = 1;
          function fntAddProductos(){
              
              var strHtml = "";
                strHtml = "<tr>"+
                              "<td>"+
                                  "<input type=\"text\" name=\"producto_"+intProductos+"\" id=\"producto_"+intProductos+"\" class=\"form-control\" required>"+
                                  "<input type=\"hidden\" name=\"hidproducto_"+intProductos+"\" id=\"hidproducto_"+intProductos+"\">"+
                                  "<input type=\"hidden\" name=\"hidValidacionproducto_"+intProductos+"\" id=\"hidValidacionproducto_"+intProductos+"\">"+
                              "</td>"+
                          "</tr>"+
                          "<tr>"+
                              "<td>"+
                                  "<input type=\"tel\" name=\"galones_"+intProductos+"\" id=\"galones_"+intProductos+"\" onchange=\"fntValidacionGAlon('"+intProductos+"'); fntCalculoGalones();\" placeholder=\"Galones\" step=\"any\" class=\"form-control\" required>"+
                              "</td>"+
                          "</tr>";

                $("#tbProductos > tbody").append(strHtml);
              
              fntAutoCompleteProducto(intProductos);
              intProductos++;
          }
          
          function fntAutoCompleteProducto(intIndex){
              
              var objFecha = document.getElementById("fecha_despacho");
              var strFecha = objFecha.value;
              
              if ( strFecha != "" ){
                  
                  $( "#producto_"+intIndex ).autocomplete({

                     source: "formulario.php?validaciones=producto&fecha="+strFecha,
                     minLength: 2,
                     select: function( event, ui ) {

                         var objhidCliente = document.getElementById("hidproducto_"+intIndex);
                         var objcliente = document.getElementById("producto_"+intIndex);
                         var objgalon = document.getElementById("galones_"+intIndex);
                         var objhidValidacionproducto = document.getElementById("hidValidacionproducto_"+intIndex);
                         var objhidPcostoD = document.getElementById("pcostod");
                         var objhidPcostoQ = document.getElementById("pcostoq");
                         
                         objhidCliente.value = ui.item.valor_hidden;
                         objcliente.value = ui.item.value;
                         //objgalon.value = ui.item.galon;
                         objhidValidacionproducto.value = ui.item.galon;
                         objhidPcostoQ.value = ui.item.precio;
                         objhidPcostoD.value = ui.item.pcostodolar;

                         fntCalculoGalones();

                     }
                 });
                  
              }
                    
          }
          
          function fntValidacionGAlon(intIndex){
              
              var objgalon = document.getElementById("galones_"+intIndex);
              var objhidValidacionproducto = document.getElementById("hidValidacionproducto_"+intIndex);
              
              var sinGalon = objgalon.value*1;
              var sinValidacion = objhidValidacionproducto.value*1;
              
              var sinResultado = sinGalon>sinValidacion ? sinValidacion : sinGalon;
              
              objgalon.value = sinResultado;
                
          }
          
          function fntCambioFecha(){
              $("input[id*='galones_']").each(function(){
                  
                  var arrSplit = $(this).attr("id").split("_");
                  
                  var objgalon = document.getElementById("galones_"+arrSplit[1]);
                  var objhidValidacionproducto = document.getElementById("hidValidacionproducto_"+arrSplit[1]);
                  objgalon.value = 0;
                  objhidValidacionproducto.value = 0;

                  fntAutoCompleteProducto(arrSplit[1]);

              });
              
              fntCalculoGalones();
          }
          
          function fntCalculoGalones(){
              
              var objcantidad = document.getElementById("cantidad");
              
              var sinTotalGalones = 0;
              
              $("input[id*='galones_']").each(function(){
                  
                  var arrSplit = $(this).attr("id").split("_");

                  var objgalon = document.getElementById("galones_"+arrSplit[1]);
                  var sinGalon = objgalon.value*1;
                  
                  sinTotalGalones += sinGalon;

              });
              
              objcantidad.value = sinTotalGalones;
              
          }
          
          function fntAutoCompleteAeronave(){
              
              $( "#matricula_aeronave" ).autocomplete({
                                                                 
                 source: "formulario.php?validaciones=aeronave&idClienteNiU="+parseInt($('#hidClienteNiu').val(),10),
                 minLength: 2,
                 select: function( event, ui ) {
                     
                     var objmatricula = document.getElementById("matricula_aeronave");
                     var objtipoaeronave = document.getElementById("tipo_aeronave");
                     var objniuAeronave = document.getElementById("niuAeronave");
                     
                     objtipoaeronave.value = ui.item.tipo; 
                     objmatricula.value = ui.item.value;
                     objniuAeronave.value = ui.item.niu;
                      
                 }
             });
              
          }
          
          fntAddProductos();
          
       </script>
   </body>
</html>