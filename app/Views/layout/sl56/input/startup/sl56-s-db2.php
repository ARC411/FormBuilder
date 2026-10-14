<div class="table-responsive" id="slvi">
<?= csrf_field(); ?>

<?php 
// =========================================================================================
// FUNGSI BANTUAN PHP (DIJALANKAN SAAT LOAD HALAMAN)
// Fungsi ini bertugas memecah teks dari database (misal: "0.05 MPa") menjadi 2 bagian: 
// 1. Angka ("0.05") untuk ditaruh di input angka.
// 2. Teks ("MPa") untuk memilih dropdown secara otomatis.
// =========================================================================================
function getValUnit($alldata, $param) {
    $result = ['val' => '', 'unit' => ''];
    if (isset($alldata) && isset($alldata[0][$param]) && $alldata[0][$param] !== '') {
        $parts = explode(' ', trim($alldata[0][$param]));
        $result['val'] = $parts[0];
        if (count($parts) > 1) {
            unset($parts[0]);
            $result['unit'] = implode(' ', $parts);
        }
    }
    return $result;
}
?>

<input type="hidden" id="number-edit" name="number-edit" value=<?php if(isset($alldata)){echo $number;} ?>>
    <table class="table table-striped-columns">
    <thead align="center">
        <tr>
            <th colspan="19" scope="col"><?php echo nl2br("LD Die Bonding 2")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" >No</th>
            <th colspan="2" scope="col">Start up Check Item</th>
            <th scope="col" >Standard</th>
            <th scope="col" >Data</th>
        </tr>
    </thead>

    <tbody>
        <tr>
          <td rowspan="2">1</td>
          <td rowspan="2" colspan="2">@ Transfer Air Pressure</td>
          <td rowspan="1">A: 340-490 MPa</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            onchange="if(this.value !== '') changeRange('par001',340,490);" 
            oninput="let b=document.getElementById('par002'); if(this.value && b.value){ this.setCustomValidity('ERROR: Tidak boleh isi keduanya!'); this.reportValidity(); this.style.background='#ffcccc'; b.style.background='#ffcccc'; } else { this.setCustomValidity(''); b.setCustomValidity(''); this.style.background=''; b.style.background=''; }" 
            placeholder="" id="par001" name="par001" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par001'];} ?>>
          </td>
        </tr>
        
        <tr>
          <td rowspan="1">B: 0.34-0.49 MPa</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            onchange="if(this.value !== '') changeRange('par002',0.34,0.49);" 
            oninput="let a=document.getElementById('par001'); if(this.value && a.value){ this.setCustomValidity('ERROR: Tidak boleh isi keduanya!'); this.reportValidity(); this.style.background='#ffcccc'; a.style.background='#ffcccc'; } else { this.setCustomValidity(''); a.setCustomValidity(''); this.style.background=''; a.style.background=''; }" 
            placeholder="" id="par002" name="par002" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par002'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="4">2</td>
          <td rowspan="4">@ Chip-clean Air Pressure</td>
          <td rowspan="2">Chip</td>
          <td >A: 60 - 150 MPa</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            onchange="if(this.value !== '') changeRange('par003',60,150);" 
            oninput="let b=document.getElementById('par004'); if(this.value && b.value){ this.setCustomValidity('ERROR: Tidak boleh isi keduanya!'); this.reportValidity(); this.style.background='#ffcccc'; b.style.background='#ffcccc'; } else { this.setCustomValidity(''); b.setCustomValidity(''); this.style.background=''; b.style.background=''; }" 
            placeholder="" id="par003" name="par003" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par003'];} ?>>
          </td>
        </tr>

        <tr>
          <td >B: 0.06 - 0.150 MPa</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            onchange="if(this.value !== '') changeRange('par004',0.06,0.15);" 
            oninput="let a=document.getElementById('par003'); if(this.value && a.value){ this.setCustomValidity('ERROR: Tidak boleh isi keduanya!'); this.reportValidity(); this.style.background='#ffcccc'; a.style.background='#ffcccc'; } else { this.setCustomValidity(''); a.setCustomValidity(''); this.style.background=''; a.style.background=''; }" 
            placeholder="" id="par004" name="par004" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par004'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="2">Stem</td>
          <td >A: 340 - 490 MPa</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            onchange="if(this.value !== '') changeRange('par005',340,490);" 
            oninput="let b=document.getElementById('par006'); if(this.value && b.value){ this.setCustomValidity('ERROR: Tidak boleh isi keduanya!'); this.reportValidity(); this.style.background='#ffcccc'; b.style.background='#ffcccc'; } else { this.setCustomValidity(''); b.setCustomValidity(''); this.style.background=''; b.style.background=''; }" 
            placeholder="" id="par005" name="par005" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par005'];} ?>>
          </td>
        </tr>

        <tr>
          <td >B: 0.340 - 0.490 MPa</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            onchange="if(this.value !== '') changeRange('par006',0.340,0.490);" 
            oninput="let a=document.getElementById('par005'); if(this.value && a.value){ this.setCustomValidity('ERROR: Tidak boleh isi keduanya!'); this.reportValidity(); this.style.background='#ffcccc'; a.style.background='#ffcccc'; } else { this.setCustomValidity(''); a.setCustomValidity(''); this.style.background=''; a.style.background=''; }" 
            placeholder="" id="par006" name="par006" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par006'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="4">3</td>
          <td rowspan="4">@ Vacuum Pressure</td>
          <td rowspan="2">Chip</td>
          <td >A: -80 kPa atau kurang</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            onchange="if(this.value !== '') changeRange('par007',-99999999999,-80);" 
            oninput="let b=document.getElementById('par008'); if(this.value && b.value){ this.setCustomValidity('ERROR: Tidak boleh isi keduanya!'); this.reportValidity(); this.style.background='#ffcccc'; b.style.background='#ffcccc'; } else { this.setCustomValidity(''); b.setCustomValidity(''); this.style.background=''; b.style.background=''; }" 
            placeholder="" id="par007" name="par007" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par007'];} ?>>
          </td>
        </tr>

        <tr>
          <td >B: -80 kPa atau kurang</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            onchange="if(this.value !== '') changeRange('par008',-99999999999,-80);" 
            oninput="let a=document.getElementById('par007'); if(this.value && a.value){ this.setCustomValidity('ERROR: Tidak boleh isi keduanya!'); this.reportValidity(); this.style.background='#ffcccc'; a.style.background='#ffcccc'; } else { this.setCustomValidity(''); a.setCustomValidity(''); this.style.background=''; a.style.background=''; }" 
            placeholder="" id="par008" name="par008" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par008'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="2">Stem</td>
          <td >A: -70 kPa atau kurang</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            onchange="if(this.value !== '') changeRange('par009',-99999999999,-70);" 
            oninput="let b=document.getElementById('par010'); if(this.value && b.value){ this.setCustomValidity('ERROR: Tidak boleh isi keduanya!'); this.reportValidity(); this.style.background='#ffcccc'; b.style.background='#ffcccc'; } else { this.setCustomValidity(''); b.setCustomValidity(''); this.style.background=''; b.style.background=''; }" 
            placeholder="" id="par009" name="par009" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par009'];} ?>>
          </td>
        </tr>

        <tr>
          <td >B: -70 kPa atau kurang</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            onchange="if(this.value !== '') changeRange('par010',-99999999999,-70);" 
            oninput="let a=document.getElementById('par009'); if(this.value && a.value){ this.setCustomValidity('ERROR: Tidak boleh isi keduanya!'); this.reportValidity(); this.style.background='#ffcccc'; a.style.background='#ffcccc'; } else { this.setCustomValidity(''); a.setCustomValidity(''); this.style.background=''; a.style.background=''; }" 
            placeholder="" id="par010" name="par010" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par010'];} ?>>
          </td>
        </tr>
        
        <tr>
          <td rowspan="3">4</td>
          <td rowspan="3" colspan="2">* Collet Cleaning <br> (Bersihkan dengan Cotton Bud yang dibasahi dengan alkohol)</td>
          <td >Collet A</td>
          <td>
            <select class="form-control" id="par011" name="par011">
              <option value="">-- Pilih --</option>
              <option value="✔" <?php if(isset($alldata) && $alldata[0]['par011'] == '✔'){echo 'selected';} ?>>✔ (OK)</option>
              <option value="✖" <?php if(isset($alldata) && $alldata[0]['par011'] == '✖'){echo 'selected';} ?>>✖ (NG)</option>
            </select>
          </td>
        </tr>

        <tr>
          <td >Collet B</td>
          <td>
            <select class="form-control" id="par012" name="par012">
              <option value="">-- Pilih --</option>
              <option value="✔" <?php if(isset($alldata) && $alldata[0]['par012'] == '✔'){echo 'selected';} ?>>✔ (OK)</option>
              <option value="✖" <?php if(isset($alldata) && $alldata[0]['par012'] == '✖'){echo 'selected';} ?>>✖ (NG)</option>
            </select>
          </td>
        </tr>

        <tr>
          <td >Middle Collet</td>
          <td>
            <select class="form-control" id="par013" name="par013">
              <option value="">-- Pilih --</option>
              <option value="✔" <?php if(isset($alldata) && $alldata[0]['par013'] == '✔'){echo 'selected';} ?>>✔ (OK)</option>
              <option value="✖" <?php if(isset($alldata) && $alldata[0]['par013'] == '✖'){echo 'selected';} ?>>✖ (NG)</option>
            </select>
          </td>
        </tr>

        <tr>
          <td >5</td>
          <td colspan="3">* Pengecekan kondisi collet guide (Check apakah collet guide goyang)</td>
          <td>
            <select class="form-control" id="par014" name="par014">
              <option value="">-- Pilih --</option>
              <option value="✔" <?php if(isset($alldata) && $alldata[0]['par014'] == '✔'){echo 'selected';} ?>>✔ (OK)</option>
              <option value="✖" <?php if(isset($alldata) && $alldata[0]['par014'] == '✖'){echo 'selected';} ?>>✖ (NG)</option>
            </select>
          </td>
        </tr>

        <!-- ========================================================================= -->
        <!-- MULAI DARI SINI: PARAMETER DENGAN DROPDOWN (SKENARIO C)                   -->
        <!-- Perubahan: validateDynamicUnit DIHAPUS dari onkeyup agar tidak overreact. -->
        <!-- ========================================================================= -->

        <tr>
          <td rowspan="2">6</td>
          <td rowspan="2" colspan="2">@Colled Load</td>
          <td >Collet A : 0.60 - 1.00 N (60-100 gr)</td>
          <?php $p015 = getValUnit(isset($alldata) ? $alldata : null, 'par015'); ?>
          <td>
            <div class="input-group">
                <!-- HIDDEN INPUT: Inilah yang akan disubmit ke database (name="par015") -->
                <input type="hidden" id="par015" name="par015" value="<?= isset($alldata) ? $alldata[0]['par015'] : '' ?>">
                
                <!-- INPUT UI ANGKA: onchange untuk validasi merah, onkeyup HANYA untuk gabung teks -->
                <input class="form-control" type="number" step="0.000000001" id="par015_val" value="<?= $p015['val'] ?>" 
                       onchange="combineData('par015'); validateDynamicUnit('par015')" 
                       onkeyup="combineData('par015')" placeholder="">
                       
                <!-- SELECT UI SATUAN -->
                <select class="form-control" id="par015_unit" onchange="combineData('par015'); validateDynamicUnit('par015')" style="max-width: 80px; padding: 0.375rem;">
                    <option value="N" <?= $p015['unit'] == 'N' ? 'selected' : '' ?>>N</option>
                    <option value="gr" <?= $p015['unit'] == 'gr' ? 'selected' : '' ?>>gr</option>
                </select>
            </div>
          </td>
        </tr>

        <tr>
          <td >Collet B : 0.60 - 1.40 N (60-140 gr)</td>
          <?php $p016 = getValUnit(isset($alldata) ? $alldata : null, 'par016'); ?>
          <td>
            <div class="input-group">
                <input type="hidden" id="par016" name="par016" value="<?= isset($alldata) ? $alldata[0]['par016'] : '' ?>">
                
                <input class="form-control" type="number" step="0.000000001" id="par016_val" value="<?= $p016['val'] ?>" 
                       onchange="combineData('par016'); validateDynamicUnit('par016')" 
                       onkeyup="combineData('par016')" placeholder="">
                       
                <select class="form-control" id="par016_unit" onchange="combineData('par016'); validateDynamicUnit('par016')" style="max-width: 80px; padding: 0.375rem;">
                    <option value="N" <?= $p016['unit'] == 'N' ? 'selected' : '' ?>>N</option>
                    <option value="gr" <?= $p016['unit'] == 'gr' ? 'selected' : '' ?>>gr</option>
                </select>
            </div>
          </td>
        </tr>

        <tr>
          <td >7</td>
          <td colspan="3">* Pengecekan die bonding monitor <br>(Check posisi cursor pada LD chip dengan melihat monitor)</td>
          <td>
            <select class="form-control" id="par017" name="par017">
              <option value="">-- Pilih --</option>
              <option value="✔" <?php if(isset($alldata) && $alldata[0]['par017'] == '✔'){echo 'selected';} ?>>✔ (OK)</option>
              <option value="✖" <?php if(isset($alldata) && $alldata[0]['par017'] == '✖'){echo 'selected';} ?>>✖ (NG)</option>
            </select>
          </td>
        </tr>

        <tr>
          <td rowspan="6">8</td>
          <td rowspan="6">@ Dispenser indication</td>
          <td rowspan="2">Air Pressure</td>
          <td >Analog : 0.01-0.10 MPa (10-100 kPa) / 0.10 - 1 kgf/cm^2</td>
          <?php $p018 = getValUnit(isset($alldata) ? $alldata : null, 'par018'); ?>
          <td>
            <div class="input-group">
                <input type="hidden" id="par018" name="par018" value="<?= isset($alldata) ? $alldata[0]['par018'] : '' ?>">
                
                <input class="form-control" type="number" step="0.000000001" id="par018_val" value="<?= $p018['val'] ?>" 
                       onchange="combineData('par018'); validateDynamicUnit('par018')" 
                       onkeyup="combineData('par018')" placeholder="">
                       
                <select class="form-control" id="par018_unit" onchange="combineData('par018'); validateDynamicUnit('par018')" style="max-width: 95px; padding: 0.375rem;">
                    <option value="MPa" <?= $p018['unit'] == 'MPa' ? 'selected' : '' ?>>MPa</option>
                    <option value="kPa" <?= $p018['unit'] == 'kPa' ? 'selected' : '' ?>>kPa</option>
                    <option value="kgf/cm^2" <?= $p018['unit'] == 'kgf/cm^2' ? 'selected' : '' ?>>kgf/cm²</option>
                </select>
            </div>
          </td>
        </tr>

        <tr>
          <td >Digital : 0.005-0.10 MPa (5-100 kPa) / 0.05 - 1 kgf/cm^2</td>
          <?php $p019 = getValUnit(isset($alldata) ? $alldata : null, 'par019'); ?>
          <td>
            <div class="input-group">
                <input type="hidden" id="par019" name="par019" value="<?= isset($alldata) ? $alldata[0]['par019'] : '' ?>">
                
                <input class="form-control" type="number" step="0.000000001" id="par019_val" value="<?= $p019['val'] ?>" 
                       onchange="combineData('par019'); validateDynamicUnit('par019')" 
                       onkeyup="combineData('par019')" placeholder="">
                       
                <select class="form-control" id="par019_unit" onchange="combineData('par019'); validateDynamicUnit('par019')" style="max-width: 95px; padding: 0.375rem;">
                    <option value="MPa" <?= $p019['unit'] == 'MPa' ? 'selected' : '' ?>>MPa</option>
                    <option value="kPa" <?= $p019['unit'] == 'kPa' ? 'selected' : '' ?>>kPa</option>
                    <option value="kgf/cm^2" <?= $p019['unit'] == 'kgf/cm^2' ? 'selected' : '' ?>>kgf/cm²</option>
                </select>
            </div>
          </td>
        </tr>

        <tr>
          <td rowspan="2">Dispense Time</td>
          <td >Analog : 0.01-0.15 sec / 10-150 ms</td>
          <?php $p020 = getValUnit(isset($alldata) ? $alldata : null, 'par020'); ?>
          <td>
            <div class="input-group">
                <input type="hidden" id="par020" name="par020" value="<?= isset($alldata) ? $alldata[0]['par020'] : '' ?>">
                
                <input class="form-control" type="number" step="0.000000001" id="par020_val" value="<?= $p020['val'] ?>" 
                       onchange="combineData('par020'); validateDynamicUnit('par020')" 
                       onkeyup="combineData('par020')" placeholder="">
                       
                <select class="form-control" id="par020_unit" onchange="combineData('par020'); validateDynamicUnit('par020')" style="max-width: 80px; padding: 0.375rem;">
                    <option value="sec" <?= $p020['unit'] == 'sec' ? 'selected' : '' ?>>sec</option>
                    <option value="ms" <?= $p020['unit'] == 'ms' ? 'selected' : '' ?>>ms</option>
                </select>
            </div>
          </td>
        </tr>
        
        <tr>
          <td >Digital : 0.005-0.15 sec / 5-150 ms</td>
          <?php $p021 = getValUnit(isset($alldata) ? $alldata : null, 'par021'); ?>
          <td>
            <div class="input-group">
                <input type="hidden" id="par021" name="par021" value="<?= isset($alldata) ? $alldata[0]['par021'] : '' ?>">
                
                <input class="form-control" type="number" step="0.000000001" id="par021_val" value="<?= $p021['val'] ?>" 
                       onchange="combineData('par021'); validateDynamicUnit('par021')" 
                       onkeyup="combineData('par021')" placeholder="">
                       
                <select class="form-control" id="par021_unit" onchange="combineData('par021'); validateDynamicUnit('par021')" style="max-width: 80px; padding: 0.375rem;">
                    <option value="sec" <?= $p021['unit'] == 'sec' ? 'selected' : '' ?>>sec</option>
                    <option value="ms" <?= $p021['unit'] == 'ms' ? 'selected' : '' ?>>ms</option>
                </select>
            </div>
          </td>
        </tr>

        <!-- ========================================================================= -->
        <!-- KEMBALI KE PARAMETER NORMAL TANPA DROPDOWN SATUAN                         -->
        <!-- ========================================================================= -->

        <tr>
          <td rowspan="2">Suction Pressure Force</td>
          <td >Analog : kurang dari 1/6 dari posisi Off</td>
          <td>
            <select class="form-control" id="par022" name="par022">
              <option value="">-- Pilih --</option>
              <option value="✔" <?php if(isset($alldata) && $alldata[0]['par022'] == '✔'){echo 'selected';} ?>>✔ (OK)</option>
              <option value="✖" <?php if(isset($alldata) && $alldata[0]['par022'] == '✖'){echo 'selected';} ?>>✖ (NG)</option>
            </select>
          </td>
        </tr>
        
        <tr>
          <td >Digital : -0.3 ~ -0.1 kPa / -0.03 ~ -0.01 kgf/cm^2 / -3 ~ -1 mmHg</td>
          <td>
            <select class="form-control" id="par023" name="par023">
              <option value="">-- Pilih --</option>
              <option value="✔" <?php if(isset($alldata) && $alldata[0]['par023'] == '✔'){echo 'selected';} ?>>✔ (OK)</option>
              <option value="✖" <?php if(isset($alldata) && $alldata[0]['par023'] == '✖'){echo 'selected';} ?>>✖ (NG)</option>
            </select>
          </td>
        </tr>

        <tr>
          <td rowspan="2">9</td>
          <td rowspan="2" colspan="2">* Check kondisi Ag Paste</td>
          <td >Posisi</td>
          <td>
            <select class="form-control" id="par024" name="par024">
              <option value="">-- Pilih --</option>
              <option value="✔" <?php if(isset($alldata) && $alldata[0]['par024'] == '✔'){echo 'selected';} ?>>✔ (OK)</option>
              <option value="✖" <?php if(isset($alldata) && $alldata[0]['par024'] == '✖'){echo 'selected';} ?>>✖ (NG)</option>
            </select>
          </td>
        </tr> 

        <tr>
          <td >Quantity</td>
          <td>
            <select class="form-control" id="par025" name="par025">
              <option value="">-- Pilih --</option>
              <option value="✔" <?php if(isset($alldata) && $alldata[0]['par025'] == '✔'){echo 'selected';} ?>>✔ (OK)</option>
              <option value="✖" <?php if(isset($alldata) && $alldata[0]['par025'] == '✖'){echo 'selected';} ?>>✖ (NG)</option>
            </select>
          </td>
        </tr> 

        <tr>
          <td rowspan="2">10</td>
          <td rowspan="2" colspan="2">* Check arah Ion Blower Fan</td>
          <td >Posisi blower</td>
          <td>
            <select class="form-control" id="par026" name="par026">
              <option value="">-- Pilih --</option>
              <option value="✔" <?php if(isset($alldata) && $alldata[0]['par026'] == '✔'){echo 'selected';} ?>>✔ (OK)</option>
              <option value="✖" <?php if(isset($alldata) && $alldata[0]['par026'] == '✖'){echo 'selected';} ?>>✖ (NG)</option>
            </select>
          </td>
        </tr> 

        <tr>
          <td >Putaran blower</td>
          <td>
            <select class="form-control" id="par027" name="par027">
              <option value="">-- Pilih --</option>
              <option value="✔" <?php if(isset($alldata) && $alldata[0]['par027'] == '✔'){echo 'selected';} ?>>✔ (OK)</option>
              <option value="✖" <?php if(isset($alldata) && $alldata[0]['par027'] == '✖'){echo 'selected';} ?>>✖ (NG)</option>
            </select>
          </td>
        </tr> 

        <tr>
          <td rowspan="1">11</td>
          <td rowspan="1" colspan="2">Cleaning Machine</td>
          <td >Bersihkan mesin dan lingkungan sekitarnya dari debu dan kotoran</td>
          <td>
            <select class="form-control" id="par028" name="par028">
              <option value="">-- Pilih --</option>
              <option value="✔" <?php if(isset($alldata) && $alldata[0]['par028'] == '✔'){echo 'selected';} ?>>✔ (OK)</option>
              <option value="✖" <?php if(isset($alldata) && $alldata[0]['par028'] == '✖'){echo 'selected';} ?>>✖ (NG)</option>
            </select>
          </td>
        </tr> 

        <tr>
          <td rowspan="1">12</td>
          <td rowspan="1" colspan="2">@ Needle Push Up Level</td>
          <td >0.2 ~ 0.6 mm</td>
          <td>
            <input class="form-control" type="number" step="0.0000001" onchange="changeRange('par029',0.2,0.6)" placeholder="" id="par029" name="par029" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par029'];} ?>>
          </td>
        </tr> 
    
        <tr>
          <td colspan="4">Note</td>
          <td>
            <input class="form-control" type="text" placeholder="Tambahkan catatan jika ada" id="par030" name="par030" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par030'].'"';} ?>>
          </td>
        </tr>

      </tbody>   
    </table>
</div>

<!-- ========================================================================= -->
<!-- JAVASCRIPT: LOGIC PENGGABUNGAN DATA & VALIDASI WARNA                      -->
<!-- ========================================================================= -->
<script>
/**
 * Fungsi: combineData(id)
 * Tujuan: Menggabungkan nilai angka (misal: "0.05") dengan satuannya (misal: "MPa")
 *         lalu memasukkannya ke Hidden Input agar bisa disubmit ke database.
 */
function combineData(id) {
    let valElem = document.getElementById(id + '_val');    // Input UI (Angka)
    let unitElem = document.getElementById(id + '_unit');  // Input UI (Dropdown)
    let targetElem = document.getElementById(id);          // Hidden Input (ke DB)
    
    if (valElem && unitElem && targetElem) {
        if (valElem.value !== '') {
            targetElem.value = valElem.value + ' ' + unitElem.value; // Hasil: "0.05 MPa"
        } else {
            targetElem.value = ''; // Kosongkan jika angkanya dihapus
        }
    }
}

/**
 * Fungsi: validateDynamicUnit(id)
 * Tujuan: Merubah parameter min/max untuk validasi warna merah sesuai satuan.
 *         Kemudian memanggil fungsi bawaan changeRange().
 */
function validateDynamicUnit(id) {
    let inputElem = document.getElementById(id + '_val');
    let unitElem = document.getElementById(id + '_unit');
    
    if (!inputElem || !unitElem || inputElem.value === '') return;

    let unit = unitElem.value;
    let min, max;

    // -- Limit untuk Collet Load A & B --
    if (id === 'par015' || id === 'par016') {
        if (unit === 'N') { min = 0.60; max = (id==='par015'?1.00:1.40); }
        else if (unit === 'gr') { min = 60; max = (id==='par015'?100:140); }
    } 
    // -- Limit untuk Air Pressure Analog & Digital --
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
    // -- Limit untuk Dispense Time Analog & Digital --
    else if (id === 'par020') {
        if (unit === 'sec') { min = 0.01; max = 0.15; }
        else if (unit === 'ms') { min = 10; max = 150; }
    } 
    else if (id === 'par021') {
        if (unit === 'sec') { min = 0.005; max = 0.15; }
        else if (unit === 'ms') { min = 5; max = 150; }
    }

    // Panggil fungsi validasi warna/alert bawaan aplikasi
    // Menggunakan ID UI (contoh: 'par015_val') agar warnanya muncul di input form di layar
    if (typeof changeRange === "function") {
        changeRange(id + '_val', min, max);
    }
}
</script>