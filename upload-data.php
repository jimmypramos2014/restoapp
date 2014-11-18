<?php
include('adata/Db.class.php');
include('adata/Db.class.php');
include("bussiness/clientes.php");
include("bussiness/organigrama.php");
include("bussiness/productos.php");

$objCliente = new clsCliente();

$IdEmpresa = 1;
$IdCentro = 1;
$IdCanal = 1;
$i = 0;
$flagValidation = '';
$sqlInsertCliNat = '';
$sqlInsertCliJur = '';

$sqlBulkInserCliNat = '';
$sqlBulkInserCliJur = '';
// LOG
$log = '=== ' . @date('Y-m-d H:i:s') . ' ===============================' . "\n"
        . 'FILES:' . print_r($_FILES, 1) . "\n"
        . 'POST:' . print_r($_POST, 1) . "\n";
$fp = fopen('upload-log.txt', 'a');
fwrite($fp, $log);
fclose($fp);



// Result object
//$r = new stdClass();
// Result content type
header('content-type: application/json');


// Maximum file size
$maxsize = 100; //Mb
// File size control
if ($_FILES['xfile']['size'] > ($maxsize * 1048576)) {
    $r->error = "Max file size: $maxsize Kb";
}


// Uploading folder
$folder = 'media/xls/';
if (!is_dir($folder))
    mkdir($folder);
// If specifics folder 
$folder .= $_POST['folder'] ? $_POST['folder'] . '/' : '';
if (!is_dir($folder))
    mkdir($folder);


// If the file is an image
if (preg_match('/image/i', $_FILES['xfile']['type'])) {

    $filename = $_POST['value'] ? $_POST['value'] :
            $folder . $_FILES['xfile']['name'] . '.jpg';
} else {

    $tld = explode(',', $_FILES['xfile']['name']);
    $tld = $tld[count($tld) - 1];
    $filename = $_POST['value'] ? $_POST['value'] :
            $folder . $_FILES['xfile']['name'];


}


// Supporting image file types
$types = Array('image/png', 'image/gif', 'image/jpeg');
// File type control
/*if (in_array($_FILES['xfile']['type'], $types)) {
    // Create an unique file name    
    // Uploaded file source
    $source = file_get_contents($_FILES["xfile"]["tmp_name"]);
    // Image resize
    imageresize($source, $filename, $_POST['width'], $_POST['height'], $_POST['crop'], $_POST['quality']);
} else
// If the file is not an image*/
move_uploaded_file($_FILES["xfile"]["tmp_name"], $filename);



// Result data
/*$r->filename = $filename;
$r->path = $path;
$r->img = '<img src="' . $path . $filename . '" alt="image" />';

// Return to JSON
echo json_encode($r);*/
$tipoupload = $_POST['tipoupload'];
$onlyName = basename($filename);
$typefile = $_FILES['xfile']['type'];


if (file_exists ($fileName)){
    require_once('../intranet/common/PHPExcel.php');
    require_once('../intranet/common/PHPExcel/Reader/Excel2007.php');

    if ($TipoData == 'clientes'){
        $objReader = new PHPExcel_Reader_Excel2007();
        $objPHPExcel = $objReader->load('bak_'.$archivo);
        $objFecha = new PHPExcel_Shared_Date();

        $sqlInsertCliNat = 'INSERT INTO tm_cliente_natural (';
        $sqlInsertCliNat .= 'tm_idempresa, tm_idcentro, tm_idcanal, tm_iddocident, ';
        $sqlInsertCliNat .= 'tm_numerodoc, tm_nombres, tm_apepaterno, tm_apematerno, ';
        $sqlInsertCliNat .= 'tm_direccion, tm_telefono, tm_fax, tm_email, tm_foto, ';
        $sqlInsertCliNat .= ' Activo, IdUsuarioReg, FechaReg, IdUsuarioAct, FechaAct) VALUES ';
    
        $sqlInsertCliJur = 'INSERT INTO tm_cliente_juridica (';
        $sqlInsertCliJur .= 'tm_idempresa, tm_idcentro, tm_idcanal, tm_iddocident, ';
        $sqlInsertCliJur .= 'tm_numerodoc, tm_representante, tm_razsocial, ';
        $sqlInsertCliJur .= 'tm_direccion, tm_telefono, tm_fax, tm_email, tm_foto, ';
        $sqlInsertCliJur .= ' Activo, IdUsuarioReg, FechaReg, IdUsuarioAct, FechaAct) VALUES ';


        $objPHPExcel->setActiveSheetIndex(0);
        $countRowsExcel = $objPHPExcel->getActiveSheet()->getHighestRow();
        
        for ($i = 2; $i <= $countRowsExcel; $i++){
            
            $getTipoCliente = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
            $getIdDocIdent = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
            $getNumeroDoc = trim($objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue());

            $getRazonSocial = trim($objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue());

            $getApePaterno = trim($objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue());
            $getApeMaterno = trim($objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue());
            $getNombres = trim($objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue());
            
            if ($getTipoCliente == 'NA'){
                if ($getIdDocIdent == 'DNI')
                    $IdDocIdent = 1;
                elseif ($getIdDocIdent == 'RUC')
                    $IdDocIdent = 6;
                else
                    $IdDocIdent = 7;
                $flagValidation = ' CONCAT(TRIM(tm_apepaterno), \' \', TRIM(tm_apematerno), \' \', TRIM(tm_nombres)) = \''.$getApePaterno.' '.$getApeMaterno.' '.$getNombres.'\'';
                $flagValidation .= ' and tm_iditc = \'00\'';
            }
            else {
                $IdDocIdent = 6;
                $flagValidation = ' TRIM(tm_razsocial) = \''.$getRazonSocial.'\' ';
                $flagValidation .= 'and tm_iditc = \'01\'';
            }

            $flagValidation .= ' and tm_numerodoc = \''.$getNumeroDoc.'\'';

            $rsVal = $objCliente->Listar('VALID-'.$getTipoCliente, $flagValidation);
            $countRsVal = count($rsVal);

            if ($countRsVal <= 0){
                //$_DATOS_EXCEL[$i]['tipocliente'] = $getTipoCliente == "NA" ? 1 : 6;
                $_DATOS_EXCEL[$i]['idempresa'] = $IdEmpresa;
                $_DATOS_EXCEL[$i]['idcentro'] = $IdCentro;
                $_DATOS_EXCEL[$i]['idcanal'] = $IdCanal;
                $_DATOS_EXCEL[$i]['iddocident'] = $IdDocIdent;
                $_DATOS_EXCEL[$i]['nrodoc'] = $getNumeroDoc;

                if ($getTipoCliente == 'NA'){
                    $_DATOS_EXCEL[$i]['nombres'] = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['apepaterno'] = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['apematerno'] = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                }
                else {
                    $_DATOS_EXCEL[$i]['representante'] = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                    $_DATOS_EXCEL[$i]['razonsocial'] = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                }
                
                $_DATOS_EXCEL[$i]['direccion'] = trim($objPHPExcel->getActiveSheet()->getCell('N'.$i)->getCalculatedValue());
                $_DATOS_EXCEL[$i]['telefono'] = $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['fax'] = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                $_DATOS_EXCEL[$i]['email'] = trim($objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue());
                $_DATOS_EXCEL[$i]['foto'] = '';

                
                foreach($_DATOS_EXCEL as $campo => $valor){
                    if ($getTipoCliente == 'NA'){
                        $sqlBulkInserCliNat.= ' (\'';
                        foreach ($valor as $campo2 => $valor2){
                            $campo2 == 'email' ? $sqlBulkInserCliNat.= str_replace("'", "\'", trim(preg_replace('/\s+/', ' ', $valor2)))."', '1', '1', NOW(), '1', NOW()),\n" : $sqlBulkInserCliNat.= str_replace("'", "\'", trim(preg_replace('/\s+/', ' ', $valor2))).'\',\'';
                        }
                    }
                    elseif ($getTipoCliente == 'JU') {
                        $sqlBulkInserCliJur.= ' (\'';
                        foreach ($valor as $campo2 => $valor2){
                            $campo2 == 'email' ? $sqlBulkInserCliJur.= str_replace("'", "\'", trim(preg_replace('/\s+/', ' ', $valor2)))."', '1', '1', NOW(), '1', NOW()),\n" : $sqlBulkInserCliJur.= str_replace("'", "\'", trim(preg_replace('/\s+/', ' ', $valor2))).'\',\'';
                        }
                    }
                }
            }
        }

        $sqlBulkInserCliNat = substr($sqlBulkInserCliNat, 0, strlen(trim($sqlBulkInserCliNat)) - 1);
        $sqlBulkInserCliJur = substr($sqlBulkInserCliJur, 0, strlen(trim($sqlBulkInserCliJur)) - 1);

        /*if (strlen($sqlBulkInserCliNat) > 0)
            $objData->MultiInsert($sqlInsertCliNat.$sqlBulkInserCliNat);
        
        if (strlen($sqlBulkInserCliJur) > 0)
            $objData->MultiInsert($sqlInsertCliJur.$sqlBulkInserCliJur);*/

        echo $sqlInsertCliNat.$sqlBulkInserCliNat;
        echo $sqlInsertCliJur.$sqlBulkInserCliJur;

        unlink($destino);
    }
}
?>