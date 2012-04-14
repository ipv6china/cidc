<{$this->doctype()}>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><{$this->title}> - Comsenz IDC System</title>
        <link rel="shortcut icon" href="/favicon.ico"/>
        <link rel="stylesheet" type="text/css" href="<{$this->baseUrl('/')}>js/ext/resources/css/ext-all.css" />
        <link href="<{$this->baseUrl('/')}>style/style.css" media="screen" rel="stylesheet" type="text/css" />
        <link href="<{$this->baseUrl('/')}>style/icons.css" media="screen" rel="stylesheet" type="text/css" />
        <link href="<{$this->baseUrl('/')}>style/ext-patch.css" media="screen" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="<{$this->baseUrl('/')}>js/ext/bootstrap.js"></script>
        <script type="text/javascript" src="<{$this->baseUrl('/')}>js/ext/locale/ext-lang-zh_CN.js"></script>
        <script type="text/javascript" src="<{$this->baseUrl('/')}>js/common.js"></script>
        <script type="text/javascript" src="<{$this->baseUrl('/')}>js/etao.js"></script>
    </head>
    <body>
        <div class="mainBody">
            <div class="mainNav">
                <div class="logo"></div>
                <ul>
                    <li<{if "idc" == $moduleName && "index" == $controllerName}> class="current"<{/if}>><a href="<{$this->url(['module'=>'idc','controller' => 'index', 'action' => 'index'])}>" class="">首页</a></li>
                    <li<{if "idc" == $moduleName && "group" == $controllerName}> class="current"<{/if}>><a href="<{$this->baseUrl('idc/group')}>">分组</a></li>
                    <li<{if "idc" == $moduleName && "network" == $controllerName}> class="current"<{/if}>><a href="<{$this->baseUrl('idc/network')}>">网络</a></li>
                    <li<{if "idc" == $moduleName && "cabinet" == $controllerName}> class="current"<{/if}>><a href="<{$this->baseUrl('idc/cabinet')}>">设备</a></li>
                    <li style="display:none;" <{if "idc" == $moduleName && "machine" == $controllerName}> class="current"<{/if}>><a href="<{$this->baseUrl('idc/machine')}>">服务器</a></li>
                    <li style="display:none;" <{if "idc" == $moduleName && "networkequipment" == $controllerName}> class="current"<{/if}>><a href="<{$this->baseUrl('idc/networkequipment')}>">网络设备</a></li>
                    <li<{if "idc" == $moduleName && "domain" == $controllerName}> class="current"<{/if}>><a href="<{$this->baseUrl('idc/domain')}>">域名</a></li>
                    <li<{if "idc" == $moduleName && "log" == $controllerName}> class="current"<{/if}>><a href="<{$this->baseUrl('idc/log')}>">日志</a></li>
                    <li<{if "tools" == $moduleName && "graphviz" == $controllerName}> class="current"<{/if}>><a href="<{$this->baseUrl('tools')}>">工具</a></li>
                    <li<{if "user" == $moduleName && "user" == $controllerName}> class="current"<{/if}>><a href="<{$this->baseUrl('user')}>">用户</a></li>
                </ul>
            </div>
            <div id="content">
                <{if is_array($this->messages) && count($this->messages) > 0}>
                    <div class="message">
                        <{foreach key="key" item="message" from=$this->messages}>
                            <div class="info"><{$message}></div>
                        <{/foreach}>
                    </div>
                <{/if}>
                <{$this->layout()->content}>
                <div id="footer">
                    <div class="copy">&copy; 2011 Comsenz Inc.</div>
                </div>
            </div>
        </div>
    </body>
</html>
