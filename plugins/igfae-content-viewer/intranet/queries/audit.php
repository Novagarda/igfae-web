<?php

/**
 * Ejemplo de uso: saveAudit(IGFAE_INTRANET_AUDIT_TYPE_ADD, 170, IGFAE_INTRANET_ENTITY_NAME_PROJECT, 150);
 * 
 * @param int       $auditTypeID The audit type ID (See constants.php IGFAE_INTRANET_AUDIT_TYPE_*)
 * @param int       $entityNameID The entity name ID (See constants.php IGFAE_INTRANET_ENTITY_NAME_**)
 * @param int       $entityID The entity ID
 */
function saveAudit($auditTypeID, $entityNameID, $entityID)
{
    global $current_user;
    // Obtenemos el PersonnelID asociado al UserID
    $personnelID = findPersonnelByWPUserId($current_user->ID);
    
    // Verificacion datos
    if (!isset($auditTypeID) || !isset($personnelID) || !isset($entityNameID) || !isset($entityID)) {
        // TODO revisar esto
        throw new \Exception('The data received for the audit is incorrect');
    }
    // Recuperamos el Personnel
    $sql = "select concat(p.Name, ' ', p.FirstSurname, ' ', coalesce(p.SecondSurname, '')) as name
    from Personnel p 
    where p.PersonnelID = %d";
    $rows = igfae_intranet_fetch_data($sql, [$personnelID]);
    if ($rows['count'] > 0) {
        $personnelName = $rows['rows'][0]->name;
    } else {
        // Si se trata de un usuario (administrador de WP) que no esta asociado a un Personnel, utilizamos el login del usuario
        $personnelName = $current_user->user_login;
    }

    $sql = "insert into Audit ( PersonnelID, Username, `Date`, AuditTypeID, EntityNameID, EntityID) values (%d, %s, %s, %d, %s, %d)";
    igfae_intranet_save($sql, [$personnelID, $personnelName, date(IGFAE_INTRANET_DATE_FORMAT_Y_M_D_H_I_S), $auditTypeID, $entityNameID, $entityID]);
}
