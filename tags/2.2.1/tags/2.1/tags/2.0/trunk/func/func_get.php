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
        case 'metabox_get_search':
            if (isset($_GET['get_search']) && preg_match_all('#([^,]+)#mis', $_GET['get_search'], $match, PREG_SET_ORDER)) {
                $search_upload = [];
                foreach ($match as $val) array_push($search_upload, $val[1]);
                toolshot_class_table_update('toolshot_player', ['set' => 'value=\''. json_encode($search_upload).'\'', 'where' => 'name="search_upload"']);
            }
            break;
        case 'upload_post':
            /*echo '<pre>';
            var_dump($_GET);
            echo '</pre>';
            $new_post = array(
                'post_title'    => $_GET['title'],
                'post_content'  => '[toolshot_player img="'.$toolshot_player['img'].'" url="'.$_GET['url'].'"]',
                'post_status'   => 'publish',
                'post_type'	  => 'post',
                'post_author'   => get_current_user_id(),
                'post_category' => $_GET['category']
            );
            $post_id = wp_insert_post($new_post);

            // download image file
            $upload_dir = wp_upload_dir();
            $image_data = file_get_contents($_GET['img']);
            $filename = basename($_GET['img']);
            if(wp_mkdir_p($upload_dir['path'])) $file = $upload_dir['path'] . '/' . $filename;
            else $file = $upload_dir['basedir'] . '/' . $filename;
            file_put_contents($file, $image_data);

            $wp_filetype = wp_check_filetype($filename, null );
            $attachment = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name($filename),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
            $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
            $res2= set_post_thumbnail( $post_id, $attach_id );
            update_post_meta($post_id, '_toolshot_player_url', sanitize_text_field($_GET['url']));*/
            upload_post($_GET['title'], $_GET['img'], $_GET['url'], $_GET['category']);
            break;
        case 'upload_all':
            foreach($_GET['title'] as $key => $val){
                if(isset($_GET['title'][$key], $_GET['img'][$key], $_GET['url'][$key]))
                    upload_post($_GET['title'][$key], $_GET['img'][$key], $_GET['url'][$key], $_GET['category']);
            }
            echo '<msg>1</msg>';
            break;
    }
    // upload post
    function upload_post($title, $img, $url, $category){
        $new_post = array(
            'post_title'    => $title,
            'post_content'  => '[toolshot_player img="'.$toolshot_player['img'].'" url="'.$url.'"]',
            'post_status'   => 'publish',
            'post_type'	  => 'post',
            'post_author'   => get_current_user_id(),
            'post_category' => $category,
        );
        $post_id = wp_insert_post($new_post);
        wp_set_post_terms($post_id, 'video', 'post_format');

        // download image file
        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($img);
        $filename = basename($img);
        if(wp_mkdir_p($upload_dir['path'])) $file = $upload_dir['path'] . '/' . $filename;
        else $file = $upload_dir['basedir'] . '/' . $filename;
        file_put_contents($file, $image_data);

        $wp_filetype = wp_check_filetype($filename, null );
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
        $res2= set_post_thumbnail( $post_id, $attach_id );
        update_post_meta($post_id, '_toolshot_player_url', sanitize_text_field($url));
    }

    die();
