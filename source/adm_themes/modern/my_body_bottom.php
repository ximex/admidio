
<!-- Hier koennen Sie Ihren HTML-Code einbauen, der am Ende des <body> Bereichs
     einer Admidio-Modul-Seite erscheinen soll.
-->

        &nbsp;</div>
        <div id="bottom_border_img_big"></div>
    </div>
    <div id="right_block">
        <div id="top_border_img_small"></div>
        <div id="sidebar" class="content">
            <?php
            if($g_valid_login)
            {
                echo "<h3>Angemeldet als</h3>";
            }
            else
            {
                echo "<h3>Anmelden</h3>";
            }
            include(SERVER_PATH. "/adm_plugins/login_form/login_form.php");
            echo '<br/>';
            include(SERVER_PATH. "/adm_plugins/calendar/calendar.php");
            echo '<br/>
            <h3>Module</h3>
            <span class="menu" style="margin-bottom: 10px;"><a href="'. $g_root_path. '/adm_program/index.php"><img
                style="vertical-align: middle;" src="'. THEME_PATH. '/icons/home.png" alt="Übersicht" title="Übersicht" /></a>
                <a href="'. $g_root_path. '/adm_program/index.php">Übersicht</a></span>';
            if( $g_preferences['enable_announcements_module'] == 1
            || ($g_preferences['enable_announcements_module'] == 2 && $g_valid_login))
            {
                echo '
                <span class="menu"><a href="'. $g_root_path. '/adm_program/modules/announcements/announcements.php"><img
                    style="vertical-align: middle;" src="'. THEME_PATH. '/icons/announcements.png" alt="Ankündigungen" title="Ankündigungen" /></a>
                    <a href="'. $g_root_path. '/adm_program/modules/announcements/announcements.php">Ankündigungen</a></span>';
            }
            if($g_preferences['enable_download_module'] == 1)
            {
                echo '
                <span class="menu"><a href="'. $g_root_path. '/adm_program/modules/downloads/downloads.php"><img
                    style="vertical-align: middle;" src="'. THEME_PATH. '/icons/download.png" alt="Downloads" title="Downloads" /></a>
                    <a href="'. $g_root_path. '/adm_program/modules/downloads/downloads.php">Downloads</a></span>';
            }
            if($g_preferences['enable_mail_module'] == 1)
            {
                echo '
                <span class="menu"><a href="'. $g_root_path. '/adm_program/modules/mail/mail.php"><img
                    style="vertical-align: middle;" src="'. THEME_PATH. '/icons/email.png" alt="E-Mail" title="E-Mail" /></a>
                    <a href="'. $g_root_path. '/adm_program/modules/mail/mail.php">E-Mail</a></span>';
            }
            if($g_preferences['enable_photo_module'] == 1 
            || ($g_preferences['enable_photo_module'] == 2 && $g_valid_login))
            {
                echo '
                <span class="menu"><a href="'. $g_root_path. '/adm_program/modules/photos/photos.php"><img
                    style="vertical-align: middle;" src="'. THEME_PATH. '/icons/photo.png" alt="Fotos" title="Fotos" /></a>
                    <a href="'. $g_root_path. '/adm_program/modules/photos/photos.php">Fotos</a></span>';
            }
            if( $g_preferences['enable_guestbook_module'] == 1
            || ($g_preferences['enable_guestbook_module'] == 2 && $g_valid_login))            
            {
                echo '
                <span class="menu"><a href="'. $g_root_path. '/adm_program/modules/guestbook/guestbook.php"><img
                    style="vertical-align: middle;" src="'. THEME_PATH. '/icons/guestbook.png" alt="Gästebuch" title="Gästebuch" /></a>
                    <a href="'. $g_root_path. '/adm_program/modules/guestbook/guestbook.php">Gästebuch</a></span>';
            }

            echo '
            <span class="menu"><a href="'. $g_root_path. '/adm_program/modules/lists/lists.php"><img
                style="vertical-align: middle;" src="'. THEME_PATH. '/icons/lists.png" alt="Listen" title="Listen" /></a>
                <a href="'. $g_root_path. '/adm_program/modules/lists/lists.php">Listen</a></span>
            <span class="menu"><a href="'. $g_root_path. '/adm_program/modules/lists/mylist.php"><img
                style="vertical-align: middle;" src="'. THEME_PATH. '/icons/mylist.png" alt="Eigene Listen" title="Eigene Listen" /></a>
                <a href="'. $g_root_path. '/adm_program/modules/lists/mylist.php">Eigene Listen</a></span>
            <span class="menu"><a href="'. $g_root_path. '/adm_program/modules/profile/profile.php"><img
                style="vertical-align: middle;" src="'. THEME_PATH. '/icons/profile.png" alt="Mein Profil" title="Mein Profil" /></a>
                <a href="'. $g_root_path. '/adm_program/modules/profile/profile.php">Mein Profil</a></span>';

            if( $g_preferences['enable_dates_module'] == 1
            || ($g_preferences['enable_dates_module'] == 2 && $g_valid_login))                    
            {
                echo '
                <span class="menu"><a href="'. $g_root_path. '/adm_program/modules/dates/dates.php"><img
                    style="vertical-align: middle;" src="'. THEME_PATH. '/icons/dates.png" alt="Termine" title="Termine" /></a>
                    <a href="'. $g_root_path. '/adm_program/modules/dates/dates.php">Termine</a></span>';
            }
            if( $g_preferences['enable_weblinks_module'] == 1
            || ($g_preferences['enable_weblinks_module'] == 2 && $g_valid_login))            
            {
                echo '
                <span class="menu"><a href="'. $g_root_path. '/adm_program/modules/links/links.php"><img
                    style="vertical-align: middle;" src="'. THEME_PATH. '/icons/weblinks.png" alt="Weblinks" title="Weblinks" /></a>
                    <a href="'. $g_root_path. '/adm_program/modules/links/links.php">Weblinks</a></span>';
            }
            
            if($g_preferences['enable_forum_interface'])
            {
                echo '<span class="menu"><a href="'. $g_forum->url. '"><img
                    style="vertical-align: middle;" src="'. THEME_PATH. '/icons/forum.png" alt="Forum" title="Forum" /></a>
                    <a href="'. $g_forum->url. '">Forum</a></span>';
            }

            if($g_current_user->isWebmaster() || $g_current_user->assignRoles() || $g_current_user->approveUsers() || $g_current_user->editUsers())
            {
                echo '<h3>Administration</h3>';
                if($g_current_user->approveUsers() && $g_preferences['registration_mode'] > 0)
                {
                    echo '<span class="menu"><a href="'. $g_root_path. '/adm_program/administration/new_user/new_user.php"><img
                    style="vertical-align: middle;" src="'. THEME_PATH. '/icons/new_registrations.png" alt="Neue Anmeldungen" title="Neue Anmeldungen" /></a>
                    <a href="'. $g_root_path. '/adm_program/administration/new_user/new_user.php">Neue Anmeldungen</a></span>';
                }
                if($g_current_user->editUsers())
                {
                    echo '<span class="menu"><a href="'. $g_root_path. '/adm_program/administration/members/members.php"><img
                    style="vertical-align: middle;" src="'. THEME_PATH. '/icons/user_administration.png" alt="Benutzerverwaltung" title="Benutzerverwaltung" /></a>
                    <a href="'. $g_root_path. '/adm_program/administration/members/members.php">Benutzerverwaltung</a></span>';
                }
                if($g_current_user->assignRoles())
                {
                    echo '<span class="menu"><a href="'. $g_root_path. '/adm_program/administration/roles/roles.php"><img
                    style="vertical-align: middle;" src="'. THEME_PATH. '/icons/roles.png" alt="Rollenverwaltung" title="Rollenverwaltung" /></a>
                    <a href="'. $g_root_path. '/adm_program/administration/roles/roles.php">Rollenverwaltung</a></span>';
                }
                if($g_current_user->isWebmaster())
                {
                    echo '<span class="menu"><a href="'. $g_root_path. '/adm_program/administration/backup/backup.php"><img
                    style="vertical-align: middle;" src="'. THEME_PATH. '/icons/backup.png" alt="Datenbank Backup" title="Datenbank Backup" /></a>
                    <a href="'. $g_root_path. '/adm_program/administration/backup/backup.php">Datenbank Backup</a></span>';
                }
                if($g_current_user->isWebmaster())
                {
                    echo '<span class="menu"><a href="'. $g_root_path. '/adm_program/administration/organization/organization.php"><img
                    style="vertical-align: middle;" src="'. THEME_PATH. '/icons/options.png" alt="Organisationseinstellungen" title="Organisationseinstellungen" /></a>
                    <a href="'. $g_root_path. '/adm_program/administration/organization/organization.php">Organisationseinstellungen</a></span>';
                }

            }
            
            ?>
        </div>
        <div id="bottom_border_img_small"></div>

        <div style="clear: both;"></div>
    </div>
</div>

<p>
    <a href="http://www.admidio.org"><img
    src="<?php echo THEME_PATH; ?>/images/admidio_logo_20.png" style="border: 0px; vertical-align: bottom;"
     alt="Das Online-Verwaltungssystem für Vereine, Gruppen und Organisationen"
     title="Das Online-Verwaltungssystem für Vereine, Gruppen und Organisationen" /></a>
    <span style="font-size: 9pt;">&nbsp;&nbsp;&copy; 2004 - 2011&nbsp;&nbsp;Admidio Team</span>
</p>