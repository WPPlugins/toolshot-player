jwplayer.key = "dWwDdbLI0ul1clbtlw+4/UHPxlYmLoE9Ii9QEw==";
/* deleteCache */
function deleteCache(){
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if (this.responseText==1) {
            location.reload();
        }
    };
    xhttp.open('GET', url_toolshot_player+'main/delete_cache?url='+url_video_md5, true);
    xhttp.send();
}
/* ads */
if(typeof tc_text != 'undefined' && tc_text['ads_type']){
    switch(tc_text['ads_type']){
        case 'banner':
            document.getElementById('toolshot_ads').setAttribute('style', 'left:calc(50% - '+(tc_text['ads_width'].match(/\d+/g)/2)+'px);width:'+tc_text['ads_width']+';height:'+tc_text['ads_height']+';display:block;bottom:60px;');
            break;
        case 'banner_close_and_play':
            document.getElementById('toolshot_ads').setAttribute('style', 'left:calc(50% - '+(tc_text['ads_width'].match(/\d+/g)/2)+'px);top:calc(50% - '+(tc_text['ads_height'].match(/\d+/g)/2)+'px);width:'+tc_text['ads_width']+';height:'+tc_text['ads_height']+';display:block;');
            document.getElementsByClassName('toolshot_ads_close_')[0].setAttribute('onclick', 'document.getElementById(\'toolshot_ads\').outerHTML=\'\';jwplayer().play();');
            btn_close_and_play = document.createElement('div');
            btn_close_and_play.setAttribute('class', 'toolshot_ads_btn_close_and_play_');
            btn_close_and_play.innerHTML = '<button onclick="document.getElementById(\'toolshot_ads\').outerHTML=\'\';jwplayer().play();">Close And Play</button>';
            document.getElementById('toolshot_ads').appendChild(btn_close_and_play);
            tc_text['autoplay'] = 'false';
            break;
    }
}
/* player settup */
if(typeof url_video != 'undefined') {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        var obj_player = {
            sources: eval(xhttp.responseText),
            allowfullscreen: true,
            autostart: (tc_text['autoplay'] == 'true' ? true : false),
            responsive: true,
            primary: "html5",
            width: "100%",
            height: "100%",
            skin: {name: tc_text['skin']},
            sharing: {
                link: document.URL
            }
        };
        var player = jwplayer("mediaplayer");
        player["setup"](obj_player);
        /* button */
        jwplayer().on('ready', function () {
            var leftGroup = document.getElementsByClassName('jw-controlbar-left-group')[0];
            rewind = 10;
            if (tc_text['rewind'] == 'true') {
                var myRewButton = document.createElement("div");
                myRewButton.id = "myRewButton";
                myRewButton.setAttribute('class', 'jw-icon jw-icon-inline jw-button-color jw-reset icon-rewind');
                myRewButton.setAttribute('onclick', 'jwplayer().seek(jwplayer().getPosition()-' + rewind + ')');
                leftGroup.insertBefore(myRewButton, leftGroup.childNodes[1]);
            }
            fast_forward = 10;
            if (tc_text['fast_forward'] == 'true') {
                var myFFButton = document.createElement("div");
                myFFButton.id = "myFFButton";
                myFFButton.setAttribute('class', 'jw-icon jw-icon-inline jw-button-color jw-reset icon-fast-forward');
                myFFButton.setAttribute('onclick', 'jwplayer().seek(jwplayer().getPosition()+' + fast_forward + ')');
                leftGroup.insertBefore(myFFButton, leftGroup.childNodes[2]);
            }
            if (tc_text['download'] == 'true') {
                var download_ = document.createElement("div");
                download_.setAttribute('class', 'jw-icon jw-icon-inline jw-button-color jw-reset icon-download3');
                download_.setAttribute('onclick', 'window.open("'+url_toolshot_player+'download/?' + url_video + '&referrer=' + window.location.href + '","_blank")');
                document.getElementsByClassName('jw-group jw-controlbar-left-group jw-reset')[0].appendChild(download_);
            }
        });
        /* error */
        jwplayer().on('error', function () {
            deleteCache();
        });
    };
    xhttp.open("GET", url_toolshot_player+"json_video?" + url_video, true);
    xhttp.send(null);
}
/* logo */
if(typeof tc_text != 'undefined') {
    var tmp = '';
    switch (tc_text['logo_position']) {
        case 'topleft':
            tmp += 'top:' + tc_text['logo_y'].match(/\d+/g) + '%;';
            tmp += 'left:' + tc_text['logo_x'].match(/\d+/g) + '%;';
            break;
        case 'topright':
            tmp += 'top:' + tc_text['logo_y'].match(/\d+/g) + '%;';
            tmp += 'right:' + tc_text['logo_x'].match(/\d+/g) + '%;';
            break;
        case 'bottomleft':
            tmp += 'bottom:' + tc_text['logo_y'].match(/\d+/g) + '%;';
            tmp += 'left:' + tc_text['logo_x'].match(/\d+/g) + '%;';
            break;
        case 'bottomright':
            tmp += 'bottom:' + tc_text['logo_y'].match(/\d+/g) + '%;';
            tmp += 'right:' + tc_text['logo_x'].match(/\d+/g) + '%;';
            break;
    }
    if (tc_text['logo'].match(/^https?\:\/\//gim)) tmp += 'width:' + tc_text['logo_size'].match(/\d+/g) + '%;';
    else tmp += 'font-size:' + (tc_text['logo_size'].match(/\d+/g) * 2 / 10) + 'em;'
    document.getElementsByClassName('toolshot_logo_')[0].setAttribute('style', tmp);
    tmp = '<a href="' + tc_text['logo_url'] + '">';
    tmp += (tc_text['logo'].match(/^https?\:\/\//gim) ? '<img src="' + tc_text['logo'] + '">' : '<span>' + tc_text['logo'] + '</span>');
    tmp += '</a>';
    document.getElementsByClassName('toolshot_logo_')[0].innerHTML = tmp;
}