function quantityChange1(){
    let input = document.getElementById('par013')
    let output = document.getElementById('par014')

    if( parseInt(output.value) > parseInt(input.value) ){
        output.value=input.value
    }

    let ng = document.getElementById('par015')
    let yield = document.getElementById('par016')

    ng.value = input.value-output.value
    yield.value = (output.value*100/input.value).toFixed(2)
}

function quantityChange2(){
    let input = document.getElementById('par013')
    let output = document.getElementById('par014')

    if(parseInt(output.value) > parseInt(input.value)){
        output.value=input.value
    }

    let yield = document.getElementById('par016')

    yield.value = ((output.value)*100/input.value).toFixed(2)
}

function quantityChange3(){
    let input = document.getElementById('par013')
    let output = document.getElementById('par014')

    if( parseInt(output.value) > parseInt(input.value) ){
        output.value=input.value
    }
}
function quantityChange4(){
    let input = document.getElementById('par013')
    let output = document.getElementById('par014')

    if( parseInt(output.value) > parseInt(input.value) ){
        output.value=input.value
    }

    let reject = document.getElementById('par015')
   
    reject.value = input.value-output.value
}

function quantityChange5(){
    let input = document.getElementById('par013')
    let output = document.getElementById('par014')

    if( parseInt(output.value) > parseInt(input.value) ){
        output.value=input.value
    }

    let reject = document.getElementById('par015')
    let fr = document.getElementById('par016')
    let yield = document.getElementById('par017')

    reject.value = input.value-output.value
    fr.value = (reject.value*100/input.value).toFixed(2)
    yield.value = (output.value*100/input.value).toFixed(2)

}

function quantityChange6(){
    let input = document.getElementById('par013');
    let output = document.getElementById('par014');

    if( parseInt(output.value) > parseInt(input.value) ){
        output.value=input.value
    }
}
function quantityChange7(){
    let input = document.getElementById('par013')
    let output = document.getElementById('par014')

    if( parseInt(output.value) > parseInt(input.value) ){
        output.value=input.value
    }

    let yield = document.getElementById('par015')
   
    yield.value = (output.value*100/input.value).toFixed(2)
}
function changeTime()
{
    let hour = document.getElementById('hh_2').value
    let minute = document.getElementById('mm_2').value

    let par010 = document.getElementById('par010')

    par010.value = hour + ':' + minute + ':' +'00'
    console.log("time is ",par010.value)
}

// Single Laser 5.6 DB1
function ausnSLDB1Change(){
    let input = document.getElementById('par017')
    let output = document.getElementById('par018')

    if(parseInt(output.value) > parseInt(input.value)){
        output.value=input.value
    }

    let others = document.getElementById('par019')
    let yield = document.getElementById('par020')

    others.value = input.value-output.value
    yield.value = (others.value*100/input.value).toFixed(2)
}

// Single Laser 5.6 SLBI

function quantitySLBIChange(){
    let pass = document.getElementById('par015')
    let reject = document.getElementById('par016')
    let yield = document.getElementById('par017')
    console.log(pass.value,reject.value,(parseInt(pass.value)+parseInt(reject.value)))
    yield.value = ((parseInt(pass.value))*100/(parseInt(pass.value)+parseInt(reject.value))).toFixed(2)

}

function quantitySettingSLBI(){
    let input = document.getElementById('par013')
    let setting = document.getElementById('par014')

    if(setting.value>input.value){
        setting.value=input.value
    }
    passResettingChange()
    rejectResettingChange()
}
function passResettingChange(){
    let pass = document.getElementById('par015')
    let reject = document.getElementById('par016')
    let yield = document.getElementById('par017')
    let setting = document.getElementById('par013')

    if(parseInt(pass.value) > parseInt(setting.value) ){
        pass.value=setting.value
    }

    reject.value = setting.value-pass.value
    yield.value = ((parseInt(pass.value))*100/(parseInt(pass.value)+parseInt(reject.value))).toFixed(2)
    console.log(yield.value)
}

function rejectResettingChange(){
    let pass = document.getElementById('par015')
    let reject = document.getElementById('par016')
    let yield = document.getElementById('par017')
    let setting = document.getElementById('par013')

    if(parseInt(reject.value) > parseInt(setting.value) ){
        reject.value=setting.value
    }

    pass.value = setting.value-reject.value
    yield.value = ((parseInt(pass.value))*100/(parseInt(pass.value)+parseInt(reject.value))).toFixed(2)
    console.log(yield.value)
}

function changeRange(id,rangeMin,rangeMax) {

    console.log("ganti warna");
    let parameter = document.getElementById(id);
    let value = parseFloat(parameter.value);
    let cnt_error = document.getElementById("cnt-error-input")
    let color_white = getComputedStyle(parameter).backgroundColor=="rgb(255, 255, 255)" 

    if(value<=parseFloat(rangeMax) && value>=parseFloat(rangeMin)) {
        if(!color_white){
            cnt_error.value=Math.max(parseInt(cnt_error.value)-1,0)
        }
        parameter.style.backgroundColor = '';
    } else {
        if(color_white){
            cnt_error.value=parseInt(cnt_error.value)+1
        }
        alert("Input diluar batas ("+rangeMin+"-"+rangeMax+")")
        parameter.style.backgroundColor = 'red';
    }

    console.log("ini total error ",cnt_error.value)
    if(cnt_error.value>0){
        changeDisableSubmit(true)
    }
    else{
        changeDisableSubmit(false)
        
    }
}

function changeRange2(id,rangeMin1,rangeMax1,rangeMin2,rangeMax2) {

    console.log("ganti warna");
    let parameter = document.getElementById(id);
    let value = parseFloat(parameter.value);
    let cnt_error = document.getElementById("cnt-error-input")
    let color_white = getComputedStyle(parameter).backgroundColor=="rgb(255, 255, 255)" 

    if((value<=parseFloat(rangeMax1) && value>=parseFloat(rangeMin1)) || (value<=parseFloat(rangeMax2) && value>=parseFloat(rangeMin2))) {
        if(!color_white){
            cnt_error.value=Math.max(parseInt(cnt_error.value)-1,0)
        }
        parameter.style.backgroundColor = '';
    } else {
        if(color_white){
            cnt_error.value=parseInt(cnt_error.value)+1
        }
        alert("Input diluar batas ("+rangeMin1+"-"+rangeMax1+") atau "+"("+rangeMin2+"-"+rangeMax2+")")
        parameter.style.backgroundColor = 'red';
    }

    console.log("ini total error ",cnt_error.value)
    if(cnt_error.value>0){
        changeDisableSubmit(true)
    }
    else{
        changeDisableSubmit(false)
        
    }
}

function changeDisableSubmit(type){
    let submit_button = document.getElementById("submit")
    submit_button.disabled=type
}

// FL 1.8 Die Bonding 2 - Die Bond Strength Standard
function updateDieBondStd() {
    var smChip = document.getElementById("sm_chip_type");
    if (!smChip) return;
    var chipType = smChip.value;
    var stdInput = document.getElementById("std_die_bond");
    var stdLabel = document.getElementById("label_die_bond_std");
    var input1st = document.getElementById("par017");
    var input2nd = document.getElementById("par018");
    
    var minVal = 0;
    
    if (chipType === "350") {
        minVal = 2.8;
    } else if (chipType === "450") {
        minVal = 3.6;
    } else if (chipType === "850") {
        minVal = 8.7;
    }
    
    if (stdInput) {
        stdInput.value = minVal > 0 ? minVal : "";
    }
    
    if (stdLabel) {
        stdLabel.innerHTML = minVal > 0 ? minVal : "-";
    }
    
    if (input1st) {
        input1st.setAttribute("min", minVal);
    }
    if (input2nd) {
        input2nd.setAttribute("min", minVal);
    }
}
