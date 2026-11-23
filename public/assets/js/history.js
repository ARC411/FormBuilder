$(document).ready(function()
{
  
  var start = moment().subtract(29, 'days');
  var end = moment();
  changeDevice()
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

  $('#ModelName,#lotNo,#machno, #process,#type-process').change(function(){
    console.log("chanbge procesafsd")
    updateTable()
    updateDocNo()
  })

});

    function updateTable()
    {
      let resDate,dateStart,dateEnd,process,model,lotNo,machno,device,typeProcess;

      resDate=convertDate()
      dateStart=resDate.dateStart
      dateEnd=resDate.dateEnd

      process=document.getElementById("process").value
      console.log("process ",process)
      model=$('#ModelName').val()
      lotNo=$('#lotNo').val()
      machno=$('#machno').val()

      device=""

      if(process!=null){
        console.log("ini type process ",process)
        device=process.split("-")[0]
        if(process.split("-")[1] == 'p'){
          typeProcess="production";
        }
        else if(process.split("-")[1] == 'f'){
          typeProcess="foregoing";
        }
        else{
          typeProcess="startup";
        }

      }
      
      console.log(dateStart,dateEnd,process,model,lotNo,machno);
      const xhr = new XMLHttpRequest();
      xhr.open("GET", "/"+typeProcess+"/data?dateStart="+resDate.dateStart+" 00:00:00"+"&dateEnd="+resDate.dateEnd+" 23:59:59"+"&process="+process+"&model="+model+"&lotno="+lotNo+"&machno="+machno+"&device="+device, true);
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
        var docNo = document.getElementById("docno")
        var processValue = document.getElementById("process")
        var text = processValue.options[processValue.selectedIndex].text;
        docNo.value=text.split(" ")[0]
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
    var docType = document.getElementById('type-process').value
    var device = document.getElementById("device").value
    
    console.log("ini doctype device",docType,device)
    
    $.ajax(
        {
            url: (window.BASE_URL || '/') + "process/list?device="+device+"&type="+docType,
            dataType:'JSON',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            success: function(data)
            {
                var processValue = document.getElementById("process")
                var processValueLen = processValue.length
                for(let i=0;i<processValueLen;i++){
                    processValue.remove(0);
                }
                for (let i=0;i<data.length;i++) {
                    var option = document.createElement("option");
                    option.text = data[i].name
                    option.value = data[i].process_code
                    if(processValue.length==0){
                        processValue.add(option,processValue[0]);
                    }
                    else{
                        processValue.add(option, processValue[processValue.length]);
                    }
                }
                updateTable()
                updateDocNo()

            },
            error: function(data)
            {
                console.log(data)
            },
        }
    );
}

function copytable() {
  var urlField = document.getElementById(document.getElementById("process").value)   
  console.log("amanananan")
  console.log(urlField)
  var range = document.createRange()
  range.selectNode(urlField)
  window.getSelection().addRange(range) 
  document.execCommand('copy')
}

function copy_clipboard() {
  var table = document.getElementById(document.getElementById("process").value);
  console.log(table)
  var range,selection
  
  if(document.createRange && window.getSelection) {
    range=document.createRange()
    selection = window.getSelection()
    selection.removeAllRanges()

    try {
      range.selectNodeContents(table);
      selection.addRange(range)
    } catch (e) {
      range.selectNode(table)
      selection.addRange(range)
    }

    document.execCommand('copy')
    selection.removeAllRanges()
    alert('Table copied to clipboard.')
  }
  else{
    range=document.body.createTextRange()
    range.moveToElementText(table)
    range.select()
    range.execCommand('copy')
    alert('Table copied to clipboard.')
    
  }
}

function download_table_as_csv() {
  var process = document.getElementById("process").value
  console.log("prosesss ",process)
  var cnt_table = document.getElementById("cnt-table").value
  console.log("cnt-table ",cnt_table)
  if(cnt_table==1){
    var table1 = document.getElementById("table1");
    book = TableToExcel.tableToBook(table1,{sheet:{name:"Page 1"}})
    TableToExcel.save(book, process+".xlsx")
  }
  else if(cnt_table==2){
    console.log("masuk dua")
    var table1 = document.getElementById("table1");
    var table2 = document.getElementById("table2");
    book = TableToExcel.tableToBook(table1,{sheet:{name:"Page 1"}})
    TableToExcel.tableToSheet(book,table2,{sheet:{name:"Page 2"}});
    TableToExcel.save(book, process+".xlsx")
  }
  // table2 = document.getElementById("flcsa");
  // book = TableToExcel.tableToBook(table1, {sheet:{name:"sheet1"}});
  // TableToExcel.tableToSheet(book, table2, {sheet:{name:"sheet2"}});
  // TableToExcel.save(book, "test.xlsx")
} 

document.getElementById("menu-show").addEventListener("click",function(){
  console.log("menu show clicked");
  if ( document.getElementById("menu").classList.contains('show') ){
    document.getElementById("menu").classList.remove('show');
  }
  else{
    document.getElementById("menu").classList.add('show')
  }
})