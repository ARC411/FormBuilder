$(document).ready(function()
{
    // Memanggil form saat load pertama kali
    injectForm();

    $('#submit').hide();
    if($('#msgsuccess').html() && $('#msgsuccess').html() != "Kosong"){
        alert($('#msgsuccess').html());
    }
    $('#process-txt').change(function(){
        updateDocNo()
    });
    $('#process-txt,#device-txt').change(function()
    {
        updateInput()
        updateDocNo()
    });
    
    // Autofill data diri jika emp-id sudah ada isinya
    if ($('#empid-txt').length > 0 && $('#empid-txt').val() !== '') {
        empAuto();
    }
});

function empAuto()
{
    var empid = $.trim($("#empid-txt").val());
    if (empid.length > 0)
    {
    $.ajax(
    {
        url: "/checkseet/home/ajaxAutofill",
        dataType:'JSON',
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        data:"empid-txt=" + empid,
        success: function(data)
        {
            $('#shift-txt').val(data.acc);
            $('#empid-txt').val(data.empid);            
            $('#group-txt').val(data.groupid); 
            
            // PERBAIKAN: Menyesuaikan dengan ID baru untuk mengisi Nama dan Posisi
            $('#name-txt').val(data.name); 
            if(data.position) {
                $('#position-txt').val(data.position);
            }

            const element = document.getElementById("empid-lbl");  
            if(element){
                element.classList.remove("visible"); 
                element.classList.add("invisible"); 
            }
            console.log("sukses autofill")
        },
        error: function(data)
        {
            const element = document.getElementById("empid-lbl");  
            if(element){
                element.classList.remove("invisible"); 
                element.classList.add("visible"); 
            }
            $('#shift-txt').val('');            
            $('#group-txt').val('');
            $('#name-txt').val('');
            $('#position-txt').val('');
            console.log("gagal autofill")
        },
    });
    }
    else
    {
        $('#shift-txt').val('');
        $('#group-txt').val('');
        $('#name-txt').val('');
        $('#position-txt').val('');
    }
}

function injectForm()
{
    var processVal = document.getElementById('process-form') ? document.getElementById('process-form').value : '';
    var numberVal = document.getElementById('number-edit') ? document.getElementById('number-edit').value : '';

    if(numberVal === ''){
        console.error("Gagal load form: ID (number) kosong!");
        return;
    }

    const xhr = new XMLHttpRequest();
    // Perbaikan: Tambahkan base folder /checkseet
    var urlTarget = "/checkseet/startup/edit/form?number=" + numberVal;
    
    xhr.open("GET", urlTarget, true);
    xhr.onload = (e) => {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                var modal = document.getElementById('modal')
                if(modal){
                    modal.innerHTML=xhr.responseText;
                    $('#submit').show();
                }
            } else {
                var modal = document.getElementById('modal')
                if(modal){
                    modal.innerHTML="<div class='alert alert-danger'>Gagal memuat form checklist (" + xhr.status + ").</div>";
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