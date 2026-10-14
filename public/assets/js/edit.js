$(document).ready(function()
{
    // Pastikan injectForm dipanggil saat halaman dimuat
    injectForm();

    $('#submit').hide();
    
    if($('#msgsuccess').html() && $('#msgsuccess').html() != "Kosong"){
        alert($('#msgsuccess').html());
    }
    
    $('#process-txt').change(function(){
        updateDocNo()
    });
    
    $('#process-txt, #device-txt').change(function()
    {
        updateInput()
        updateDocNo()
    });
    
    // Auto fill operator data on load (Untuk Start dan Finish)
    if ($('#empid-txt').length > 0 && $('#empid-txt').val() !== '') {
        empAuto('#empid-txt','#shift-txt','#group-txt','#name-txt','#position-txt','empid-lbl');
    }
    if ($('#empid2-txt').length > 0 && $('#empid2-txt').val() !== '') {
        empAuto('#empid2-txt','#shift2-txt','#group2-txt','#name2-txt','#position2-txt','empid-lbl2');
    }
});

// Fungsi untuk Set Waktu Saat Ini
function now1() { if (document.getElementById('flexCheck1').checked == true) { document.getElementById('inp1').value= Date().slice(16,24); } else { document.getElementById('inp1').value= ""; } }
function now2() { if (document.getElementById('flexCheck2').checked == true) { document.getElementById('inp2').value= Date().slice(16,24); } else { document.getElementById('inp2').value= ""; } }
function now5() { if (document.getElementById('flexCheck5').checked == true) { document.getElementById('inp5').value= Date().slice(16,24); } else { document.getElementById('inp5').value= ""; } }
function now6() { if (document.getElementById('flexCheck6').checked == true) { document.getElementById('inp6').value= Date().slice(16,24); } else { document.getElementById('inp6').value= ""; } }

// Fungsi Auto Fill Karyawan (Telah disesuaikan 5 Kolom & Base URL)
function empAuto(empIdTag, shiftTag, groupTag, nameTag, positionTag, empIdlTag)
{
    var empid = $.trim($(empIdTag).val());
    if (empid.length > 0)
    {
        $.ajax({
            url: "/checkseet/home/ajaxAutofill", // Gunakan 'home' huruf kecil
            dataType:'JSON',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            data:"empid-txt=" + empid,
            success: function(data)
            {
                $(shiftTag).val(data.acc);
                $(empIdTag).val(data.empid);            
                $(groupTag).val(data.groupid);
                $(nameTag).val(data.name);
                
                // Cek jika API mengirimkan position
                if(data.position) {
                    $(positionTag).val(data.position);
                }
                
                const element = document.getElementById(empIdlTag);  
                if(element){
                    element.classList.remove("visible"); 
                    element.classList.add("invisible"); 
                }
                console.log("sukses auto fill");
            },
            error: function(data)
            {
                const element = document.getElementById(empIdlTag);  
                if(element){
                    element.classList.remove("invisible"); 
                    element.classList.add("visible"); 
                }
                $(shiftTag).val('');            
                $(groupTag).val('');
                $(nameTag).val('');
                $(positionTag).val('');
                console.log("gagal auto fill");
            },
        });
    }
    else
    {
        $(shiftTag).val('');
        $(groupTag).val('');
        $(nameTag).val('');
        $(positionTag).val('');
    }
}

// Fungsi Kalkulasi
function calc1()
{
    var elm = document.forms["checksheet"];
    if(elm["inp4"] && elm["inp3"]){
        elm["inp4"].max=elm["inp4"].value;

        if (elm["inp3"].value != "" && elm["inp4"].value != "")
        {
            if (parseInt(elm["inp4"].value) > parseInt(elm["inp3"].value))
            {
                elm["inp4"].value = parseInt(elm["inp3"].value);
            }
            else if(parseInt(elm["inp4"].value) < 0)
            {
                elm["inp4"].value = 0;
            }
            elm["inp5"].value = parseInt(elm["inp3"].value) - parseInt(elm["inp4"].value);
        }
    }
}

// Fungsi untuk Memanggil Form Check Item (Telah disesuaikan Base URL)
function injectForm()
{
    var deviceVal = document.getElementById('device-txt') ? document.getElementById('device-txt').value : '';
    var processVal = document.getElementById('process-txt') ? document.getElementById('process-txt').value : '';
    var numberVal = document.getElementById('number-form') ? document.getElementById('number-form').value : '';

    if(deviceVal === '' || processVal === '' || numberVal === ''){
        console.error("Gagal load form: device, process, atau number kosong!");
        return;
    }

    const xhr = new XMLHttpRequest();
    var urlTarget = "/checkseet/production/edit/form?device=" + deviceVal + "&process=" + processVal + "&number=" + numberVal;
    
    xhr.open("GET", urlTarget, true);
    xhr.onload = (e) => {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                var modal = document.getElementById('modal');
                if(modal){
                    modal.innerHTML = xhr.responseText;
                    $('#submit').show();
                }
            } else {
                var modal = document.getElementById('modal');
                if(modal){
                    modal.innerHTML = "<div class='alert alert-danger'>Gagal memuat form checklist (" + xhr.status + ").</div>";
                }
                $('#submit').hide();
                console.error("AJAX Error: ", xhr.statusText);
            }
        }
    };
    xhr.onerror = (e) => {
        console.error("Request gagal dieksekusi", xhr.statusText);
    };
    xhr.send(null);
}