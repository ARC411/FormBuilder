<!-- FLDB1 -->
<div class="table-responsive">
  <?= csrf_field(); ?>
  <input type="hidden" name="cnt-proses" value="1">
  <input type="hidden" id="number-edit" name="number-edit" value="<?php if(isset($alldata)){echo $number;} ?>">
  <input type="hidden" id="par016" name="par016" value="<?php if(isset($alldata)){echo htmlspecialchars($alldata[0]['par016'] ?? '');} ?>">
  <?php $currentStd = isset($alldata) ? ($alldata[0]['std_die_bond'] ?? '') : ''; ?>

  <!-- Pilihan Jenis SM-Chip -->
  <div class="mb-3 row align-items-center">
    <label for="sm_chip_type" class="col-sm-2 col-form-label fw-bold">Jenis SM-Chip</label>
    <div class="col-sm-4">
      <select class="form-control" id="sm_chip_type" name="sm_chip_type" onchange="updateDieBondStd()" required>
          <option value="" disabled <?= empty($currentStd) ? 'selected' : '' ?>>-- Pilih Jenis SM-Chip --</option>
          <option value="350" <?= ($currentStd == '2.8' || $currentStd == '350') ? 'selected' : '' ?>>350 μm</option>
          <option value="450" <?= ($currentStd == '3.6' || $currentStd == '450') ? 'selected' : '' ?>>450 μm</option>
          <option value="850" <?= ($currentStd == '8.7' || $currentStd == '850') ? 'selected' : '' ?>>850 μm</option>
      </select>
    </div>
  </div>

  <!-- Input hidden untuk dikirim ke database -->
  <input type="hidden" id="std_die_bond" name="std_die_bond" value="<?= $currentStd ?>">
  <table class="table table-striped-columns">
    <thead align="center">
      <tr>
        <th colspan="9" scope="col"><?php echo nl2br("PRODUCTION PROCESS CONTROL DIE BONDING 2")?></th>
      </tr>
      <tr>
        <th class="input-form" colspan="3" scope="col">Quantity</th>
        <th class="input-form" colspan="6" scope="col">Foregoing Result (40 pcs/lot)</th>
      </tr>
      <tr>
        <th class="input-form" rowspan="3">In</th>
        <th class="input-form" rowspan="3">Out</th>
        <th class="input-form" rowspan="3">NG</th>
        <th class="input-form" colspan="2" scope="col">SM chip</th>
        <th class="input-form" colspan="4" scope="col">LD chip</th>
      </tr>
      <tr>
        <th class="input-form" rowspan="2">Crack</th>
        <th class="input-form" rowspan="2">Chipping</th>
        <th class="input-form" rowspan="2">LD pos</th>
        <th class="input-form" rowspan="2">Miss LD chip</th>
        <th class="input-form" rowspan="2">Crack</th>
        <th class="input-form" rowspan="2">Chipping</th>
      </tr>
    </thead>

    <tbody>
      <tr>
        <td rowspan="2">
          <input type="text" class="form-control" id="par013" name="par013" onchange="quantityChange1()" onkeyup="quantityChange1()" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par013'];} ?>>
        </td>
        <td rowspan="2">
          <input type="text" class="form-control" id="par014" name="par014" onchange="quantityChange1()" onkeyup="quantityChange1()" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par014'];} ?>>
        </td>
        <td rowspan="2">
          <input type="text" class="form-control" id="par015" name="par015" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par015'];} ?> readonly>
        </td>
        <td rowspan="2">
          <select class="form-select" id="par035" name="par035" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par035'];} ?>>
          <?php if(isset($alldata)){
              if($alldata[0]["par035"]=="-"){
                echo '
                  <option value="-" selected>-</option>
                  <option value="OK">OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else if($alldata[0]["par035"]=="OK"){
                echo '
                  <option value="-">-</option>
                  <option value="OK" selected>OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else{
                echo '
                  <option value="-">-</option>
                  <option value="OK">OK</option>
                  <option value="NG" selected>NG</option>
                ';
              }
            }
            else{
              echo '
                <option value="-">-</option>
                <option value="OK">OK</option>
                <option value="NG">NG</option>
              ';
            } ?>
          </select>
        </td>
        <td rowspan="2">
          <select class="form-select" id="par036" name="par036" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par036'];} ?>>
          <?php if(isset($alldata)){
              if($alldata[0]["par036"]=="-"){
                echo '
                  <option value="-" selected>-</option>
                  <option value="OK">OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else if($alldata[0]["par036"]=="OK"){
                echo '
                  <option value="-">-</option>
                  <option value="OK" selected>OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else{
                echo '
                  <option value="-">-</option>
                  <option value="OK">OK</option>
                  <option value="NG" selected>NG</option>
                ';
              }
            }
            else{
              echo '
                <option value="-">-</option>
                <option value="OK">OK</option>
                <option value="NG">NG</option>
              ';
            } ?>
          </select>
        </td>
        <td rowspan="2">
          <select class="form-select" id="par037" name="par037" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par037'];} ?>>
          <?php if(isset($alldata)){
              if($alldata[0]["par037"]=="-"){
                echo '
                  <option value="-" selected>-</option>
                  <option value="OK">OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else if($alldata[0]["par037"]=="OK"){
                echo '
                  <option value="-">-</option>
                  <option value="OK" selected>OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else{
                echo '
                  <option value="-">-</option>
                  <option value="OK">OK</option>
                  <option value="NG" selected>NG</option>
                ';
              }
            }
            else{
              echo '
                <option value="-">-</option>
                <option value="OK">OK</option>
                <option value="NG">NG</option>
              ';
            } ?>
          </select>
        </td>
        <td rowspan="2">
          <select class="form-select" id="par038" name="par038" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par038'];} ?>>
          <?php if(isset($alldata)){
              if($alldata[0]["par038"]=="-"){
                echo '
                  <option value="-" selected>-</option>
                  <option value="OK">OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else if($alldata[0]["par038"]=="OK"){
                echo '
                  <option value="-">-</option>
                  <option value="OK" selected>OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else{
                echo '
                  <option value="-">-</option>
                  <option value="OK">OK</option>
                  <option value="NG" selected>NG</option>
                ';
              }
            }
            else{
              echo '
                <option value="-">-</option>
                <option value="OK">OK</option>
                <option value="NG">NG</option>
              ';
            } ?>
          </select>
        </td>
        <td rowspan="2">
          <select class="form-select" id="par039" name="par039" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par039'];} ?>>
          <?php if(isset($alldata)){
              if($alldata[0]["par039"]=="-"){
                echo '
                  <option value="-" selected>-</option>
                  <option value="OK">OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else if($alldata[0]["par039"]=="OK"){
                echo '
                  <option value="-">-</option>
                  <option value="OK" selected>OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else{
                echo '
                  <option value="-">-</option>
                  <option value="OK">OK</option>
                  <option value="NG" selected>NG</option>
                ';
              }
            }
            else{
              echo '
                <option value="-">-</option>
                <option value="OK">OK</option>
                <option value="NG">NG</option>
              ';
            } ?>
          </select>
        </td>
        <td rowspan="2">
          <select class="form-select" id="par040" name="par040" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par040'];} ?>>
          <?php if(isset($alldata)){
              if($alldata[0]["par040"]=="-"){
                echo '
                  <option value="-" selected>-</option>
                  <option value="OK">OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else if($alldata[0]["par040"]=="OK"){
                echo '
                  <option value="-">-</option>
                  <option value="OK" selected>OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else{
                echo '
                  <option value="-">-</option>
                  <option value="OK">OK</option>
                  <option value="NG" selected>NG</option>
                ';
              }
            }
            else{
              echo '
                <option value="-">-</option>
                <option value="OK">OK</option>
                <option value="NG">NG</option>
              ';
            } ?>
          </select>
        </td>
      </tr>
    </tbody>
  </table>
  <table class="table table-striped-columns">
    <thead align="center">
      <tr>
        <th class="input-form" colspan="2">@ Die Bond Strength</th>
        <th class="input-form" colspan="2" rowspan="2" >* Check Mark 2Pcs/shift</th>
        <th class="input-form" colspan="2"># Check of Ag Paste</th>
        <th class="input-form" colspan="2"># Check ketebalan Ag Paste (nilai h)</th>
        <th class="input-form" rowspan="4">**Disp. Air Pressure</th>
        <th class="input-form" rowspan="4">**Disp. time</th>
        <th class="input-form" rowspan="4">**Vacuum pressure</th>
      </tr>
      <tr>
        <th class="input-form" colspan="2" scope="col">(2pcs/shift)</th>
        <th class="input-form" colspan="2" scope="col">(2pcs/Lot)</th>
        <th class="input-form" colspan="2" scope="col">(2pcs/Lot)</th>
      </tr>
      <tr>
        <th class="input-form" colspan="2" scope="col">
            Min: <span id="label_die_bond_std">-</span> N
        </th>
        <th class="input-form" colspan="2" scope="col">OK:✓ <br> NG:X</th>
        <th class="input-form" colspan="2" scope="col">OK:✓ <br> NG:X</th>
        <th class="input-form" colspan="2" scope="col">OK:✓ <br> NG:X</th>
      </tr>
      <tr>
        <th class="input-form" >1st</th>
        <th class="input-form" >2nd</th>
        <th class="input-form" >1st</th>
        <th class="input-form" >2nd</th>
        <th class="input-form" >1st</th>
        <th class="input-form" >2nd</th>
        <th class="input-form" >1st</th>
        <th class="input-form" >2nd</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td rowspan="2">
          <input type="number" step="0.01" class="form-control" id="par017" name="par017" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par017'];} ?>>
        </td>
        <td rowspan="2">
          <input type="number" step="0.01" class="form-control" id="par018" name="par018" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par018'];} ?>>
        </td>
        <td rowspan="2">
          <select class="form-select" id="par041" name="par041" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par041'];} ?>>
          <?php if(isset($alldata)){
              if($alldata[0]["par041"]=="-"){
                echo '
                  <option value="-" selected>-</option>
                  <option value="OK">OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else if($alldata[0]["par041"]=="OK"){
                echo '
                  <option value="-">-</option>
                  <option value="OK" selected>OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else{
                echo '
                  <option value="-">-</option>
                  <option value="OK">OK</option>
                  <option value="NG" selected>NG</option>
                ';
              }
            }
            else{
              echo '
                <option value="-">-</option>
                <option value="OK">OK</option>
                <option value="NG">NG</option>
              ';
            } ?>
          </select>
        </td>
        <td rowspan="2">
          <select class="form-select" id="par042" name="par042" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par042'];} ?>>
          <?php if(isset($alldata)){
              if($alldata[0]["par042"]=="-"){
                echo '
                  <option value="-" selected>-</option>
                  <option value="OK">OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else if($alldata[0]["par042"]=="OK"){
                echo '
                  <option value="-">-</option>
                  <option value="OK" selected>OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else{
                echo '
                  <option value="-">-</option>
                  <option value="OK">OK</option>
                  <option value="NG" selected>NG</option>
                ';
              }
            }
            else{
              echo '
                <option value="-">-</option>
                <option value="OK">OK</option>
                <option value="NG">NG</option>
              ';
            } ?>
          </select>
        </td>
        <td rowspan="2">
          <select class="form-select" id="par043" name="par043" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par043'];} ?>>
          <?php if(isset($alldata)){
              if($alldata[0]["par043"]=="-"){
                echo '
                  <option value="-" selected>-</option>
                  <option value="OK">OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else if($alldata[0]["par043"]=="OK"){
                echo '
                  <option value="-">-</option>
                  <option value="OK" selected>OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else{
                echo '
                  <option value="-">-</option>
                  <option value="OK">OK</option>
                  <option value="NG" selected>NG</option>
                ';
              }
            }
            else{
              echo '
                <option value="-">-</option>
                <option value="OK">OK</option>
                <option value="NG">NG</option>
              ';
            } ?>
          </select>
        </td>
        <td rowspan="2">
          <select class="form-select" id="par002" name="par002" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par002'];} ?>>
          <?php if(isset($alldata)){
              if($alldata[0]["par002"]=="-"){
                echo '
                  <option value="-" selected>-</option>
                  <option value="OK">OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else if($alldata[0]["par002"]=="OK"){
                echo '
                  <option value="-">-</option>
                  <option value="OK" selected>OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else{
                echo '
                  <option value="-">-</option>
                  <option value="OK">OK</option>
                  <option value="NG" selected>NG</option>
                ';
              }
            }
            else{
              echo '
                <option value="-">-</option>
                <option value="OK">OK</option>
                <option value="NG">NG</option>
              ';
            } ?>
          </select>
        </td>
        <td rowspan="2">
          <select class="form-select" id="par003" name="par003" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par003'];} ?>>
          <?php if(isset($alldata)){
              if($alldata[0]["par003"]=="-"){
                echo '
                  <option value="-" selected>-</option>
                  <option value="OK">OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else if($alldata[0]["par003"]=="OK"){
                echo '
                  <option value="-">-</option>
                  <option value="OK" selected>OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else{
                echo '
                  <option value="-">-</option>
                  <option value="OK">OK</option>
                  <option value="NG" selected>NG</option>
                ';
              }
            }
            else{
              echo '
                <option value="-">-</option>
                <option value="OK">OK</option>
                <option value="NG">NG</option>
              ';
            } ?>
          </select>
        </td>
        <td rowspan="2">
          <select class="form-select" id="par004" name="par004" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par004'];} ?>>
          <?php if(isset($alldata)){
              if($alldata[0]["par004"]=="-"){
                echo '
                  <option value="-" selected>-</option>
                  <option value="OK">OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else if($alldata[0]["par004"]=="OK"){
                echo '
                  <option value="-">-</option>
                  <option value="OK" selected>OK</option>
                  <option value="NG">NG</option>
                ';
              }
              else{
                echo '
                  <option value="-">-</option>
                  <option value="OK">OK</option>
                  <option value="NG" selected>NG</option>
                ';
              }
            }
            else{
              echo '
                <option value="-">-</option>
                <option value="OK">OK</option>
                <option value="NG">NG</option>
              ';
            } ?>
          </select>
        </td>
        <td rowspan="2">
          <input type="text" class="form-control" id="par005" name="par005" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par005'];} ?>>
        </td>
        <td rowspan="2">
          <input type="text" class="form-control" id="par006" name="par006" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par006'];} ?>>
        </td>
        <td rowspan="2">
          <input type="text" class="form-control" id="par007" name="par007" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par007'];} ?>>
        </td>
      </tr>
    </tbody>
  </table>
</div>

<script>
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

if (document.getElementById("sm_chip_type") && document.getElementById("sm_chip_type").value !== "") {
    updateDieBondStd();
}

if (document.getElementById("par017")) {
    document.getElementById("par017").addEventListener("change", function() {
        let minReq = parseFloat(this.getAttribute("min"));
        if (this.value !== "" && !isNaN(minReq) && minReq > 0 && parseFloat(this.value) < minReq) {
            alert("NG! Nilai di bawah standar minimum (" + minReq + " N) untuk SM-chip tersebut.");
            this.value = "";
        }
    });
}

if (document.getElementById("par018")) {
    document.getElementById("par018").addEventListener("change", function() {
        let minReq = parseFloat(this.getAttribute("min"));
        if (this.value !== "" && !isNaN(minReq) && minReq > 0 && parseFloat(this.value) < minReq) {
            alert("NG! Nilai di bawah standar minimum (" + minReq + " N) untuk SM-chip tersebut.");
            this.value = "";
        }
    });
}
</script>