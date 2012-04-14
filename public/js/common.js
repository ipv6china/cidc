//note userAgent
var BROWSER = {};
var USERAGENT = navigator.userAgent.toLowerCase();
BROWSER.ie = window.ActiveXObject && USERAGENT.indexOf('msie') != -1 && USERAGENT.substr(USERAGENT.indexOf('msie') + 5, 3);
BROWSER.firefox = document.getBoxObjectFor && USERAGENT.indexOf('firefox') != -1 && USERAGENT.substr(USERAGENT.indexOf('firefox') + 8, 3);
BROWSER.chrome = window.MessageEvent && !document.getBoxObjectFor && USERAGENT.indexOf('chrome') != -1 && USERAGENT.substr(USERAGENT.indexOf('chrome') + 7, 10);
BROWSER.opera = window.opera && opera.version();
BROWSER.safari = window.openDatabase && USERAGENT.indexOf('safari') != -1 && USERAGENT.substr(USERAGENT.indexOf('safari') + 7, 8);
BROWSER.other = !BROWSER.ie && !BROWSER.firefox && !BROWSER.chrome && !BROWSER.opera && !BROWSER.safari;
BROWSER.firefox = BROWSER.chrome ? 1 : BROWSER.firefox;

function copyCode(s)
{
    if (window.clipboardData)
    {
        window.clipboardData.setData('text',s.val());
        alert('复制成功！');
    }
    else
    {
        alert('你的浏览器不支持脚本复制或你拒绝了浏览器安全确认。请尝试[Ctrl+C]复制代码并粘贴到要加入功能的页面。');
    }
}

//统一调用接口
function runPutty(ip){
    var port = arguments[1]?arguments[1]:22;
    var username = arguments[2]?arguments[2]:'root';

    var userAgent;
    userAgent = getUserAgent();
    switch (userAgent)
    {
        case "IE":
            return runPuttyForIE(ip,port,username);
            break;
        case "FIREFOX":
            return runPuttyForFireFox(ip,port,username);
            break;
        case "CHROME":
            return runPuttyForChrome(ip,port,username);
            break;
        default:
            alert('暂不支持您的系统');
            break;
    }
}

//for Chrome
function runPuttyForChrome(ip,port,username){
    alert('Chrome 不支持本地程序调用，请使用IE或FireFox');
    return;
}

//for FireFox
function runPuttyForFireFox(ip,port,username)
{
    //about:config 设置 signed.applets.codebase_principal_support = TRUE;
    var os = getOs();
    var puttyPath;
    if('Windows' == os){
        puttyPath = 'C:\\Windows\\putty.exe';
    }else{
        puttyPath = '/usr/bin/putty';
    }
    try{
        netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
        var file = Components.classes["@mozilla.org/file/local;1"]
        .createInstance(Components.interfaces.nsILocalFile);
        file.initWithPath(puttyPath);
        var process=Components.classes['@mozilla.org/process/util;1']
        .createInstance(Components.interfaces.nsIProcess);
        process.init(file);
        var arguments = [ip,'-l',username,'-P',port];
        process.run(false,arguments,arguments.length);
    }catch(e){
        var help = "注意事项：\n"
        +"   1. 请确认安装了 putty 软件,并且路径正确\n"
        +"        Windows: C:\\Windows\\putty.exe \n"
        +"        linux|Mac: usr/bin/putty\n"
        +"   2. 确认 FireFox 设置正确\n"
        +"       在地址栏输入：about:config\n"
        +"   signed.applets.codebase_principal_support=TRUE\n";
        alert(help);
        alert(e);
    }
}

//for IE
function runPuttyForIE(ip,port,username)
{
    var strPath = 'file:///C:/Windows/putty.exe '+ip+' -P '+port+' -l '+username;
    try{
        var objShell = new ActiveXObject("wscript.shell");
        objShell.Run(strPath);
        objShell = null;
    }catch(e){
         var help = "注意事项：\n"
        +"   1. 请确认安装了 putty 软件,并且路径正确\n"
        +"        Windows: C:\\Windows\\putty.exe \n"
        +"        linux|Mac: usr/bin/putty\n"
        +"   2. 请把本网站加入受信任的站点\n"
        +"       Internat 选项 -> 安全 -> 受信任的站点 \n";
        alert(help);
        alert(e);
    }
}

//判断用户浏览器类型
function getUserAgent(){
    var userAgent = navigator.userAgent.toLowerCase();
    if(!/opera/.test(userAgent) && /msie/.test(userAgent)){
        return "IE";
    }
    if(!/webkit/.test(userAgent) && /gecko/.test(userAgent)){
        return "FIREFOX";
    }
    if(/\bchrome\b/.test(userAgent)){
        return "CHROME";
    }
    return 'OTHER';
}

//判断OS
function getOs(){
    var userAgent = navigator.userAgent.toLowerCase();
    if(/windows|win32/.test(userAgent)){
        return "Windows";
    }
    if(/linux/.test(userAgent)){
        return "Mac";
    }
    if(/macintosh|mac os x/.test(userAgent)){
        return "Mac";
    }
}

function implode (glue, pieces) {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Waldo Malqui Silva
    // +   improved by: Itsacon (http://www.itsacon.net/)
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: implode(' ', ['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: 'Kevin van Zonneveld'
    // *     example 2: implode(' ', {first:'Kevin', last: 'van Zonneveld'});
    // *     returns 2: 'Kevin van Zonneveld'
    var i = '',
    retVal = '',
    tGlue = '';
    if (arguments.length === 1) {
        pieces = glue;
        glue = '';
    }
    if (typeof(pieces) === 'object') {
        if (Object.prototype.toString.call(pieces) === '[object Array]') {
            return pieces.join(glue);
        }
        for (i in pieces) {
            retVal += tGlue + pieces[i];
            tGlue = glue;
        }
        return retVal;
    }
    return pieces;
}