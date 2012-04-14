README
======

This directory should be used to place project specfic documentation including
but not limited to project notes, generated API/phpdoc documentation, or
manual files generated or hand written.  Ideally, this directory would remain
in your development environment only and should not be deployed with your
application to it's final production location.


Setting Up Your VHOST
=====================

The following is a sample VHOST you might want to consider for your project.

<VirtualHost *:80>
   DocumentRoot "D:/Develop/PHP/WORK/comsenz/work/idc.comsenz.com/public"
   ServerName .local

   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV development

   <Directory "D:/Develop/PHP/WORK/comsenz/work/idc.comsenz.com/public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>

</VirtualHost>


查询接口（需认证）
-----------------
根据machine_id查
http://mgt.nix-adm.org/idc/equipment/jsonallequipments/queryType/machine_id/queryWord/HGWE9302
根据mac查
http://mgt.nix-adm.org/idc/equipment/jsonallequipments/queryType/mac/queryWord/00:AA:BB:CC:DD:EE
根据ip查
http://mgt.nix-adm.org/idc/equipment/jsonallequipments/queryType/ip/queryWord/124.238.249.10
