$(document).ready(function()
{
    $.ajax(
        {
            url: "/checkseet/device/list",
            dataType:'JSON',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            success: function(data)
            {
                console.log(data);
                var deviceValue = document.getElementById("device")
                var deviceValueLen = deviceValue.length
                for(let i=0;i<deviceValueLen;i++){
                    deviceValue.remove(0);
                }
                for (let i=0;i<data.length;i++) {
                    var option = document.createElement("option");
                    option.text = data[i].name
                    option.value = data[i].code
                    if(deviceValue.length==0){
                        deviceValue.add(option,deviceValue[0]);
                    }
                    else{
                        deviceValue.add(option, deviceValue[deviceValue.length]);
                    }
                }
            },
            error: function(data)
            {
                console.log(data)
            },
        }
    );
});