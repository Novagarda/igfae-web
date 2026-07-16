<?php
function saveFile($filepath, $pathId)
{
    $fileType = pathinfo($filepath,PATHINFO_EXTENSION);
    $fileName = pathinfo($filepath,PATHINFO_FILENAME);
    $fileTypeId = null;
    switch (strtolower($fileType)) {
        case 'JPG':
        case 'jpg':
            $fileTypeId = IGFAE_INTRANET_FILE_EXTENSIONS_JPG;
            break;
        case 'PNG':
        case 'png':
            $fileTypeId = IGFAE_INTRANET_FILE_EXTENSIONS_PNG;
            break;
        case 'JPEG':
        case 'jpeg':
            $fileTypeId = IGFAE_INTRANET_FILE_EXTENSIONS_JPEG;
            break;
        case 'PDF':
        case 'pdf':
            $fileTypeId = IGFAE_INTRANET_FILE_EXTENSIONS_PDF;
            break;
        case 'XLS':
        case 'xls':
            $fileTypeId = IGFAE_INTRANET_FILE_EXTENSIONS_XLS;
            break;
        case 'XLSX':
        case 'xlsx':
            $fileTypeId = IGFAE_INTRANET_FILE_EXTENSIONS_XLSX;
            break;
        case 'DOC':
        case 'doc':
            $fileTypeId = IGFAE_INTRANET_FILE_EXTENSIONS_DOC;
            break;
        case 'DOCX':
        case 'docx':
            $fileTypeId = IGFAE_INTRANET_FILE_EXTENSIONS_DOCX;
            break;
    }

    if (!empty($fileTypeId)) {
        return igfae_intranet_insert('File',array(
            'Filename' => $fileName,
            'FileHash' => hash_file('md5', $filepath),
            'FilesExtensionID' => $fileTypeId,
            'PathID' => $pathId
        ),null);
    } else {
        return null;
    }


}

function getPathToFile($fileId){
  $sql = "SELECT p.Url, f.FileName, fe.FileExtension
  from File f
  INNER JOIN FileExtension fe
  on f.FilesExtensionID = fe.FileExtensionID 
  INNER JOIN `Path` p
  on f.PathID = p.PathID 
  where f.FileID = $fileId";

  $resultRows = igfae_intranet_fetch_data($sql);

  $result = "";
  foreach ($resultRows['rows'] as $row) {
    $result = $row->Url . "/" .  $row->FileName . "." . $row->FileExtension;
  }

  return $result;
}



function deleteFileFromBBDD($fileId){

    $sql = "DELETE FROM `File`
    WHERE FileID = $fileId";
    igfae_intranet_save($sql);

}