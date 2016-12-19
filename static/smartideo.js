window.onload = function(){
    if(document.getElementById('smartideo_tips')){
        var mobile = navigator.userAgent.toLowerCase().match(/(ipad|ipod|iphone|android|mmp|smartphone|midp|wap|xoom|symbian|j2me|blackberry|win ce)/i) != null;
        var tips = document.getElementById("smartideo_tips");
        var content = "";
        if(mobile){
            content = tips.getAttribute("smartideo-title-mobile");
        }else{
            content = tips.getAttribute("smartideo-title");
        }
        if(content == ""){
            tips.style.display = "none";
        }else{
            tips.innerHTML = content;
        }
    }
}
