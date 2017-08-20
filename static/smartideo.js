(function() {
    var smartideoTips = document.getElementsByClassName('smartideo-tips');
    if(smartideoTips.length > 0){
        var mobile = navigator.userAgent.toLowerCase().match(/(ipad|ipod|iphone|android|mmp|smartphone|midp|wap|xoom|symbian|j2me|blackberry|win ce)/i) != null;
        for(var i = 0; i < smartideoTips.length; i++){
            var smartideoTip = smartideoTips[i];
            var content = "";
            if(mobile){
                content = smartideoTip.getAttribute("smartideo-title-mobile");
            }else{
                content = smartideoTip.getAttribute("smartideo-title");
            }
            if(content == ""){
                smartideoTip.style.display = "none";
            }else{
                smartideoTip.innerHTML = content;
            }
        }
    }
})();