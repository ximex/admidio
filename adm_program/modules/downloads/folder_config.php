<?php
/******************************************************************************
 * Ordnerberechtigungen konfigurieren
 *
 * Copyright    : (c) 2004 - 2009 The Admidio Team
 * Homepage     : http://www.admidio.org
 * Module-Owner : Elmar Meuthen
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Uebergaben:
 *
 * folder_id : Ordner Id des uebergeordneten Ordners
 *
 *****************************************************************************/

require('../../system/common.php');
require('../../system/login_valid.php');
require('../../system/classes/table_folder.php');

// pruefen ob das Modul ueberhaupt aktiviert ist
if ($g_preferences['enable_download_module'] != 1)
{
    // das Modul ist deaktiviert
    $g_message->show($g_l10n->get('SYS_PHR_MODULE_DISABLED'));
}

// erst prüfen, ob der User auch die entsprechenden Rechte hat
if (!$g_current_user->editDownloadRight())
{
    $g_message->show($g_l10n->get('SYS_PHR_NO_RIGHTS'));
}

// Uebergabevariablen pruefen
if (array_key_exists('folder_id', $_GET))
{
    if (is_numeric($_GET['folder_id']) == false)
    {
        $g_message->show($g_l10n->get('SYS_INVALID_PAGE_VIEW'));
    }
    $folder_id = $_GET['folder_id'];
}
else
{
    // ohne FolderId gehts auch nicht weiter
    $g_message->show($g_l10n->get('SYS_INVALID_PAGE_VIEW'));
}



$_SESSION['navigation']->addUrl(CURRENT_URL);

//Folderobject erstellen
$folder = new TableFolder($g_db);
$folder->getFolderForDownload($folder_id);

//pruefen ob ueberhaupt ein Datensatz in der DB gefunden wurde...
if (!$folder->getValue('fol_id'))
{
    //Datensatz konnte nicht in DB gefunden werden...
    $g_message->show($g_l10n->get('SYS_INVALID_PAGE_VIEW'));
}

//NavigationsLink erhalten
$navigationBar = $folder->getNavigationForDownload();

//Parentordner holen
$parentRoleSet = null;
if ($folder->getValue('fol_fol_id_parent')) {
    $parentFolder = new TableFolder($g_db);
    $parentFolder->getFolderForDownload($folder->getValue('fol_fol_id_parent'));
    //Rollen des uebergeordneten Ordners holen
    $parentRoleSet = $parentFolder->getRoleArrayOfFolder();

}

if ($parentRoleSet == null) {
        //wenn der uebergeordnete Ordner keine Rollen gesetzt hat sind alle erlaubt
        //alle aus der DB aus lesen
        $sql_roles = 'SELECT *
                         FROM '. TBL_ROLES. ', '. TBL_CATEGORIES. '
                        WHERE rol_valid = 1
                          AND rol_system = 0
                          AND rol_cat_id = cat_id
                          AND cat_org_id = '. $g_current_organization->getValue('org_id'). '
                        ORDER BY rol_name';
        $result_roles = $g_db->query($sql_roles);

        while($row_roles = $g_db->fetch_object($result_roles))
        {
            //Jede Rolle wird nun dem Array hinzugefuegt
            $parentRoleSet[] = array(
                                'rol_id'        => $row_roles->rol_id,
                                'rol_name'      => $row_roles->rol_name);

        }

    }


//aktuelles Rollenset des Ordners holen
$roleSet = $folder->getRoleArrayOfFolder();

// Html-Kopf ausgeben
$g_layout['title'] = 'Ordnerberechtigungen setzen';

$g_layout['header'] = '
    <script type="text/javascript"><!--
    	$(document).ready(function() 
		{
            $("#fol_public").focus();
	 	});

        // Scripts fuer Rollenbox
        function hinzufuegen()
        {
            var allowed_roles = document.getElementById("AllowedRoles");
            var denied_roles  = document.getElementById("DeniedRoles");

            if (denied_roles.selectedIndex >= 0) {
                NeuerEintrag = new Option(denied_roles.options[denied_roles.selectedIndex].text, denied_roles.options[denied_roles.selectedIndex].value, false, true);
                denied_roles.options[denied_roles.selectedIndex] = null;
                allowed_roles.options[allowed_roles.length] = NeuerEintrag;
            }
        }

        function entfernen()
        {
            var allowed_roles = document.getElementById("AllowedRoles");
            var denied_roles  = document.getElementById("DeniedRoles");

            if (allowed_roles.selectedIndex >= 0)
            {
                NeuerEintrag = new Option(allowed_roles.options[allowed_roles.selectedIndex].text, allowed_roles.options[allowed_roles.selectedIndex].value, false, true);
                allowed_roles.options[allowed_roles.selectedIndex] = null;
                denied_roles.options[denied_roles.length] = NeuerEintrag;
            }
        }

        function absenden()
        {
            var allowed_roles = document.getElementById("AllowedRoles");

            allowed_roles.multiple = true;

            for (var i = 0; i < allowed_roles.options.length; i++)
            {
                allowed_roles.options[i].selected = true;
            }

            form.submit();
        }
    //--></script>';


require(THEME_SERVER_PATH. "/overall_header.php");

// Html des Modules ausgeben
echo '
<form method="post" action="'.$g_root_path.'/adm_program/modules/downloads/download_function.php?mode=7&amp;folder_id='.$folder_id.'">
<div class="formLayout" id="edit_download_folder_form" >
    <div class="formHead">Ordnerberechtigungen setzen</div>
    <div class="formBody">'.
        $navigationBar.'
        <div class="groupBox">
            <div class="groupBoxBody" >
                <ul class="formFieldList">
                    <li>
                        <div>
                            <input type="checkbox" id="fol_public" name="fol_public" ';
                            if($folder->getValue('fol_public') == 0)
                            {
                                echo ' checked="checked" ';
                            }
                            if($folder->getValue('fol_fol_id_parent') && $parentFolder->getValue('fol_public') == 0)
                            {
                                echo ' disabled="disabled" ';
                            }
                            echo ' value="0" onclick="toggleElement(\'rolesBox\');" />
                            <label for="fol_public"><img src="'. THEME_PATH. '/icons/lock.png" alt="Der Ordner ist &ouml;ffentlich." /></label>&nbsp;
                            <label for="fol_public">Öffentlicher Zugriff ist nicht erlaubt.</label>
                            <a rel="colorboxHelp" href="'. $g_root_path. '/adm_program/system/msg_window.php?message_id=DOW_PHR_PUBLIC_DOWNLOAD_FLAG&amp;inline=true"><img 
                                onmouseover="ajax_showTooltip(event,\''.$g_root_path.'/adm_program/system/msg_window.php?message_id=DOW_PHR_PUBLIC_DOWNLOAD_FLAG\',this)" onmouseout="ajax_hideTooltip()"
                                class="iconHelpLink" src="'. THEME_PATH. '/icons/help.png" alt="Hilfe" title="" /></a>';

                            //Der Wert der DisabledCheckbox muss mit einem versteckten Feld uebertragen werden.
                            if($folder->getValue('fol_fol_id_parent') && $parentFolder->getValue('fol_public') == 0)
                            {
                                echo '<input type=hidden id="fol_public_hidden" name="fol_public" value='. $parentFolder->getValue('fol_public'). ' />';
                            }

                        echo '
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="groupBox" id="rolesBox" ';
            if($folder->getValue('fol_public') == 1)
            {
                echo ' style="display: none;" ';
            }
            echo '>
            <div class="groupBoxHeadline">Rollenzugriffsberechtigungen</div>
            <div class="groupBoxBody" >
                <p>Hier wird konfiguriert welche Rollen Zugriff auf den Ordner haben dürfen.
                   Gesetzte Berechtigungen werden an alle Unterordner vererbt und bereits vorhandene
                   Berechtigungen in Unterordnern werden überschrieben. Es stehen nur Rollen
                   zur Verfügung die auf den übergeordneten Ordner Zugriff haben.</p>

                <div style="text-align: left; float: left;">
                    <div><img class="iconInformation" src="'. THEME_PATH. '/icons/no.png" alt="Kein Zugriff" title="Kein Zugriff" />Kein Zugriff</div>
                    <div>
                        <select id="DeniedRoles" size="8" style="width: 200px;">';
                        for($i=0; $i < count($parentRoleSet); $i++) 
                        {
                            $nextRole = $parentRoleSet[$i];

                            if ($roleSet == null || in_array($nextRole, $roleSet) == false) 
                            {
                                echo '<option value="'. $nextRole['rol_id']. '">'. $nextRole['rol_name']. '</option>';
                            }
                        }

                        echo '
                        </select>
                    </div>
                </div>
                <div style="float: left;" class="verticalIconList">
                    <ul>
                        <li>
                            <a class="iconLink" href="javascript:hinzufuegen()"><img 
                                src="'. THEME_PATH. '/icons/forward.png" alt="Rolle hinzufügen" title="Rolle hinzufügen" /></a>
                        </li>
                        <li>
                            <a class="iconLink" href="javascript:entfernen()"><img
                                src="'. THEME_PATH. '/icons/back.png" alt="Rolle entfernen" title="Rolle entfernen" /></a>
                        </li>
                    </ul>
                </div>
                <div>
                    <div><img class="iconInformation" src="'. THEME_PATH. '/icons/ok.png" alt="Zugriff erlaubt" title="Zugriff erlaubt" />Zugriff erlaubt</div>
                    <div>
                        <select id="AllowedRoles" name="AllowedRoles[]" size="8" style="width: 200px;">';
                        for($i=0; $i<count($roleSet); $i++) {

                            $nextRole = $roleSet[$i];
                            echo '<option value="'. $nextRole['rol_id']. '">'. $nextRole['rol_name']. '</option>';
                        }
                        echo '
                        </select>
                    </div>
                </div>
            </div>
        </div>


        <div class="formSubmit">
            <button name="speichern" type="submit" value="speichern" onclick="absenden()">
            <img src="'. THEME_PATH. '/icons/disk.png" alt="Speichern" />
            &nbsp;Speichern</button>
        </div>
    </div>
</div>
</form>

<ul class="iconTextLinkList">
    <li>
        <span class="iconTextLink">
            <a href="'.$g_root_path.'/adm_program/system/back.php"><img
            src="'. THEME_PATH. '/icons/back.png" alt="'.$g_l10n->get('SYS_BACK').'" /></a>
            <a href="'.$g_root_path.'/adm_program/system/back.php">'.$g_l10n->get('SYS_BACK').'</a>
        </span>
    </li>
</ul>';

require(THEME_SERVER_PATH. '/overall_footer.php');

?>