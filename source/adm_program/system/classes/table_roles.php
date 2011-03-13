<?php
/******************************************************************************
 * Klasse fuer den Zugriff auf die Datenbanktabelle adm_roles
 *
 * Copyright    : (c) 2004 - 2011 The Admidio Team
 * Homepage     : http://www.admidio.org
 * License      : GNU Public License 2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Diese Klasse dient dazu einen Rollenobjekt zu erstellen.
 * Eine Rolle kann ueber diese Klasse in der Datenbank verwaltet werden.
 * Dazu werden die Informationen der Rolle sowie der zugehoerigen Kategorie
 * ausgelesen. Geschrieben werden aber nur die Rollendaten
 *
 * Neben den Methoden der Elternklasse TableAccess, stehen noch zusaetzlich
 * folgende Methoden zur Verfuegung:
 *
 * countVacancies($count_leaders = false) - gibt die freien Plaetze der Rolle zurueck
 *                    dies ist interessant, wenn rol_max_members gesetzt wurde
 * setInactive()    - setzt die Rolle auf inaktiv
 * setActive()      - setzt die Rolle wieder auf aktiv
 * viewRole()       - diese Methode basiert auf viewRole des Usersobjekts, geht aber noch weiter 
 *                    und prueft auch Rollen zu Terminen (hier muss man nicht Mitglied der Rolle
 *                    sein, sondern nur in einer Rolle sein, die den Termin sehen darf)
 *
 *****************************************************************************/

require_once(SERVER_PATH. '/adm_program/system/classes/table_access.php');

class TableRoles extends TableAccess
{
    // Alle konfigurierbare Werte für die Bezahlzeitraeume
    // Null oder 0 ist auch erlaubt, bedeutet aber dass kein Zeitraum konfiguriert ist
    protected $role_cost_periods = array(-1,1,2,4,12);
    protected $role_weekdays = array(1,2,3,4,5,6,7);

    // Konstruktor
    public function __construct(&$db, $role = '')
    {
        parent::__construct($db, TBL_ROLES, 'rol', $role);
    }

    // die Funktion gibt die Anzahl freier Plaetze zurueck
    // ist rol_max_members nicht gesetzt so wird immer 999 zurueckgegeben
    public function countVacancies($count_leaders = false)
    {
        if($this->getValue('rol_max_members') > 0)
        {
            $sql    = 'SELECT mem_usr_id FROM '. TBL_MEMBERS. '
                        WHERE mem_rol_id = '. $this->getValue('rol_id'). '
                          AND mem_begin <= "'.DATE_NOW.'"
                          AND mem_end    > "'.DATE_NOW.'"';
            if($count_leaders == false)
            {
                $sql = $sql. ' AND mem_leader = 0 ';
            }
            $this->db->query($sql);

            $num_members = $this->db->num_rows();
            return $this->getValue('rol_max_members') - $num_members;
        }
        return 999;
    }

    // Loescht die Abhaengigkeiten zur Rolle und anschliessend die Rolle selbst...
    public function delete()
    {
        global $g_current_session;

        // einlesen aller Userobjekte der angemeldeten User anstossen, da evtl.
        // eine Rechteaenderung vorgenommen wurde
        $g_current_session->renewUserObject();

        // die Systemrollem duerfen nicht geloescht werden
        if($this->getValue('rol_system') == true)
        {
            $sql    = 'DELETE FROM '. TBL_ROLE_DEPENDENCIES. '
                        WHERE rld_rol_id_parent = '. $this->getValue('rol_id'). '
                           OR rld_rol_id_child  = '. $this->getValue('rol_id');
            $this->db->query($sql);

            $sql    = 'DELETE FROM '. TBL_MEMBERS. '
                        WHERE mem_rol_id = '. $this->getValue('rol_id');
            $this->db->query($sql);

            return parent::delete();
        }
        else
        {
            return false;
        }
    }
    
    public function getCostPeriode()
    {
        return $this->role_cost_periods;
    }

    // die Funktion gibt die deutsche Bezeichnung für die Beitragszeitraeume wieder
    public static function getCostPeriodDesc($my_rol_cost_period)
    {
        global $g_l10n;
    
        if($my_rol_cost_period == -1)
        {
            return $g_l10n->get('ROL_UNIQUELY');
        }
        elseif($my_rol_cost_period == 1)
        {
            return $g_l10n->get('ROL_ANNUALLY');
        }
        elseif($my_rol_cost_period == 2)
        {
            return $g_l10n->get('ROL_SEMIYEARLY');
        }
        elseif($my_rol_cost_period == 4)
        {
            return $g_l10n->get('ROL_QUARTERLY');
        }
        elseif($my_rol_cost_period == 12)
        {
            return $g_l10n->get('ROL_MONTHLY');
        }
        else
        {
            return '--';
        }
    }
    
    public function getWeekdays()
    {
        return $this->role_weekdays;
    }

    // die Funktion gibt die deutsche Bezeichnung für die Beitragszeitraeume wieder
    public static function getWeekdayDesc($weekday)
    {
        global $g_l10n;
    
        if($weekday == 1)
        {
            return $g_l10n->get('SYS_MONDAY');
        }
        elseif($weekday == 2)
        {
            return $g_l10n->get('SYS_TUESDAY');
        }
        elseif($weekday == 3)
        {
            return $g_l10n->get('SYS_WEDNESDAY');
        }
        elseif($weekday == 4)
        {
            return $g_l10n->get('SYS_THURSDAY');
        }
        elseif($weekday == 5)
        {
            return $g_l10n->get('SYS_FRIDAY');
        }
        elseif($weekday == 6)
        {
            return $g_l10n->get('SYS_SATURDAY');
        }
        elseif($weekday == 7)
        {
            return $g_l10n->get('SYS_SUNDAY');
        }
        else
        {
            return '--';
        }
    }
 
    // Rolle mit der uebergebenen ID oder dem Rollennamen aus der Datenbank auslesen
    function readData($role, $sql_where_condition = '', $sql_additional_tables = '')
    {
        global $g_current_organization;

        if(is_numeric($role))
        {
            $sql_where_condition .= ' rol_id = '.$role;
        }
        else
        {
            $role = addslashes($role);
            $sql_where_condition .= ' rol_name LIKE "'.$role.'" ';
        }

        $sql_additional_tables .= TBL_CATEGORIES;
        $sql_where_condition   .= ' AND rol_cat_id = cat_id
                                    AND (  cat_org_id = '. $g_current_organization->getValue('org_id').'
                                        OR cat_org_id IS NULL ) ';
        return parent::readData($role, $sql_where_condition, $sql_additional_tables);
    }

    // interne Funktion, die Defaultdaten fur Insert und Update vorbelegt
    // die Funktion wird innerhalb von save() aufgerufen
    public function save($updateFingerPrint = true)
    {
        global $g_current_session;
        $fields_changed = $this->columnsValueChanged;
 
        parent::save($updateFingerPrint);

        // Nach dem Speichern noch pruefen, ob Userobjekte neu eingelesen werden muessen,
        if($fields_changed && is_object($g_current_session))
        {
            // einlesen aller Userobjekte der angemeldeten User anstossen, da evtl.
            // eine Rechteaenderung vorgenommen wurde
            $g_current_session->renewUserObject();
        }
    }

    // aktuelle Rolle wird auf aktiv gesetzt
    public function setActive()
    {
        global $g_current_session;

        // die Systemrollem sind immer aktiv
        if($this->getValue('rol_system') == true)
        {
            $sql    = 'UPDATE '. TBL_MEMBERS. ' SET mem_end   = "9999-12-31"
                        WHERE mem_rol_id = '. $this->getValue('rol_id');
            $this->db->query($sql);

            $sql    = 'UPDATE '. TBL_ROLES. ' SET rol_valid = 1
                        WHERE rol_id = '. $this->getValue('rol_id');
            $this->db->query($sql);

            // einlesen aller Userobjekte der angemeldeten User anstossen, da evtl.
            // eine Rechteaenderung vorgenommen wurde
            $g_current_session->renewUserObject();

            return 0;
        }
        return -1;
    }

    // aktuelle Rolle wird auf inaktiv gesetzt
    public function setInactive()
    {
        global $g_current_session;

        // die Systemrollem sind immer aktiv
        if($this->getValue('rol_system') == true)
        {
            $sql    = 'UPDATE '. TBL_MEMBERS. ' SET mem_end   = "'.DATE_NOW.'"
                        WHERE mem_rol_id = '. $this->getValue('rol_id'). '
                          AND mem_begin <= "'.DATE_NOW.'"
                          AND mem_end    > "'.DATE_NOW.'" ';
            $this->db->query($sql);

            $sql    = 'UPDATE '. TBL_ROLES. ' SET rol_valid = 0
                        WHERE rol_id = '. $this->getValue('rol_id');
            $this->db->query($sql);

            // einlesen aller Userobjekte der angemeldeten User anstossen, da evtl.
            // eine Rechteaenderung vorgenommen wurde
            $g_current_session->renewUserObject();

            return 0;
        }
        return -1;
    }
    
    // diese Methode basiert auf viewRole des Usersobjekts, geht aber noch weiter 
    // und prueft auch Rollen zu Terminen (hier muss man nicht Mitglied der Rolle
    // sein, sondern nur in einer Rolle sein, die den Termin sehen darf)
    public function viewRole()
    {
        global $g_current_user, $g_valid_login;
        
        if($g_valid_login == true)
        {
            if($this->getValue('cat_name_intern') == 'CONFIRMATION_OF_PARTICIPATION')
            {
                // pruefen, ob der Benutzer Mitglied einer Rolle ist, die den Termin sehen darf
                $sql = 'SELECT dtr_rol_id
                          FROM '.TBL_DATE_ROLE.', '.TBL_DATES.'
                         WHERE dat_rol_id = '.$this->getValue('rol_id').'
                           AND dtr_dat_id = dat_id
                           AND (  dtr_rol_id IS NULL
                               OR EXISTS (SELECT 1
                                            FROM '.TBL_MEMBERS.'
                                           WHERE mem_rol_id = dtr_rol_id
                                             AND mem_usr_id = '.$g_current_user->getValue('usr_id').'))';
                $this->db->query($sql);
                
                if($this->db->num_rows() > 0)
                {
                    return true;
                }
            }
            else
            {
                return $g_current_user->viewRole($this->getValue('rol_id'));
            }
        }
        return false;
    }
}
?>