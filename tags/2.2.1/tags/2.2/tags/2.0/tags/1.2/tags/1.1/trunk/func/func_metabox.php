<?php
add_action('add_meta_boxes', 'metabox_toolshot_player');
function metabox_toolshot_player(){
    add_meta_box('metabox_toolshot_player', 'ToolsHot Player', 'metabox_toolshot_player_output', 'post');
}

add_action('save_post', 'metabox_toolshot_save');
function metabox_toolshot_save($post_id){
    if(!isset($_POST['nonce_metabox_toolshot_player'])) return;
    if(!wp_verify_nonce($_POST['nonce_metabox_toolshot_player'], 'save_metabox_toolshot_player')) return;
    update_post_meta($post_id, '_th_player_url', sanitize_text_field($_POST['th_hand_upload_input_url']));
    update_post_meta($post_id, '_th_player_image', sanitize_text_field($_POST['th_hand_upload_input_image']));
}

add_action( 'the_content', 'metabox_toolshot_show' );
function metabox_toolshot_show($content){
    if (is_single()){
        global $post, $url_toolshot_player, $toolshot_player;
        $url = get_post_meta($post->ID, '_th_player_url', true);
		if(empty($url)) return $content;
        $image = get_post_meta($post->ID, '_th_player_image', true);
        if(!empty($image)) $toolshot_player['image'] = $image;
		
        $url_video = 'p_key='.$toolshot_player['key'].'&url='.urlencode(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($toolshot_player['key']), $url, MCRYPT_MODE_CBC, md5(md5($toolshot_player['key'])))));
        $html = '';
        $html .= '<div class="embed-responsive embed-responsive-16by9">';
        wp_enqueue_style("jw_player", plugins_url("../assets/css/toolshot.player.css", __FILE__), FALSE);
        if($toolshot_player['player'] == 'toolshot'){
            $tmp = '';
            foreach($toolshot_player as $key => $val) if($key=='ads_code' || $key=='source_player'){}else $tmp .= $key.'='.$val.'&';
            $html .= '<iframe class="embed-responsive-item" src="'.$url_toolshot_player.'?'.$tmp.$url_video.'" allowfullscreen=""></iframe>';
        }else{
            wp_enqueue_style("toolshot_player_skin_css", plugins_url("../assets/css/skin-player/".$toolshot_player['skin'].".css", __FILE__), FALSE);
            wp_enqueue_script("jw_player", plugins_url("../assets/js/jwplayer.js", __FILE__), FALSE);
            wp_enqueue_script("toolshot_player_js", plugins_url("../assets/js/toolshot.player.js", __FILE__), FALSE);
            $html .= '<div class="embed-responsive-item">
                        <div id="mediaplayer">Loading the player ...</div>
                    </div>
                    <div class="toolshot_logo_"></div>';
            if($toolshot_player['ads_show']=='show'){
                $html .= '<div id="toolshot_ads">
                    <a title="Close" class="toolshot_ads_close_" href="javascript:void(0);" onclick="document.getElementById(\'toolshot_ads\').outerHTML=\'\';">x</a>
                    '.$toolshot_player['ads_code'].'
                </div>';
            }
            $html .= '<script type="text/javascript">
                        var url_toolshot_player = \''.$url_toolshot_player.'\';
                        var toolshot_player = {';
                        foreach($toolshot_player as $key => $val) if($key=='ads_code' || $key=='source_player'){}else $html .= $key.' : \''.$val.'\',';
                        $html .= '};
                        var url_video_md5 = \''.md5($url).'\';
                        var url_video = \''.$url_video.'\';
                    </script>';
        }
        $html .= '</div><!--/.embed-responsive-->';
        $content = $html.$content;
    }
    return $content;
}
function metabox_toolshot_player_output($wp_post){
    global $toolshot_post, $url_toolshot, $url_toolshot_player, $toolshot_player;
    wp_nonce_field('save_metabox_toolshot_player', 'nonce_metabox_toolshot_player');
?>
    <style>
        ul.th_tab_{
            list-style: none;
            padding-left:10px;
            border-bottom:#E8E8E8 solid 1px;
        }
        ul.th_tab_ li{
            float:left;
            display: block;
            margin: 0 8px 0 0;
            line-height: 35px;
        }
        ul.th_tab_ li a{
            box-shadow: none;
            padding: 8px 10px;
            text-decoration: none;
            background: #F1F1F1;
            color: #555;
            border: #ccc solid 1px;
            border-bottom: #E8E8E8 solid 1px;
            font-size: 15px;
            font-weight: 700;
            line-height: inherit;
        }
        ul.th_tab_ li a.th_active_{
            background: #fff;
            border-bottom: #fff solid 1px;
            color:#000;
        }
        .clearfix:before, .clearfix:after {
            clear: both;
            content: " ";
            display: block;
            height: 0;
            visibility: hidden;
        }
        .label_{font-weight: bold;display: block;}
        #th_hand_upload hr{
            border:none;
            border-bottom: #E8E8E8 solid 1px;
            padding: 0.5em 0;
        }
        #th_hand_upload_source{word-break: break-all;}
        #th_hand_upload_source p, #th_auto_upload_checkbox p{margin: 0.6em 0;}
        #th_hand_upload_source b, #th_auto_upload_checkbox b{text-transform: capitalize;}
        #th_hand_upload_source a{text-decoration: none;}
        #th_auto_upload{display: none;}
        #th_auto_upload_checkbox label{
            margin-left: 1.1em;
            line-height: 2em;
            display: inline-block;
        }
        input[name*=th_][type=text]{width:100%;}
        .two_col_{
            margin-top:1em;
        }
        .two_col_ .item_{
            float:left;
            width:49%;
        }
        .two_col_ .item_:first-child{
            margin-right:2%;
        }
        .two_col_ .item_{text-align: center;}
        .two_col_ .dashicons{line-height: 28px;}
        .two_col_ .item_ button, .two_col_ .item_ input{margin-bottom:1em;}
        .two_col_ .item_ #output canvas{max-width:100%;}
        .embed-responsive {
            position: relative;
            display: block;
            height: 0;
            padding: 0;
            overflow: hidden;
        }
        .embed-responsive.embed-responsive-16by9 {
            padding-bottom: 56.25%;
            background: #f1f1f1;
        }
        .embed-responsive .embed-responsive-item, .embed-responsive iframe, .embed-responsive embed, .embed-responsive object {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        #th_auto_upload_list_posts{
            min-height:500px;
            margin:0 -5px;
        }
        #th_auto_upload_list_posts .item_{
            width:16.66%;
            float:left;
        }
        #th_auto_upload_list_posts .item_ a{
            text-decoration: none;
            color:rgba(68, 68, 68, 0.78);
        }
        #th_auto_upload_list_posts .item_ a:hover{
            color:#000;
        }
        #th_auto_upload_list_posts .item_ > div{
            padding:0 5px;
        }
        #th_auto_upload_list_posts .item_ a div.img_{
            height:150px;
            overflow: hidden;
        }
        #th_auto_upload_list_posts .item_ a div.img_ img{
            width:100%;
        }
        #th_auto_upload_list_posts .item_ p{
            margin-top: .3em;
            text-align: center;
            height: 3em;
            overflow: hidden;
        }
    </style>
    <ul class="th_tab_ clearfix">
        <li><a href="#" class="th_active_" data-upload="hand_upload" onclick="return th_change_tab(this, 'hand_upload');">Hand Upload</a></li>
        <li><a href="#" data-upload="auto_upload" onclick="return th_change_tab(this, 'auto_upload');">Source Suggests</a></li>
    </ul>
    <div id="th_hand_upload">
        <div id="th_hand_upload_source">
            Source support :
        </div>
        <hr>

        <?php $image = get_post_meta($wp_post->ID, '_th_player_image', true);?>
        <p><label class="label_" for="th_hand_upload_input_image">Image Video (Url or Empty)</label></p>
        <input type="text" name="th_hand_upload_input_image" id="th_hand_upload_input_image" value="<?=!empty($image) ? $image:$toolshot_player['image']?>">

        <p><label class="label_" for="th_hand_upload_input_url">Url Video</label></p>
        <input type="text" name="th_hand_upload_input_url" id="th_hand_upload_input_url" onclick="th_get_video(this, this.value)" onkeyup="th_get_video(this, this.value)" onchange="th_get_video(this, this.value)" value="<?=get_post_meta($wp_post->ID, '_th_player_url', true)?>">
        <div class="two_col_ clearfix">
            <div class="item_">
                <button class="button" type="button" onclick="shoot();"><span class="dashicons dashicons-camera"></span> Capture Video</button>
                <div class="embed-responsive embed-responsive-16by9"></div>
            </div><!--.item_-->
            <div class="item_">
                <button class="button" type="button" onclick="document.getElementById('set-post-thumbnail').click()"><span class="dashicons dashicons-format-image"></span> Set featured image</button>
                <div id="output"></div>
            </div>
        </div><!--.two_col_-->
    </div><!--#th_hand_upload-->
    <div id="th_auto_upload">
        <div id="th_auto_upload_checkbox">
            <p><b>Source Get : </b></p>
        </div>
        <hr>
        <div id="th_auto_upload_list_posts" class="clearfix">
            <div></div>
        </div><!--#th_auto_upload_list_posts-->
        <?php wp_nonce_field('save_func_post_toolshot_player', 'nonce_func_post_toolshot_player');?>
    </div><!--#th_auto_upload-->
    <script>
        var url_toolshot_player = '<?=$url_toolshot_player?>';
        var http = new XMLHttpRequest();
        var tmp_change_tab = false;
        var _th_player_url = [];
        <?php foreach(toolshot_class_table_select('postmeta', ['select' => 'meta_value', 'where'=>'meta_key="_th_player_url" and meta_value!=""', 'group by'=>'meta_value']) as $val){?>
            _th_player_url.push('<?=$val->meta_value?>');
        <?php }?>
        // th_change_tab
        function th_change_tab(thiss, val){
            document.getElementsByClassName('th_active_')[0].setAttribute('class', '');
            thiss.setAttribute('class', 'th_active_');
            document.getElementById('th_hand_upload').setAttribute('style', 'display:none');
            document.getElementById('th_auto_upload').setAttribute('style', 'display:none');
            if(val=='hand_upload'){
                document.getElementById('th_hand_upload').setAttribute('style', 'display:block');
            }else{
                document.getElementById('th_auto_upload').setAttribute('style', 'display:block');
                if(!tmp_change_tab) get_filter();
                tmp_change_tab = true;
            }
            return false;
        }
        // get source support
        var source_player = [<?php foreach (json_decode($toolshot_player['source_player']) as $val) echo '"'.$val.'",';?>];
        http.open("GET", url_toolshot_player+'get_source_support', true);
        http.onreadystatechange = function(){
            obj = JSON.parse(http.responseText);
            th_hand_upload_source = '<p><b>Source support :</b></p>';
            th_auto_upload_checkbox = '<p><b>Source Get :</b></p>';
            for (key in obj){
                th_hand_upload_source += '<p><b>'+key.replace(/_/g, ' ')+'</b> : ';
                th_auto_upload_checkbox += '<p><b>'+key.replace(/_/g, ' ')+'</b> : ';
                for(key1 in obj[key]){
                    th_hand_upload_source += '<a href="'+obj[key][key1]['_link']+'" target="_blank">'+key1+'</a>,&nbsp;&nbsp;';
                    if(typeof obj[key][key1]['_filter'] != 'undefined'){
                        th_auto_upload_checkbox += '<label><input ';
                        if(source_player.indexOf(obj[key][key1]['_filter'])>=0) th_auto_upload_checkbox += 'checked ';
                        th_auto_upload_checkbox += 'type="checkbox" class="get_filter_" value="'+obj[key][key1]['_filter']+'" onclick="get_filter(this, this.value)">'+key1+'</label>';
                    }
                }
                th_hand_upload_source += '</p>';
                th_auto_upload_checkbox += '</p>';
            }
            document.getElementById('th_hand_upload_source').innerHTML = th_hand_upload_source;
            document.getElementById('th_auto_upload_checkbox').innerHTML = th_auto_upload_checkbox;
        }
        http.send(null);
        // get video
        var th_url_video = '';
        function th_get_video(thiss, val){
            if(typeof val != 'undefined' && th_url_video != val){
                th_url_video = val;
                http.open("GET", url_toolshot_player+"json_video?url="+th_url_video, true);
                http.onreadystatechange = function(){
                    obj = eval(http.responseText);
                    document.getElementsByClassName('embed-responsive')[0].innerHTML = '<video poster="'+document.getElementById('th_hand_upload_input_image').value+'" id="my-video_html5_api" autoplay controls class="embed-responsive-item"><source src="'+obj[0]['file'].replace(/\"/gim, '&#34;')+'" type="video/mp4"></video>';
                };
                http.send(null);
            }
        }
        // capture video
        var videoId = 'my-video_html5_api';
        var canvas = '';
        function capture(video) {
            var w = video.videoWidth;
            var h = video.videoHeight;
            var canvas = document.createElement('canvas');
            canvas.width  = w;
            canvas.height = h;
            var ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, w, h);
            return canvas;
        }
        function shoot(){
            var video  = document.getElementById(videoId);
            var output = document.getElementById('output');
            canvas = capture(video);
            output.innerHTML  = '';
            output.appendChild(canvas);
            var canvas = document.getElementsByTagName('canvas');
        }
        // get filter
        function get_filter(thiss, val){
            var arr = [], i = 0;
            var elements = document.getElementsByClassName('get_filter_');
            for(var i = 0; i < elements.length; i++){
                if(elements[i].checked) arr.push(elements[i].value);
            }
            params = 'nonce_func_post_toolshot_player='+document.getElementById('nonce_func_post_toolshot_player').value+'&task=metabox_get_filter&get_filter='+arr;
            http.open("GET", 'admin.php?page=toolshot_player_view_player_settings&'+params, true);
            http.onreadystatechange = function(){}
            http.send(null);
            if(typeof thiss == 'undefined' && typeof val == 'undefined'){
                if(arr.length>0) get_list_posts(arr[arr.length-1], arr, arr.length-1);
            }else{
                if(thiss.checked){
                    get_list_posts(val);
                }else{
                    var reg = new RegExp('<data data-url="'+val.replace('\/', '\\/')+'">.+?</data>', "gim");
                    document.getElementById('th_auto_upload_list_posts').innerHTML = document.getElementById('th_auto_upload_list_posts').innerHTML.replace(reg, '');
                }
            }
        }
        // get list posts
        function get_list_posts(data_url, arr, i){
            if(typeof i != 'undefined' && i<0) return;

            th_auto_upload_list_posts = document.createElement('data');
            th_auto_upload_list_posts.setAttribute('data-url', data_url);
            http = new XMLHttpRequest();
            http.open('GET', url_toolshot_player+'filter?url='+data_url, true);
            http.onreadystatechange = function(){
                obj = JSON.parse(http.responseText);
                tmp = '';
                for(key in obj)
                    if(_th_player_url.indexOf(obj[key]['url'])==-1)
                        tmp += '<div class="item_"><div><a href="'+obj[key]['url']+'" title="Get this video" onclick="return autoupload_handupload(this)"><div class="img_"><img src="'+obj[key]['img']+'"></div><p>'+obj[key]['title']+'</p></a></div></div>';
                th_auto_upload_list_posts.innerHTML = tmp;
                console.log(th_auto_upload_list_posts);
                document.getElementById('th_auto_upload_list_posts').insertBefore(th_auto_upload_list_posts, document.getElementById('th_auto_upload_list_posts').childNodes[0]);
                if(typeof i!= 'undefined'){
                    i--;
                    return get_list_posts(arr[i], arr, i);
                }
            };
            http.send(null);
        }
        // autoupload handupload
        function autoupload_handupload(thiss){
            document.querySelectorAll('[data-upload="hand_upload"]')[0].click();
            document.getElementById('th_hand_upload_input_url').value = thiss.getAttribute('href');
            th_get_video(document.getElementById('th_hand_upload_input_url'), document.getElementById('th_hand_upload_input_url').value);
            thiss.parentElement.setAttribute('style', 'opacity:0.3');
            //document.getElementById('th_hand_upload_input_url').click();
            return false;
        }
    </script>
<?php }?>