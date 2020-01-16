<?php include_once("./_common.php") 

    // 다음 REST API값들 다 들고옴




?>
<link rel="stylesheet" href="<?= G5_URL ?>/thema/Basic/assets/bs3/css/bootstrap-apms.css"/>
<form id="mapForm">
    <table class="table">
        <tr>
            <td class="text-center mb-0" colspan="2">
                <p class="alert alert-danger">키워드가 아닌 주소명으로 검색해주세요. <br/>검색이 안될경우 마지막 주소를 빼주세요.</p>
            </td>
        </tr>
        <tr>
            <td class="text-center mb-0">위치</td>
            <td id="address_name"></td>
        </tr>
        <tr>
            <td class="text-center mb-0" style="vertical-align:inherit">장소검색</td>
            <td>
                <input type="text" name="location" class="form-control" autocomplete="off">
            </td>
        </tr>
        <tr>
            <td class="text-center mb-0">세부주소</td>
            <td>
                <input type="text" name="addr" class="form-control" autocomplete="off">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div id="maps">
                    <noscript>자바스크립트 설정을 켜주세요.</noscript>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <div class="container-sm text-center">
                    <button id="complete" class="btn btn-primary">입력완료</button>
                    <button type="reset" class="btn btn-danger">재입력</button>
                </div>
            </td>
        </tr>
    </table>
</form>



<script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=<?= $config['cf_kakao_js_apikey'] ?>"></script>
<script type="text/javascript" src="<?=G5_URL."/js/mapscript.js" ?>"></script>
<script>
    // 초기 맵 설정
    var container = document.getElementById("maps");
    var addrName = document.getElementById("address_name");
    addrName.innerText = '서울 종로구 종로';

    container.style.height = 383 + "px";
    var mapPosition = new kakao.maps.LatLng(37.57312947159552, 126.97923004370492);
    // 좌표와 레벨 지정
    var options = {
        center : mapPosition,
        level : 3
    };
    var map = new kakao.maps.Map(container, options);

    // 줌 컨트롤러 부착
    var control = new kakao.maps.ZoomControl();
    map.addControl(control, kakao.maps.ControlPosition.BOTTOMRIGHT);

    //마커 부착
    var marker = new kakao.maps.Marker({
        map: map,
        position: mapPosition
    });
    marker.setMap(map);

    var threadControl = false;
    mapForm.location.addEventListener("input", function(event){
        
        if(threadControl === false && event.target.value !== null && event.target.value !== undefined )
        {
            threadControl = true;

            setTimeout(function(){
                // 예외처리 
            if(event.target.value === '') {
                threadControl = false;
                return -1;
            }
            vanilaAjax("GET", "https://dapi.kakao.com/v2/local/search/address.json?query="+event.target.value, {"Authorization" : "<?= $config['cf_kakao_rest_key']; ?>" }, function(json){
                // CALLBACK AREA
                
                // 예외처리
                if(json['documents'].length === 0) {
                    threadControl = false;
                    return -1;
                }


                var regionName = json['documents'][0]['address_name'];
                // 타입체크 (ROAD와 다른점이 있다)
                var x = 0;
                var y = 0;

                if(json['documents'][0]['address_type'] === "ROAD")
                {
                    x = json['documents'][0]['road_address']['x'];
                    y = json['documents'][0]['road_address']['y'];
                }else {
                    x = json['documents'][0]['address']['x'];
                    y = json['documents'][0]['address']['y'];
                }
                

                marker.setPosition(new kakao.maps.LatLng(y, x));
                map.setCenter(new kakao.maps.LatLng(y, x));

                addrName.innerText = regionName;

                threadControl = false;
            }); // END OF FUNCTION
            
            }, 2000); //END OF TIMEOUT FUNC 

        }
        
       

    });

    // 폼 이벤트
    mapForm.addEventListener("submit", formValidationCheck);
    function formValidationCheck(event)
    {
        event.preventDefault();
        // 열었던 부모에게 값 전달
        opener.document.getElementById("wr_10").value = document.getElementById("address_name").innerText + "/" + mapForm.addr.value;
        window.close();
    }

    // 드래그 이벤트
    kakao.maps.event.addListener(map, 'dragend', function(){
        x = map.getCenter().getLng();
        y = map.getCenter().getLat();

        vanilaAjax("GET", "https://dapi.kakao.com/v2/local/geo/coord2regioncode.json?x="+x+"&y="+y, {"Authorization" : "<?= $config['cf_kakao_rest_key']; ?>" }, function(json){
                // CALLBACK AREA
                
                // 예외처리
                if(json['documents'].length === 0)  return -1;

                addrName.innerText = json['documents'][0]['address_name'];
            }); // END OF FUNCTION

        marker.setPosition(new kakao.maps.LatLng(y, x));
    });
    
</script>

