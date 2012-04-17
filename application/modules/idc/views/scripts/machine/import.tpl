<div class="mainContent">
    <div class="pageTitle">
        <{$this->title}>
    </div>
<form method="post">
    <textarea name="data" style="width:90%;height: 500px;">{
	"machineId":"001001",
	"hostname":"Server-3",
	"device_tag":"10.0.1.2",
	"hostMachine":"",
	"status":"0",
	"cpu": " Intel(R) Xeon(R) CPU  5110  @ 1.60GHz x 4",
	"memory":"7986M",
	"hardDisk":"146.8 GB,146.8 GB,",
	"serialNumber":" 2222",
	"productName":" PowerEdge 1950",
	"manufacturer":" Dell Inc.",
	"os":"CentOS release 5.5 (Final)",
	"plateform":"x86_64",
	"version":"x86_64",
	"account":[ "root","mysql" ],
	"networkInterface":[
	{
		"interface":"eth0",
		"speed":"1000Mb/s",
		"mac":"00:11:b1:e1:61:e1",
		"ip":["10.0.1.2/27"],
		"route":["0.0.0.0 0.0.0.0 10.0.1.1"]
	},
	{
		"interface":"eth1",
		"speed":"1000Mb/s",
		"mac":"01:19:b1:e1:61:e1",
		"ip":[],
		"route":[]
	}
	]}
</textarea><br />
    <input type="submit" value="导入" />
</form>
</div>