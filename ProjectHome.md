# 简介 #
CIDC是我们为了维护自己的服务器配置信息而开发的一套简单的管理程序，主要的功能是服务器硬件信息的维护，和服务器的分组，和按机房机柜等维度重新组织和展现服务器，并提供了查询接口，方便其他平台调用显示。

有使用方面的问题可以联系:  ipv6china#comsenz.com或QQ群 31799896(验证写cidc或wephi都可以)


# 功能简介 #
下面是这个系统的功能点

  * 管理服务器的硬件配置和ip设置
  * 为每个机器分配一个唯一的ID(MachineID)便于从购买到彻底退役期间的追踪
  * 树形导航功能,方便按地理位置/机房/机柜的方式组织服务器视图
  * 可以为每个机房,机柜添加备注信息,记录一些联系方式,去机房路线或定位方法
  * 服务器有几个状态,正常,损坏,闲置
  * 每个机器都有日志,每个变更都有日志
  * 机柜内可以用拖动的办法来标定与实际一样的物理顺序,同时也可以锁定防止误拖动
  * 使用json方式导入服务器,可以选择现有的shell脚本采集,或者单独实现采集工具
> <font color='red'>
</li></ul><ul><li>支持多台同时导入(json数组)<br>
</font>
  * 改动较多的服务器信息更新也可以使用json导入
  * 在主界面支持基于hostname(主机名)和ip地址的查找,查找的时候是支持模糊搜索和自动 补齐的
  * 提供了几个http的查找接口,实现基于mac地址,machineid,和ip的查找,方便其他平台调 用
  * 实现随意分组的功能,可以把机器归入不同的业务中
  * 有维护地址池的功能,地址池可以简单按公网和私网分类,也可以更详细的划分
  * 地址池功能按划分的网段自动聚合机器,并且提供每个网段的ip地址使用情况图
  * 所有带有ip地址的地方,都接通了ssh和putty的自动登陆功能
  * 提供了一个域名登记的界面,并且自动生成dns图,方便查找dns森林的冗余情况
  * 提供了日志检索的界面,日志按系统日志和手工生成日志进行分类
  * 提供了一个wephi画图工具,用于生成基于关系的结构化图形

# 简易使用手册 #
手册下载地址
http://cidc.googlecode.com/files/Comsenz.pdf

# 运行环境安装 #
CIDC运行环境是php+pdo\_mysql+mysql server，你还可以编译安装apc提供opcode缓存支持，以加速运行
下面是在freebsd上安装运行环境的简单介绍：

<font color='red>建议安装至少php-5.3的版本，早先的版本可能有一些莫名其妙的故障。</font'>


</li><li>apache 安装<br>
<blockquote>cd /usr/port/www/apache22</blockquote></li></ul>

<blockquote>make install clean

  * php和php扩展安装
> cd /usr/port/lang/php5

> make install clean WITHOUT\_X11=yes #要选择apache支持

cd /usr/port/lang/php5-extensions
make install clean WITHOUT\_X11=yes # JSON 支持不要忘记选，还有PDO和MYSQL

  * mysqlserver
> cd /usr/ports/databases/mysql55-server/

> make install clean

  * pdo-mysql支持(重要！无此支持，无法运行)
> cd /usr/ports/databases/php5-pdo\_mysql

> make install clean

  * php.ini修改
> > date.timezone = "Asia/Shanghai"


> # 典型的web server配置 #
public目录是web的根目录，剩下目录是框架的其他部分下面给出了两种常见webserver的配置文件样例

  * apache配置
> > apache需要增加支持 .htaccess的功能，CIDC是单入口程序,在public目录下有一个.htaccess文件提供了rewrite规则，如果你使用其他web server，则需要做对应的rewrite规则转换。

下面是一个在大多数情况下都工作，但不一定安全的apache配置
```
<VirtualHost *:80>
    ServerAdmin cidc@cidc.com
    DocumentRoot "/www/cidcomsenz.com/public"
    ServerName cidc.comsenz.com
<Directory "/www/cidc.comsenz.com/public">
 
    Options Indexes FollowSymLinks

    AllowOverride All

    Order allow,deny
    Allow from all
</Directory>

</VirtualHost>
```

  * nginx 配置
如果你使用的是nginx web 服务器，可以参考下面的典型配置
```
    server {
        listen       80;
        server_name  cidc.comsenz.com;



        location / {
            root   /www/idc.comsenz.com/public;
            index  index.php index.html index.htm;

             if (!-f $request_filename)
               {
                  rewrite (.*) /index.php;
               }
          }
 

        location ~ \.php$ {
            root           /www/idc.comsenz.com/public;
            fastcgi_pass   127.0.0.1:9000;
            fastcgi_index  index.php;
            fastcgi_param  SCRIPT_FILENAME  /www/idc.comsenz.com/public/$fastcgi_script_name;
            include        fastcgi_params;
        }

    }

```

# CIDC代码安装 #

> 在需要安装的目录，导出svn即可
> svn export http://cidc.googlecode.com/svn/trunk/ cidc-read-only

访问站点首页，会跳转到 /install 安装页面，按照提示安装即可

如果网速不太快，也可以直接去Downloads下载最新打包好的代码进行安装

# CIDC代码 #
CIDC使用了zend框架，还有EXTJS 这些都已经集成到了代码中，所以代码现在有点大。

公开版本的CIDC登陆功能是临时做的，在安装的时候初始化了一个管理账户用来登陆，如需要更多的用户登陆可以自己重新实现登陆部分。

# CIDC服务器信息的采集和录入 #
录入的格式是json，所以你可以选择用任何语言生成json格式的服务器信息，我们是用shell实现的。
运行的方式是：

> elinks --source http://xxx.com/getm-json.sh|bash

脚本依赖dmidecode,elinks几个工具，如果你系统上缺少，可以单独安装。如果你不是redhat或cents系统，你可以修改或重新实现一个采集程序 。

<font color='red'>如果在你自己的平台上实现了一个更好的采集程序，并且愿意贡献给大家，可以把代码mail给我 <ipv6china@comsenz.com></font>

这个脚本可以从Downloads连接下载
```
 # wget http://cidc.googlecode.com/files/getm-json.sh -O getm-json.sh
```

如果导入有错误，可以使用json viewer来检查一下json文本错误在什么地方：
http://jsonviewer.codeplex.com/

# CIDC的WEB查询接口 #

  * 根据machine\_id查
http://cidc.comsenz.com/api/query/t/machine_id/w/HGWE9302

  * 根据mac查
http://cidc.comsenz.com/api/query/t/mac/w/00:AA:BB:CC:DD:EE

  * 根据ip查
http://cidc.comsenz.com/api/query/t/ip/w/192.168.0.10

返回的结果均为json，需要自己解析

# 常见问题 #
  * 安装后500错误
> > 可能没有安装pdo\_mysql支持

  * 安装成功了，但导入的时候提示json错误，即使用样例的json也不行
> > 这可能php版本有点低，尝试升级到5.3.x试一下，我们的运行环境是freebsd，所以当时直接就用了ports里的php53

  * 访问控制
> > 建议在实际应用的时候，做IP限制，只允许公司的地址和可信任网络访问。
  * 从一个可信任的ip自动提交机器信息到系统
> > 这需要把那个可信任ip加入白名单,library/App/CommonController.php 的28行
> > > `  $whiteIps = array('127.10.0.1','10.0.4.71'); `


> 这个白名单也是在无认证查询api的时候使用的。
> 设置好后，你可以使用脚本直接提交到系统，而无需手工导入机器。
  * 关于点击自动登陆服务器
> > 这个是靠putty+密钥代理实现的，目前支持的浏览器有ie和firefox，所以在linux和windows上都可以用。
> > 设置ie支持这个功能，需要把cidc的域名加进“Intranet”区域，并降低安全级别，以便运行js脚本。
> > 设置firefox支持则需要打开about:config配置，然后把signed.applets.codebase\_principal\_support设置为true


> 设置好浏览器后，把ssh私钥加入密钥代理就可以使用这个功能了。
  * 同时导入多台服务器的json
> cidc是支持多台机器同时导入的，做成json数组就可以了，默认是一台  { },两台就是 [{}，{} ](.md)  ，多台继续加就可以了

## Demo ##
http://cidc.demo.jzland.com
用户：admin
密码：admin888