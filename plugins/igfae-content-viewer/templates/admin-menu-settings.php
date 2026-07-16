<div id="wpbody-content">
    <div class="wrap">
        <h2><?php echo get_option("blogname"); ?> Settings</h2>
        <?php if( isset($_REQUEST["settings-updated"])):?>            
            <div id="setting-error-settings_updated" class="updated settings-error"> 
                <p><strong>Ajustes guardados.</strong></p>
            </div>
        <?php endif; ?>            
        <?php if( isset($_REQUEST["events-cache-cleared"])):?>            
            <div class="updated settings-error"> 
                <p><strong>Caché de eventos vaciada.</strong></p>
            </div>
        <?php endif; ?>            
    </div>
    
    <form method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">        
        <input type="hidden" name="action" value="igfae_save_settings" />
        <input type="hidden" name="callback" value="<?php echo admin_url('admin.php?page=igfae_settings'); ?>" />      
        
        <h1>Configuración BBDD WEB PÚBLICA</h1>
        <table class="form-table">
            <tbody>                
                <tr>
                    <th scope="row">
                        <label for="db_name_input">
                            Base de datos
                        </label>
                    </th>                    
                    <td>
                        <input name="igfae_db_name" type="text" id="db_name_input" value="<?php echo igfae_get_db_name();?>"class="regular-text" />
                        <p class="description">
                            Nombre de la base de datos
                        </p>                        
                    </td>
                </tr>                
                
                <tr>
                    <th scope="row">
                        <label for="db_user_input">
                            Usuario
                        </label>
                    </th>                    
                    <td>
                        <input name="igfae_db_user" type="text" id="db_user_input" value="<?php echo igfae_get_db_user(); ?>" class="regular-text" />
                        <p class="description">
                           Nombre de usuario de la base de datos
                        </p>                                                
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="db_pass_input">
                            Contraseña
                        </label>
                    </th>                    
                    <td>              
                        <input name="igfae_db_pass" type="password" id="db_pass_input" value="<?php echo igfae_get_db_pass(); ?>" class="regular-text" />
                        <p class="description">
                            Contraseña de la base de datos
                        </p>
                    </td>
                </tr>  
                
                <tr>
                    <th scope="row">
                        <label for="db_host_input">
                            Host
                        </label>
                    </th>                    
                    <td>
                        <input name="igfae_db_host" type="text" id="db_host_input" value="<?php echo igfae_get_db_host(); ?>" class="regular-text" />
                        <p class="description">
                            Direccion IP o dominio de la base de datos
                        </p>                        
                    </td>
                </tr>                                   
            </tbody>        
        </table>

        <h1>Configuración BBDD INTRANET</h1>
        <table class="form-table">
            <tbody>                
                <tr>
                    <th scope="row">
                        <label for="db_intranet_name_input">
                            Base de datos
                        </label>
                    </th>                    
                    <td>
                        <input name="igfae_db_intranet_name" type="text" id="db_intranet_name_input" value="<?php echo igfae_get_db_intranet_name();?>"class="regular-text" />
                        <p class="description">
                            Nombre de la base de datos
                        </p>                        
                    </td>
                </tr>                
                
                <tr>
                    <th scope="row">
                        <label for="db_intranet_user_input">
                            Usuario
                        </label>
                    </th>                    
                    <td>
                        <input name="igfae_db_intranet_user" type="text" id="db_intranet_user_input" value="<?php echo igfae_get_db_intranet_user(); ?>" class="regular-text" />
                        <p class="description">
                           Nombre de usuario de la base de datos
                        </p>                                                
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="db_intranet_pass_input">
                            Contraseña
                        </label>
                    </th>                    
                    <td>              
                        <input name="igfae_db_intranet_pass" type="password" id="db_intranet_pass_input" value="<?php echo igfae_get_db_intranet_pass(); ?>" class="regular-text" />
                        <p class="description">
                            Contraseña de la base de datos
                        </p>
                    </td>
                </tr>  
                
                <tr>
                    <th scope="row">
                        <label for="db_intranet_host_input">
                            Host
                        </label>
                    </th>                    
                    <td>
                        <input name="igfae_db_intranet_host" type="text" id="db_intranet_host_input" value="<?php echo igfae_get_db_intranet_host(); ?>" class="regular-text" />
                        <p class="description">
                            Direccion IP o dominio de la base de datos
                        </p>                        
                    </td>
                </tr>
            </tbody>        
        </table>                
        <h1>Tarea automatizada de publicaciones.</h1>
        <table class="form-table">
            <tbody>                  
                <tr>
                    <th scope="row">
                        <label for="db_intranet_host_input">
                            Inicio
                        </label>
                    </th>                    
                    <td>
                        <input name="igfae_intranet_cron_start" type="text" id="igfae_intranet_cron_start" value="<?php echo igfae_get_intranet_cron_start(); ?>" class="regular-text" />
                        <p class="description">
                            Fecha primera ejecución de la tarea
                        </p>                        
                    </td>
                </tr>                 
                <tr>
                    <th scope="row">
                        <label for="db_intranet_host_input">
                            Recurrencia
                        </label>
                    </th>                    
                    <td>
                        <input name="igfae_intranet_cron_recurrence" type="text" id="igfae_intranet_cron_recurrence" value="<?php echo igfae_get_intranet_cron_recurrence(); ?>" class="regular-text" />
                        <p class="description">
                            Expresión de recurrencia para la tarea de publicaciones. ('hourly','twicedaily','daily','weekly','monthly','quarterly','biannual')
                        </p>                        
                    </td>
                </tr>   
                <?php 
                    if ( !empty(wp_next_scheduled( 'igfae_cron_hook' ))) {
                ?>
                    <tr>
                        <th scope="row">
                            <label for="db_intranet_host_input">
                                Próxima ejecución
                            </label>
                        </th>                    
                        <td>
                            <p class="description">
                                <?php echo get_date_from_gmt(date( 'Y-m-d H:i:s', wp_next_scheduled( 'igfae_cron_hook' ) ), 'Y-m-d H:i:s' ) ?>
                            </p>                        
                        </td>
                    </tr>
                <?php 
                    }
                ?>
                <tr>
                    <th scope="row">
                        <label for="db_intranet_host_input">
                            Estado
                        </label>
                    </th>                    
                    <td>
                        <input type="radio" name="igfae_intranet_cron_state" value="1" <?= igfae_get_intranet_cron_state() == '1'?'checked':'';?>> Activado <input type="radio" name="igfae_intranet_cron_state" value="0" <?= igfae_get_intranet_cron_state() == '0'?'checked':'';?>> Desactivado                      
                    </td>
                </tr>                 
            </tbody>        
        </table>

        <h1>Caché de eventos</h1>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="igfae_events_cache_ttl_fetch">
                            TTL listados (segundos)
                        </label>
                    </th>
                    <td>
                        <input name="igfae_events_cache_ttl_fetch" type="number" min="0" id="igfae_events_cache_ttl_fetch" value="<?php echo esc_attr(igfae_get_events_cache_ttl_fetch()); ?>" class="small-text" />
                        <p class="description">
                            Duración de caché para búsquedas/listados de eventos. 0 desactiva esta caché.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="igfae_events_cache_ttl_single">
                            TTL single (segundos)
                        </label>
                    </th>
                    <td>
                        <input name="igfae_events_cache_ttl_single" type="number" min="0" id="igfae_events_cache_ttl_single" value="<?php echo esc_attr(igfae_get_events_cache_ttl_single()); ?>" class="small-text" />
                        <p class="description">
                            Duración de caché para la ficha de evento. 0 desactiva esta caché.
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="igfae_events_cache_ttl_metadata">
                            TTL metadata (segundos)
                        </label>
                    </th>
                    <td>
                        <input name="igfae_events_cache_ttl_metadata" type="number" min="0" id="igfae_events_cache_ttl_metadata" value="<?php echo esc_attr(igfae_get_events_cache_ttl_metadata()); ?>" class="small-text" />
                        <p class="description">
                            Duración de caché para tipos y columnas de eventos. 0 desactiva esta caché.
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e("Save Changes"); ?>" />
            <button type="submit" name="igfae_flush_events_cache" value="1" class="button">
                Guardar y vaciar caché de eventos
            </button>
        </p>
    </form>
    
    <div class="clear"></div>
</div>