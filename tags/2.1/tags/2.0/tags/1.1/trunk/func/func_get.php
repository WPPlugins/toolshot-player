<?php
    switch ($_GET['task']) {
        case 'player_settings':
            unset($_GET['page']);
            unset($_GET['task']);
            unset($_GET['toolshot_player_act']);
            unset($_GET['nonce_func_post_toolshot_player']);
            if(isset($_GET['ads_code']))
                $_GET['ads_code'] = preg_replace("#[\r\n\t]{2,}#mis", '', $_GET['ads_code']);
            $success = 0;
            foreach($_GET as $key => $val){
                if (toolshot_class_table_update('toolshot_player', ['set' => 'value="'.$val.'"', 'where' => 'name = "'.$key.'"']))
                    $success++;
            }
            if($success!=0) $msg = 1;
            else $msg = 'Nothing change';
            echo '<msg>'.$msg.'</msg>';
            break;
        case 'metabox_get_filter':
            if (isset($_GET['get_filter']) && preg_match_all('#([^,]+)#mis', $_GET['get_filter'], $match, PREG_SET_ORDER)) {
                $source_player = [];
                foreach ($match as $val) array_push($source_player, $val[1]);
                toolshot_class_table_update('toolshot_player', ['set' => 'value=\''. json_encode($source_player).'\'', 'where' => 'name="source_player"']);
            }
            break;
    }
    die();
