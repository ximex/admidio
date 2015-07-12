<?php

class LoginForm
{
    /**
     * @param  string $text
     * @param  string $link
     * @param  string $icon
     * @param  bool   $showIcon
     * @param  string $id
     * @param  string $target
     * @return string
     */
    protected static function getLink($text, $link, $icon, $showIcon, $id = '', $target = '')
    {
        $html = '';
        $classBtn = '';

        if ($showIcon)
        {
            $classBtn = ' class="btn"';
        }
        if ($id !== '')
        {
            $id = ' id="'.$id.'"';
        }

        $html .= '<a href="'.$link.'"'.$target.$id.$classBtn.'>';
        if ($showIcon)
        {
            $html .= '<span class="fa fa-'.$icon.' fa-fw" aria-hidden="true"></span>';
        }
        $html .= $text;
        $html .= '</a>';

        return $html;
    }

    protected static function getRank()
    {
        $htmlUserRank = '';

        if(count($plg_rank) > 0)
        {
            $currentUserRankTitle = '';
            $rankTitle = reset($plg_rank);

            while($rankTitle != false)
            {
                $rankAssessment = key($plg_rank);
                if($rankAssessment < $gCurrentUser->getValue('usr_number_login'))
                {
                    $currentUserRankTitle = $rankTitle;
                }
                $rankTitle = next($plg_rank);
            }

            if($currentUserRankTitle !== '')
            {
                $htmlUserRank = ' ('.$currentUserRankTitle.')';
            }
        }

        return $htmlUserRank;
    }

    public static function getLogin()
    {
        $html = '<div id="plugin_'.$plugin_folder.'" class="admidio-plugin-content">';
        $html .= '<h3>'.$gL10n->get('SYS_REGISTERED_AS').'</h3>';

        $html .= '</div>';

        return $html;
    }
    public static function getLogout()
    {
        $html = '<div id="plugin_'.$plugin_folder.'" class="admidio-plugin-content">';
        $html .= '<h3>'.$gL10n->get('SYS_LOGIN').'</h3>';

        $html .= '</div>';
    }
}

?>
