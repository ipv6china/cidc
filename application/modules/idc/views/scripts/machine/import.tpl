<div class="mainContent">
    <div class="pageTitle">
        <{$this->title}>
    </div>
<form method="post">
    <textarea name="data" style="width:90%;height: 500px;">{
	"machineId":"A04EI21Y",
	"hostname":"NULL249-3",
	"device_tag":"124.238.249.3",
	"hostMachine":"",
	"status":"0",
	"cpu": " Intel(R) Xeon(R) CPU  5110  @ 1.60GHz x 4",
	"memory":"7986M",
	"hardDisk":"146.8 GB,146.8 GB,",
	"serialNumber":" 4HWL52X",
	"productName":" PowerEdge 1950",
	"manufacturer":" Dell Inc.",
	"os":"CentOS release 5.5 (Final)",
	"plateform":"x86_64",
	"version":"x86_64",
	"account":[ "root","amanda","mysql","nagios","cacti" ],
	"networkInterface":[
	{
		"interface":"eth0",
		"speed":" 1000Mb/s",
		"mac":"00:19:b9:e7:67:e6",
		"ip":["124.238.249.3/27"],
		"route":["0.0.0.0 0.0.0.0 124.238.249.1"]
	},
	{
		"interface":"eth1",
		"speed":" Unknown!",
		"mac":"00:19:b9:e7:67:e8",
		"ip":[],
		"route":[]
	}
	]}
</textarea><br />
    <input type="submit" value="导入" />
</form>
</div>