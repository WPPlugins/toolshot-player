<?php
    switch ($_GET['task']) {
        case 'player_settings':
            unset($_GET['page']);
            unset($_GET['task']);
            unset($_GET['toolshot_player_act']);
            unset($_GET['nonce_func_post_toolshot_player']);
            if (isset($_GET['ads_code'])) $_GET['ads_code'] = preg_replace('#\\n#mis', '\\n', $_GET['ads_code']);
            if (toolshot_class_table_update('toolshot_config', ['set' => 'tc_text="' . preg_replace('#"#mis', '\\"', json_encode($_GET)) . '"', 'where' => 'tc_type = "player"']))
                $msg = 1;
            else $msg = 'Nothing change';
            echo '<msg>'.$msg.'</msg>';
            break;
        case 'metabox_get_filter':
            if (isset($_GET['get_filter']) && preg_match_all('#([^,]+)#mis', $_GET['get_filter'], $match, PREG_SET_ORDER)) {
                $source_player = [];
                foreach ($match as $val) array_push($source_player, $val[1]);
                toolshot_class_table_update('toolshot_config', ['set' => 'tc_option=\'' . json_encode(['source_player' => $source_player]) . '\'', 'where' => 'tc_type="player"']);
            }
            break;
    }
    die();
