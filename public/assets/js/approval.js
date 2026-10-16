$(document).ready(function() {
  var start = moment().subtract(29, 'days');
  var end = moment();
  // changeDevice()
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
  

  $('#exampleModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget);
      var number = button.data('number');
      var process = button.data('process');
      var machno = button.data('machno');
      var modal = $(this);
      var loading = $('#loading');
      
      var title = process;
      if (machno && machno !== '-') {
          title += ' - Machine: ' + machno;
      }
      $('#modalTitle').text(title);

      loading.show();

      $.ajax({
          url: "/startup/history/" + number,
          dataType: 'html',
          success: function(data) {
              loading.hide();
              modal.find('.modal-body').html(data);
              
              var fetchedMachno = modal.find('#detail-machno').val();
              if (fetchedMachno && (!machno || machno === '-')) {
                  $('#modalTitle').text(process + ' - Machine: ' + fetchedMachno);
              }
          },
          error: function(data) {
              loading.hide();
              modal.find('.modal-body').html('<p>Error loading data</p>');
              console.log(data);
          },
      });
  });
});

function changeDevice()
{
    var device = document.getElementById("device").value
    
    
    $.ajax(
        {
            url: "/process/list?device="+device+"&type=startup",
            dataType:'JSON',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            success: function(data)
            {
                var processValue = document.getElementById("process")
                var processValueLen = processValue.length
                for(let i=0;i<processValueLen;i++){
                    processValue.remove(0);
                }
                var option = document.createElement("option");
                option.text = "All"
                option.value = ""
                processValue.add(option,processValue[0])
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

            },
            error: function(data)
            {
                console.log(data)
            },
        }
    );
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

function updateTable()
{
  let resDate,dateStart,dateEnd,process,model,lotNo,machno,device,typeProcess;

  resDate=convertDate()
  dateStart=resDate.dateStart+' 00:00:00'
  dateEnd=resDate.dateEnd+' 23:59:59'

  console.log(dateStart,dateEnd,process,model,lotNo,machno);
  const xhr = new XMLHttpRequest();
  xhr.open("GET", "/approve-form?dateStart="+dateStart+"&dateEnd="+dateEnd, true);
  xhr.onload = (e) => {
    if (xhr.readyState === 4) {
      if (xhr.status === 200) {
        var table = document.getElementById('table')
        table.innerHTML=xhr.responseText;
        new DataTable('#table-approval');
      } else {
        var table = document.getElementById('table')
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