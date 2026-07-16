<?php

if ( file_exists( IGFAE_PATH . 'lib/vendor/autoload.php' ) )
    require_once( IGFAE_PATH . 'lib/vendor/autoload.php' );

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function igfae_export($data, $entityName) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    // Titulo de la hoja
    $sheet->setTitle($entityName);
    //$sheet->getColumnDimension('B')->setAutoSize(true);
    $sheet->fromArray($data, NULL, 'A1');
    // Aplicamos estilos a la hoja
    applyStylesSheet($sheet);

    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $entityName . '_' . date(IGFAE_INTRANET_DATE_FORMAT_M_D_Y_EXPORT) . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    ob_end_clean();
    $writer->save('php://output');
    exit();
}

function igfae_export_mode2($data, $data2, $entityName, $titleTab) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    // Titulo de la hoja
    $sheet->setTitle($entityName);
    
    if(isset($data[1]['AcceptanceFile'])){
        $sheet->getCell('I2')->getHyperlink()->setUrl(wp_get_upload_dir()['baseurl'] . '/Equipments/' . $data[1]['AcceptanceFile']);
        $data[1]['AcceptanceFile'] = '';
    }
    if(isset($data[1]['TechnicalContact'])){
        if($data[1]['TechnicalContact'] == ' /  / '){
            $data[1]['TechnicalContact'] = '';
        }
    }
    /*if(isset($data[1]['TechnicalSpecification'])){
            $value = new RichText();
            $value->createTextRun($data[1]['TechnicalSpecification']);
            $data[1]['TechnicalSpecification'] = $value;
    }*/
    $sheet->fromArray($data, NULL, 'A1');
    // Aplicamos estilos a la hoja
    applyStylesSheet($sheet);

    $spreadsheet->createSheet();
    $spreadsheet->setActiveSheetIndex(1);
    $spreadsheet->getActiveSheet()->setTitle($titleTab);
    $spreadsheet->getActiveSheet()->fromArray($data2, NULL, 'A1');
    applyStylesSheet($spreadsheet->getActiveSheet());
    
    $spreadsheet->setActiveSheetIndex(0);

    ob_end_clean();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $entityName . '_' . date(IGFAE_INTRANET_DATE_FORMAT_M_D_Y_EXPORT) . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    ob_end_clean();
    $writer->save('php://output');    
    exit();

}

function applyStylesSheet($sheet) {
    $headerStyleArray = [
        'font' => [
            'bold' => true,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
        'borders' => [
            'top' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => [
                'argb' => 'BFBFBF',
            ]
        ],
    ];

    // Aplicamos estilos a la cabecera (primera fila)
    $highestColumn = $sheet->getHighestColumn();
    $highestColumn++;
    for ($col = 'A'; $col != $highestColumn; ++$col) {
        $sheet->getCell($col . 1)->getStyle()->applyFromArray($headerStyleArray);
    }

}