<?php
function getSelectPrototypeResearcherIpCoordinator()
{

    $sql = "SELECT p.PersonnelID as 'personnelID' , concat(p.Name, ' ', p.FirstSurname, ' ' ,coalesce(p.SecondSurname, '')) as 'name'
    FROM Personnel p  
    left join PrototypePersonnel pp on p.PersonnelID = pp.PersonnelID and pp.PersonnelTypeID = ".IGFAE_INTRANET_PERSONNEL_TYPE_IPCP."
    ORDER BY p.Name ASC";

    $rows = igfae_intranet_fetch_data($sql);
    $result = [];

    foreach ($rows['rows'] as $row) {
      $result[] = new IntranetPersonnel(
          $row->personnelID,
          $row->name
      );
  }

    return $result;
}
