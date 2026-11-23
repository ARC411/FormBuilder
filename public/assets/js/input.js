$(document).ready(function()
{
    // document.getElementById("empid-div2").style.display = "none";
    changeDevice()
    $('#submit').hide();
    if($('#msgsuccess').html()!="Kosong"){
        alert($('#msgsuccess').html());
    }

    $('#type-process-txt, #process-txt').change(function()
    {
        console.log("[DEBUG1] ",$('#process-txt').val())
        updateInput()
        updateDocNo()
    })
    $('#type-process-txt').change(function(){
        document.getElementById("form-action").action="/"+document.getElementById("type-process-txt").value
    })
    empAuto('#empid-txt','#shift-txt','#group-txt','#name-txt','empid-lbl')
});
function empAuto(empIdTag,shiftTag,groupTag,nameTag,empIdlTag)
{
    var empid = $.trim($(empIdTag).val());
    if (empid.length > 0)
    {
    $.ajax(
    {
        url: (window.BASE_URL || '/') + "home/ajaxAutofill",
        // type:'POST',
        dataType:'JSON',
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        data:"empid-txt=" + empid,
        success: function(data)
        {
            $(shiftTag).val(data.acc);
            $(empIdTag).val(data.empid);            
            $(groupTag).val(data.groupid);
            $(nameTag).val(data.name);
            const element = document.getElementById(empIdlTag);  
            element.classList.remove("visible"); 
            element.classList.add("invisible"); 
            console.log("sukses",data)
        },
        error: function(data)
        {
            const element = document.getElementById(empIdlTag);  
            element.classList.remove("invisible"); 
            element.classList.add("visible"); 
            $(shiftTag).val('');            
            $(groupTag).val('');
            $(nameTag).val('');
            console.log("gagal")
        },
    });
    }
    else
    {
    console.log("error 2")
    $(shiftTag).val('');
    $(groupTag).val('');
    // document.getElementById("empid-lbl").style.display = "none";
    }
}

function updateInput()
{
    let typeProcess,device,process;
    process=document.getElementById("process-txt").value
    console.log("process ",process)
    if(process!=null){
        device=process.split("-")[0]
        if(process.split("-")[1] == 'p'){
            document.getElementById('model-txt').disabled = false;
            document.getElementById('lotno-txt').disabled = false;
            typeProcess="production";
            console.log("disable truer eeruer")
        }
        else if(process.split("-")[1] == 'f'){
            document.getElementById('model-txt').disabled = false;
            document.getElementById('lotno-txt').disabled = false;
            typeProcess="foregoing";
        }
        else{
            document.getElementById('model-txt').disabled = true;
            document.getElementById('lotno-txt').disabled = true;
            typeProcess="startup";
        }
        document.getElementById("form-action").action="/"+typeProcess
    }
    let cnt_error = document.getElementById("cnt-error-input")
    let submit_button = document.getElementById("submit")
    submit_button.disabled=false
    cnt_error.value=0
    
    const xhr = new XMLHttpRequest();
    xhr.open("GET", (window.BASE_URL || '/') + "input/note-form/"+process, true);
    xhr.onload = (e) => {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                var form_input = document.getElementById('form-input')
                $('#submit').show();
                form_input.innerHTML=xhr.responseText;

                var processValue = document.getElementById("process-txt")
                var header_modal = document.getElementById('catatan-modal-header')
                header_modal.innerHTML=processValue.options[processValue.selectedIndex].text
                console.log("header modal ",processValue.options[processValue.selectedIndex].text)
                
            } else {
                var form_input = document.getElementById('form-input')
                $('#submit').hide();
                form_input.innerHTML="";
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
    var processValue = document.getElementById("process-txt")
    var text = processValue.options[processValue.selectedIndex].text;
    docNo.value=text.split(" ")[0]
}
function changeDevice()
{
    var docType = document.getElementById('type-process-txt').value
    var device = document.getElementById("device-txt").value
    
    console.log("ini doctype device",docType,device)
    
    $.ajax(
        {
            url: (window.BASE_URL || '/') + "process/list?device="+device+"&type="+docType,
            dataType:'JSON',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            success: function(data)
            {
                var processValue = document.getElementById("process-txt")
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
                console.log("[DEBUG2] ",$('#process-txt').val())
                updateInput()
                updateDocNo()
            },
            error: function(data)
            {
                console.log(data)
            },
        }
    );
}