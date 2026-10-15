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

// Sinkronisasi jumlah input yang masih merah agar tombol Submit tidak pernah nyangkut
function syncErrorCount() {
    let cnt_error = document.getElementById("cnt-error-input");
    let container = document.getElementById("form-input") || document;
    let inputs = container.querySelectorAll("input, select");
    let totalError = 0;

    inputs.forEach(function(el) {
        if (!el.disabled && (el.dataset.hasError === "1" || el.style.backgroundColor === "red")) {
            totalError++;
        }
    });

    if (cnt_error) {
        cnt_error.value = totalError;
    }
    changeDisableSubmit(totalError > 0);
}

function changeRange(id,rangeMin,rangeMax) {
    console.log("ganti warna");
    let parameter = document.getElementById(id);
    if (!parameter) return;

    let rawVal = parameter.value.trim();

    // Jika input dikosongkan atau sedang disabled (gray-out), bersihkan error
    if (rawVal === '' || parameter.disabled) {
        parameter.dataset.hasError = "0";
        parameter.style.backgroundColor = parameter.disabled ? '#e9ecef' : '';
        syncErrorCount();
        return;
    }

    let value = parseFloat(rawVal);

    if(!isNaN(value) && value<=parseFloat(rangeMax) && value>=parseFloat(rangeMin)) {
        parameter.dataset.hasError = "0";
        parameter.style.backgroundColor = '';
    } else {
        parameter.dataset.hasError = "1";
        parameter.style.backgroundColor = 'red';
        alert("Input diluar batas ("+rangeMin+"-"+rangeMax+")");
    }

    syncErrorCount();
}

function changeRange2(id,rangeMin1,rangeMax1,rangeMin2,rangeMax2) {
    console.log("ganti warna");
    let parameter = document.getElementById(id);
    if (!parameter) return;

    let rawVal = parameter.value.trim();

    if (rawVal === '' || parameter.disabled) {
        parameter.dataset.hasError = "0";
        parameter.style.backgroundColor = parameter.disabled ? '#e9ecef' : '';
        syncErrorCount();
        return;
    }

    let value = parseFloat(rawVal);

    if(!isNaN(value) && ((value<=parseFloat(rangeMax1) && value>=parseFloat(rangeMin1)) || (value<=parseFloat(rangeMax2) && value>=parseFloat(rangeMin2)))) {
        parameter.dataset.hasError = "0";
        parameter.style.backgroundColor = '';
    } else {
        parameter.dataset.hasError = "1";
        parameter.style.backgroundColor = 'red';
        alert("Input diluar batas ("+rangeMin1+"-"+rangeMax1+") atau "+"("+rangeMin2+"-"+rangeMax2+")");
    }

    syncErrorCount();
}

function changeDisableSubmit(type){
    let submit_button = document.getElementById("submit");
    if (submit_button) {
        submit_button.disabled = type;
    }
}

// =========================================================================
// SL 5.6 Startup DB2 - Helper Gabung Satuan, Validasi Dinamis & Gray-out
// =========================================================================
function combineData(id) {
    let valElem = document.getElementById(id + '_val');
    let unitElem = document.getElementById(id + '_unit');
    let targetElem = document.getElementById(id);
    
    if (valElem && unitElem && targetElem) {
        if (valElem.value.trim() !== '') {
            targetElem.value = valElem.value.trim() + ' ' + unitElem.value;
        } else {
            targetElem.value = '';
        }
    }
}

function validateDynamicUnit(id) {
    let inputElem = document.getElementById(id + '_val');
    let unitElem = document.getElementById(id + '_unit');
    
    if (!inputElem || !unitElem) return;

    if (inputElem.value.trim() === '' || inputElem.disabled) {
        inputElem.dataset.hasError = "0";
        inputElem.style.backgroundColor = inputElem.disabled ? '#e9ecef' : '';
        syncErrorCount();
        return;
    }

    let unit = unitElem.value;
    let min, max;

    // Limit untuk Collet Load A & B
    if (id === 'par015' || id === 'par016') {
        if (unit === 'N') { min = 0.60; max = (id === 'par015' ? 1.00 : 1.40); }
        else if (unit === 'gr') { min = 60; max = (id === 'par015' ? 100 : 140); }
    } 
    // Limit untuk Air Pressure Analog & Digital
    else if (id === 'par018') {
        if (unit === 'MPa') { min = 0.01; max = 0.10; }
        else if (unit === 'kPa') { min = 10; max = 100; }
        else if (unit === 'kgf/cm^2') { min = 0.10; max = 1; }
    } 
    else if (id === 'par019') {
        if (unit === 'MPa') { min = 0.005; max = 0.10; }
        else if (unit === 'kPa') { min = 5; max = 100; }
        else if (unit === 'kgf/cm^2') { min = 0.05; max = 1; }
    }
    // Limit untuk Dispense Time Analog & Digital
    else if (id === 'par020') {
        if (unit === 'sec') { min = 0.01; max = 0.15; }
        else if (unit === 'ms') { min = 10; max = 150; }
    } 
    else if (id === 'par021') {
        if (unit === 'sec') { min = 0.005; max = 0.15; }
        else if (unit === 'ms') { min = 5; max = 150; }
    }
    // Limit untuk Suction Pressure Force Digital
    else if (id === 'par023') {
        if (unit === 'kPa') { min = -0.3; max = -0.1; }
        else if (unit === 'kgf/cm^2') { min = -0.03; max = -0.01; }
        else if (unit === 'mmHg') { min = -3; max = -1; }
    }

    changeRange(id + '_val', min, max);
}

function setFieldDisabledState(id, isDisabled) {
    let valElem = document.getElementById(id + '_val') || document.getElementById(id);
    let unitElem = document.getElementById(id + '_unit');
    let hiddenElem = document.getElementById(id);

    if (valElem) {
        if (isDisabled) {
            valElem.value = '';
            valElem.disabled = true;
            valElem.dataset.hasError = "0";
            valElem.style.backgroundColor = '#e9ecef';
            valElem.style.cursor = 'not-allowed';
        } else {
            // Jika sebelumnya terkunci (disabled), buka kembali
            if (valElem.disabled) {
                valElem.disabled = false;
                valElem.style.cursor = '';
                valElem.style.backgroundColor = '';
            } 
            // Jika dikosongkan dengan Backspace, hapus warna merah & status errornya
            else if (valElem.value.trim() === '' && !(valElem.validity && valElem.validity.badInput)) {
                valElem.dataset.hasError = "0";
                valElem.style.backgroundColor = '';
            }
        }
    }
    if (unitElem) {
        unitElem.disabled = isDisabled;
        unitElem.style.backgroundColor = isDisabled ? '#e9ecef' : '';
        unitElem.style.cursor = isDisabled ? 'not-allowed' : '';
    }
    if (isDisabled && hiddenElem && hiddenElem !== valElem) {
        hiddenElem.value = '';
    }
}

function togglePairLock(idA, idB) {
    let elemA = document.getElementById(idA + '_val') || document.getElementById(idA);
    let elemB = document.getElementById(idB + '_val') || document.getElementById(idB);

    if (!elemA || !elemB) return;

    let hasValA = (elemA.value.trim() !== '' && elemA.value.trim() !== '-') || (elemA.validity && elemA.validity.badInput);
    let hasValB = (elemB.value.trim() !== '' && elemB.value.trim() !== '-') || (elemB.validity && elemB.validity.badInput);

    if (hasValA && !hasValB) {
        setFieldDisabledState(idB, true);
        setFieldDisabledState(idA, false);
    } else if (hasValB && !hasValA) {
        setFieldDisabledState(idA, true);
        setFieldDisabledState(idB, false);
    } else if (!hasValA && !hasValB) {
        setFieldDisabledState(idA, false);
        setFieldDisabledState(idB, false);
    }

    syncErrorCount();
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