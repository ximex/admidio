<?php
/******************************************************************************
 * Neuen Ordner Anlegen
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

if(isset($_SESSION['download_request']))
{
   $form_values = strStripSlashesDeep($_SESSION['download_request']);
   unset($_SESSION['download_request']);
}
else
{
   $form_values['new_folder'] = null;
   $form_values['new_description'] = null;
}

//Folderobject erstellen
$folder = new TableFolder($g_db);
$folder->getFolderForDownload($folder_id);

//pruefen ob ueberhaupt ein Datensatz in der DB gefunden wurde...
if (!$folder->getValue('fol_id'))
{
    //Datensatz konnte nicht in DB gefunden werden...
    $g_message->show($g_l10n->get('SYS_INVALID_PAGE_VIEW'));
}

$parentFolderName = $folder->getValue('fol_name');


// Html-Kopf ausgeben
$g_layout['title']  = 'Ordner anlegen';
$g_layout['header'] = '
    <script type="text/javascript"><!--
        $(document).ready(function() 
        {
            $("#new_folder").focus();
        }); 
    //--></script>';
require(THEME_SERVER_PATH. '/overall_header.php');

// Html des Modules ausgeben
echo '
<form method="post" action="'.$g_root_path.'/adm_program/modules/downloads/download_function.php?mode=3&amp;folder_id='.$folder_id.'">
<div class="formLayout" id="edit_download_folder_form">
    <div class="formHead">'.$g_layout['title'].'</div>
    <div class="formBody">
        <ul class="formFieldList">
            <li>
                <dl>
                    <dt>
                        Diesen Ordner im Ordner <b>'.$parentFolderName.'</b> anlegen.
                    </dt>
                    <dd>&nbsp;</dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt><label for="new_folder">Name:</label></dt>
                    <dd>
                        <input type="text" id="new_folder" name="new_folder" value="'.$form_values['new_folder'].'" style="width: 350px;" maxlength="255" tabindex="1" />
                        <span class="mandatoryFieldMarker" title="'.$g_l10n->get('SYS_MANDATORY_FIELD').'">*</span>
                    </dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt><label for="new_description">Beschreibung:</label></dt>
                    <dd>
                        <textarea id="new_description" name="new_description" style="width: 350px;" rows="4" cols="40" tabindex="2" >'.$form_values['new_description'].'</textarea>
                    </dd>
                </dl>
            </li>
        </ul>

        <hr />

        <div class="formSubmit">
            <button name="anlegen" type="submit" value="anlegen">
            <img src="'. THEME_PATH. '/icons/folder_create.png" alt="Ordner anlegen" />
            &nbsp;Ordner anlegen</button>
        </div>
    </div>
</div>
</form>

<ul class="iconTextLinkList">
    <li>
        <span class="iconTextLink">
            <a href="'.$g_root_path.'/adm_program/system/back.php"><img
            src="'.THEME_PATH.'/icons/back.png" alt="'.$g_l10n->get('SYS_BACK').'" /></a>
            <a href="'.$g_root_path.'/adm_program/system/back.php">'.$g_l10n->get('SYS_BACK').'</a>
        </span>
    </li>
</ul>';

require(THEME_SERVER_PATH. '/overall_footer.php');

?>