/**
 * 
 * @param {*} methodType - 메서드 타입입니다 ex) "GET", "POST" ... 
 * @param {*} requestUrl - 요청할 URL입니다. ex) "http://www.naver.com/" ...
 * @param {*} param - 보낼 파리미터입니다. ex) send(...)
 * @param {*} callbackFunc - 실행 할 메서드입니다. ex) 인자로받고 실행할 function입니다.
 */
function vanilaAjax(methodType, requestUrl, param, callbackFunc)
{
    let serverConnector = new XMLHttpRequest();

    serverConnector.open(methodType, requestUrl);
    serverConnector.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=utf-8");
    if(methodType=="GET") {
        serverConnector.setRequestHeader("Authorization", "KakaoAK " + param["Authorization"]);
        serverConnector.send();
    }
    else{
        serverConnector.send(param);
    }

    serverConnector.addEventListener("load", ()=>{
        let jsonData = JSON.parse(serverConnector.response);
        callbackFunc(jsonData);

    });

}