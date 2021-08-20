<!DOCTYPE html>
<html>
<head>

<style>
    hr {
        border: 0.2px;
    }
    h2{
        text-align: center;
    }
    b.titulo{
        font-size: 30px;
    }
    div{
        background: #F2F3F4;
       margin: -18px -18px -18px -18px;
        width:80%;
    }
    
    TABLE {
        border: black 1px solid;
        background: #F2F3F4;
    }
    tr{
       width:100%; 
    }
    a.rojo{
        color: #E74C3C;
        font-size: 10px;
    }
     a.info{
        color: black;
        font-size: 10px;
         text-align: center;
    }
    tr.center{
       text-align: center; 
    }
    td.horizontal{
        border: black 1px solid;
    }
    td.titulo{
        text-align: center; 
    }
   
    
</style>
</head>
<body>
   
     <?php
    error_reporting(E_ALL);
ini_set('display_errors', 1);
     require 'main_app/main.php';
            $connect = conectar();
            $intId = isset($_GET["id"])?intval($_GET["id"]):0;
            $arrInfo = array();
            $stmt ="SELECT FIRST(100) T.DT_NIU, T.NIU_CLIENTE, T.DT_NUMERO, T.FECHA_DT, T.NOMBRE, T.CANTIDAD, T.PRODUCTO, T.TIPO, T.LUGARDES, T.MATRICULA, T.HORAINI, T.HORAFIN, T.OBSERVAC, T.CAMION , C.NIU, C.NOMBRE
            FROM TICKETS T
            LEFT JOIN CLIENTES C
            ON C.NIU = T.NIU_CLIENTE
            WHERE DT_NUMERO = '{$intId}'
            ORDER BY DT_NIU desc"; 
    $query = ibase_prepare($stmt);  
    $v_query = ibase_execute($query); 
    while($rTMP = ibase_fetch_assoc($v_query)){
        //print_r($rTMP);
        $arrInfo[$rTMP["DT_NIU"]]["DT_NUMERO"] = $rTMP["DT_NUMERO"];
        $arrInfo[$rTMP["DT_NIU"]]["FECHA_DT"] = $rTMP["FECHA_DT"];
        $arrInfo[$rTMP["DT_NIU"]]["NOMBRE"] = $rTMP["NOMBRE"];
        $arrInfo[$rTMP["DT_NIU"]]["PRODUCTO"] = $rTMP["PRODUCTO"];
        $arrInfo[$rTMP["DT_NIU"]]["CANTIDAD"] = $rTMP["CANTIDAD"];
        $arrInfo[$rTMP["DT_NIU"]]["TIPO"] = $rTMP["TIPO"];
        $arrInfo[$rTMP["DT_NIU"]]["LUGARDES"] = $rTMP["LUGARDES"];
        $arrInfo[$rTMP["DT_NIU"]]["MATRICULA"] = $rTMP["MATRICULA"];
        $arrInfo[$rTMP["DT_NIU"]]["HORAINI"] = $rTMP["HORAINI"];
        $arrInfo[$rTMP["DT_NIU"]]["HORAFIN"] = $rTMP["HORAFIN"];
        $arrInfo[$rTMP["DT_NIU"]]["OBSERVAC"] = $rTMP["OBSERVAC"];
        $arrInfo[$rTMP["DT_NIU"]]["CAMION"] = $rTMP["CAMION"];
        
    }
    
    //print "<pre>";
    //print_r($arrInfo);
    //print "</pre><br><br>";
    ?>
    <div class="col-md-12 contenido">
        <?php
        if ( is_array($arrInfo) && ( count($arrInfo) > 0 ) ){
            
            reset($arrInfo);
            foreach ( $arrInfo as $key => $value ){
              
              ?>                  
                        <table>
                           <tr>
                               <td colspan="2" class="center">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <img src="images/pdf.jpg">
                                </td> 
                                <br><br><br><br><hr>
                            </tr>
                            <tr>
                                <td><b>RECIBO DE ENTREGA No.      </b><?php echo $value['DT_NUMERO']; ?></td>
                            </tr>
                            <tr>   
                                <td><b>Fecha de despacho:  </b><?php echo $value['FECHA_DT']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Cliente:  </b><?php echo $value['NOMBRE']; ?></td>
                            </tr>
                            <tr class="center">
                                <td colspan="2"><b>Direccion: AEROPUERTO INT. La Aurora</b></td>
                            </tr>
                            <tr>
                                <td colspan="2"><hr></td>
                                
                            </tr>
                            
                            <tr>
                                <td><b>Producto:  </b></td>
                                <td><b>Cantidad:  </b></td>
                            </tr>
                            <tr>
                                <td><b>  </b><?php echo $value['PRODUCTO']; ?></td>
                                <td><b>  </b><?php echo $value['CANTIDAD']; ?></td>
                            </tr>
                            <tr>
                                <td colspan="2"><hr></td>
                            </tr>
                            <tr class="center">
                                <td colspan="2" ><a class="rojo">This product complies with specification</a><br><a class="rojo">ASTDM D-1655 Latest edition.</a></td>
                            </tr>
                            <tr>
                                <td colspan="2"><hr></td>
                            </tr>
                             <tr>
                                <td><b>Tipo de Aeronave:  </b></td>
                                <td><b>Lugar de Despacho:  </b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            </tr>
                            <tr>
                                <td><b>  </b><?php echo $value['TIPO']; ?></td>
                                <td><b>  </b><?php echo $value['LUGARDES']; ?></td>
                            </tr>
                             <tr>
                                <td><b>Matricula:  </b><?php echo $value['MATRICULA']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Hora iniciado servicio:  </b><br><?php echo $value['HORAINI']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Hora finalizado servicio:  </b><br><?php echo $value['HORAFIN']; ?></td>
                                <td ><b>No. de Unidad:  </b><br><?php echo $value['CAMION']; ?></td>
                            </tr>
                            <tr>
                                <td colspan="2"><hr></td>
                            </tr>
                             <tr class="center">
                                <td colspan="2"><a class="rojo">Producto entregado de acuerdo a procedimientos</a><br><a class="rojo">de control de calidad.</a></td>
                            </tr>
                           <tr>
                                <td colspan="2"><hr></td>
                            </tr>
                            <tr>
                                <td><b>Entregado por:  </b></td>
                            </tr>
                            <tr>
                                <td colspan="2"><hr></td>
                            </tr>
                            <tr>
                                <td><b>Observaciones:  </b><?php echo $value['OBSERVAC']; ?></td>
                            </tr>
                        </table>
            <?php
            }
            
        }
        ?>  
    </div>
     
</body>
</html>


