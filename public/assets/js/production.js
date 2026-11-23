$(document).ready(function()
{
    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end)
    {
    $('#daterange span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
    updateTable()
    }

    $('#daterange').daterangepicker(
    {
        startDate: start,
        endDate: end,
        ranges:
        {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);


    $('#fldb1, #slcs, #submit').hide();

    $('#ModelName,#lotNo,#machno,#device, #process').change(function(){
    updateTable()
    updateDocNo()
    })

});

function updateTable()
{
    resDate=convertDate()
    dateStart=resDate.dateStart
    dateEnd=resDate.dateEnd
    process=$('#process').val()
    model=$('#ModelName').val()
    lotNo=$('#lotNo').val()
    machno=$('#machno').val()
    console.log(dateStart,dateEnd,process,model,lotNo,machno);
    const xhr = new XMLHttpRequest();
    xhr.open("GET", (window.BASE_URL || '/') + "approval-data?dateStart="+resDate.dateStart+" 00:00:00"+"&dateEnd="+resDate.dateEnd+" 23:59:59"+"&process="+$('#process').val()+"&model="+model+"&lotno="+lotNo+"&machno="+machno, true);
    xhr.onload = (e) => {
    if (xhr.readyState === 4) {
        if (xhr.status === 200) {
        console.log("ini tagnya ",document.getElementById('process').value)
        var table = document.getElementById('table')
        $('#submit').show();
        table.innerHTML=xhr.responseText;
        } else {
        var table = document.getElementById('table')
        $('#submit').show();
        table.innerHTML="";
        console.error(xhr.statusText);
        }
    }
    };
    xhr.onerror = (e) => {
    console.error(xhr.statusText);
    };
    xhr.send(null);
}
function updateDocNo()
{
    var docNoValue={
    "fldb1":"FF-D1-004-21",
    "fldb2":"FF-D2-003-15",
    "flwb":"FF-WB-001",
    "flcsa":"FF-CS-004-20",
    "fltbc":"FF-TC-001-09",
    "fllc":"FF-LC-001-08",
    "sldbm":"SL-P1-001",
    "sldb1":"SL-D1-001",
    "sldb2":"SL-D2-001",
    "slld2":"SL-D2-004-01",
    "slpldsm":"SL-LM-001",
    "slildsm":"SL-LM-003-00",
    "slpag":"SL-D2-005-03",
    "slwb":"SL-WB-001",
    "slwbh":"SL-WB-008",
    "slcsm":"SL-MK-001-01",
    "slbi":"SL-BI-001-03",
    "slcs":"SL-CS-009",
    "slfcs":"SL-CS-009",
    "slht":"SL-HT-002-00",
    "sllt":"SL-LT-001-01",
    "slft":"SL-FT-007",
    "slft2":"SL-FT-001-03",
    "slvi":"SL-VI-001-03",
    "cp":"-",
    }
    var docNo = document.getElementById("docno")
    var processValue = document.getElementById("process").value
    console.log("process value ",processValue)
    console.log("doc no",docNoValue[processValue])
    docNo.value=docNoValue[processValue]
}

function convertDate()
{
    const date = $('#daterange span').html().split(" - ");
    var map = {
    "January":1,
    "February":2,
    "March":3,
    "April":4,
    "May":5,
    "June":6,
    "July":7,
    "August":8,
    "September":9,
    "October":10,
    "November":11,
    "December":12
    }
    const date0=date[0]
    const date1=date[1]

    const splitDate0=date0.split(" ")
    const splitDate1=date1.split(" ")

    const dateStart=splitDate0[2]+"-"+map[splitDate0[1]]+"-"+splitDate0[0]
    const dateEnd=splitDate1[2]+"-"+map[splitDate1[1]]+"-"+splitDate1[0]
    return {dateStart,dateEnd}
}

function changeDevice()
{
    var fl={
    "cp":"Choose Process",
    "fldb1":"FF-D1-004-21 (Die Bonding 1)",
    "fldb2":"FF-D2-003-15 (Die Bonding 2)",
    "flwb":"FF-WB-001 (Wire Bonding)",
    "flcsa":"FF-CS-004-20 (Cap Set Auto)",
    "fltbc":"FF-TC-001-09 (Tie Bar Cutting)",
    "fllc":"FF-LC-001-08 (Lead Cutting)"
}; 

// 15 process in single laser
var sl={
    "cp":"Choose Process",
    "sldbm":"SL-P1-001 (Die Bonding Machine)",
    "sldb1":"SL-D1-001 (LD Die Bonding 1 Machine)",
    "sldb2":"SL-D2-001 (LD Die Bonding 2 Machine)",
    "slld2":"SL-D2-004-01 (LD 2 Control)",
    "slpldsm":"SL-LM-001 (LDSM Machine)",
    "slildsm":"SL-LM-003-00 (LDSM QC)",
    "slpag":"SL-D2-005-03 (AG Paste Hardening)",
    "slwb":"SL-WB-001 (Wire Bonding)",
    "slwbh":"SL-WB-008 (Wire Bonding High Power)",
    "slcsm":"SL-MK-001-01 (Marking)",
    "slbi":"SL-BI-001-03 (Burn In)",
    "slcs":"SL-CS-009 (Cap Seal Automatic)",
    "slfcs":"SL-CS-009 (Foregoing Cap Seal)",
    "slht":"SL-HT-002-00 (Hot Test)",
    "sllt":"SL-LT-001-01 (Leak Testing)",
    "slft":"SL-FT-007 (Final Test (Blue Laser/ Green Laser/ Violet/ Cyan Laser))",
    "slft2":"SL-FT-001-03 (Final Test)",
    "slvi":"SL-VI-001-03 (Visual Inspection)"
};
    var docNo={
    "fldb1":"FF-D1-004-21",
    "fldb2":"FF-D2-003-15",
    "flwb":"FF-WB-001",
    "flcsa":"FF-CS-004-20",
    "fltbc":"FF-TC-001-09",
    "fllc":"FF-LC-001-08",
    "sldbm":"SL-P1-001",
    "sldb1":"SL-D1-001",
    "sldb2":"SL-D2-001",
    "slld2":"SL-D2-004-01",
    "slpldsm":"SL-LM-001",
    "slildsm":"SL-LM-003-00",
    "slpag":"SL-D2-005-03",
    "slwb":"SL-WB-001",
    "slwbh":"SL-WB-008",
    "slcsm":"SL-MK-001-01",
    "slbi":"SL-BI-001-03",
    "slcs":"SL-CS-009",
    "slfcs":"SL-CS-009",
    "slht":"SL-HT-002-00",
    "sllt":"SL-LT-001-01",
    "slft":"SL-FT-007",
    "slft2":"SL-FT-001-03",
    "slvi":"SL-VI-001-03",
    "cp":"-",
    }
    var device = document.getElementById("device").value
    var processValue = document.getElementById("process")

    

    if(device=="fl18"){
    for (var key in sl){
        for(let i=0;i<processValue.length;i++){
        if(processValue[i].value==key){
            processValue.remove(i);
            break;
        }
        }
    }
    for (var key in fl) {
        var option = document.createElement("option");
        option.text = fl[key]
        option.value=key
        if(processValue.length==0){
        processValue.add(option,processValue[0]);
        }
        else{
        processValue.add(option, processValue[processValue.length-1]);
        }
    }
    }
    else if(device=="sl56"){
    for (var key in fl){
        for(let i=0;i<processValue.length;i++){
        if(processValue[i].value==key){
            processValue.remove(i);
            break;
        }
        }
    }
    for (var key in sl) {
        var option = document.createElement("option");
        option.text = sl[key]
        option.value = key
        processValue.add(option, processValue[0]);
    }
    }else{
    var processValue=document.getElementById("process")
    var lengthProcessValue = processValue.length
    for(let i=0;i<lengthProcessValue;i++){
        processValue.remove(0);
    }
    }
}