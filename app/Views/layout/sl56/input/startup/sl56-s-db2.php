<div class="table-responsive" id="slvi">
<?= csrf_field(); ?>

<?php 
if (!function_exists('getValUnit')) {
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
            oninput="togglePairLock('par001','par002')" 
            onchange="togglePairLock('par001','par002'); changeRange('par001',340,490)" 
            placeholder="" id="par001" name="par001" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par001'].'"';} ?>>
          </td>
        </tr>
        
        <tr>
          <td rowspan="1">B: 0.34-0.49 MPa</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            oninput="togglePairLock('par001','par002')" 
            onchange="togglePairLock('par001','par002'); changeRange('par002',0.34,0.49)" 
            placeholder="" id="par002" name="par002" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par002'].'"';} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="4">2</td>
          <td rowspan="4">@ Chip-clean Air Pressure</td>
          <td rowspan="2">Chip</td>
          <td >A: 60 - 150 MPa</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            oninput="togglePairLock('par003','par004')" 
            onchange="togglePairLock('par003','par004'); changeRange('par003',60,150)" 
            placeholder="" id="par003" name="par003" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par003'].'"';} ?>>
          </td>
        </tr>

        <tr>
          <td >B: 0.06 - 0.150 MPa</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            oninput="togglePairLock('par003','par004')" 
            onchange="togglePairLock('par003','par004'); changeRange('par004',0.06,0.15)" 
            placeholder="" id="par004" name="par004" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par004'].'"';} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="2">Stem</td>
          <td >A: 340 - 490 MPa</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            oninput="togglePairLock('par005','par006')" 
            onchange="togglePairLock('par005','par006'); changeRange('par005',340,490)" 
            placeholder="" id="par005" name="par005" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par005'].'"';} ?>>
          </td>
        </tr>

        <tr>
          <td >B: 0.340 - 0.490 MPa</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            oninput="togglePairLock('par005','par006')" 
            onchange="togglePairLock('par005','par006'); changeRange('par006',0.340,0.490)" 
            placeholder="" id="par006" name="par006" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par006'].'"';} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="4">3</td>
          <td rowspan="4">@ Vacuum Pressure</td>
          <td rowspan="2">Chip</td>
          <td >A: -80 kPa atau kurang</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            oninput="togglePairLock('par007','par008')" 
            onchange="togglePairLock('par007','par008'); changeRange('par007',-99999999999,-80)" 
            placeholder="" id="par007" name="par007" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par007'].'"';} ?>>
          </td>
        </tr>

        <tr>
          <td >B: -80 kPa atau kurang</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            oninput="togglePairLock('par007','par008')" 
            onchange="togglePairLock('par007','par008'); changeRange('par008',-99999999999,-80)" 
            placeholder="" id="par008" name="par008" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par008'].'"';} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="2">Stem</td>
          <td >A: -70 kPa atau kurang</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            oninput="togglePairLock('par009','par010')" 
            onchange="togglePairLock('par009','par010'); changeRange('par009',-99999999999,-70)" 
            placeholder="" id="par009" name="par009" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par009'].'"';} ?>>
          </td>
        </tr>

        <tr>
          <td >B: -70 kPa atau kurang</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" 
            oninput="togglePairLock('par009','par010')" 
            onchange="togglePairLock('par009','par010'); changeRange('par010',-99999999999,-70)" 
            placeholder="" id="par010" name="par010" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par010'].'"';} ?>>
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

        <tr>
          <td rowspan="2">6</td>
          <td rowspan="2" colspan="2">@Colled Load</td>
          <td >Collet A : 0.60 - 1.00 N (60-100 gr)</td>
          <?php 
            $p015 = getValUnit(isset($alldata) ? $alldata : null, 'par015'); 
            $u015 = ($p015['unit'] === 'gr') ? 'gr' : 'N';
            $min015 = ($u015 === 'gr') ? '60' : '0.60';
            $max015 = ($u015 === 'gr') ? '100' : '1.00';
          ?>
          <td>
            <div class="input-group">
                <input type="hidden" id="par015" name="par015" value="<?= isset($alldata) ? htmlspecialchars($alldata[0]['par015'] ?? '') : '' ?>">
                <input class="form-control" type="number" step="0.000000001" data-min="<?= $min015 ?>" data-max="<?= $max015 ?>" id="par015_val" name="par015_val" value="<?= htmlspecialchars($p015['val']) ?>" 
                       oninput="combineData('par015')" 
                       onchange="combineData('par015'); validateDynamicUnit('par015')" placeholder="">
                <select class="form-control" id="par015_unit" name="par015_unit" onchange="combineData('par015'); validateDynamicUnit('par015')" style="max-width: 80px; padding: 0.375rem;">
                    <option value="N" data-min="0.60" data-max="1.00" <?= $u015 == 'N' ? 'selected' : '' ?>>N</option>
                    <option value="gr" data-min="60" data-max="100" <?= $u015 == 'gr' ? 'selected' : '' ?>>gr</option>
                </select>
            </div>
          </td>
        </tr>

        <tr>
          <td >Collet B : 0.60 - 1.40 N (60-140 gr)</td>
          <?php 
            $p016 = getValUnit(isset($alldata) ? $alldata : null, 'par016'); 
            $u016 = ($p016['unit'] === 'gr') ? 'gr' : 'N';
            $min016 = ($u016 === 'gr') ? '60' : '0.60';
            $max016 = ($u016 === 'gr') ? '140' : '1.40';
          ?>
          <td>
            <div class="input-group">
                <input type="hidden" id="par016" name="par016" value="<?= isset($alldata) ? htmlspecialchars($alldata[0]['par016'] ?? '') : '' ?>">
                <input class="form-control" type="number" step="0.000000001" data-min="<?= $min016 ?>" data-max="<?= $max016 ?>" id="par016_val" name="par016_val" value="<?= htmlspecialchars($p016['val']) ?>" 
                       oninput="combineData('par016')" 
                       onchange="combineData('par016'); validateDynamicUnit('par016')" placeholder="">
                <select class="form-control" id="par016_unit" name="par016_unit" onchange="combineData('par016'); validateDynamicUnit('par016')" style="max-width: 80px; padding: 0.375rem;">
                    <option value="N" data-min="0.60" data-max="1.40" <?= $u016 == 'N' ? 'selected' : '' ?>>N</option>
                    <option value="gr" data-min="60" data-max="140" <?= $u016 == 'gr' ? 'selected' : '' ?>>gr</option>
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
          <?php 
            $p018 = getValUnit(isset($alldata) ? $alldata : null, 'par018'); 
            $u018 = in_array($p018['unit'], ['kPa', 'kgf/cm^2']) ? $p018['unit'] : 'MPa';
            $min018 = ($u018 === 'kPa') ? '10' : (($u018 === 'kgf/cm^2') ? '0.10' : '0.01');
            $max018 = ($u018 === 'kPa') ? '100' : (($u018 === 'kgf/cm^2') ? '1' : '0.10');
          ?>
          <td>
            <div class="input-group">
                <input type="hidden" id="par018" name="par018" value="<?= isset($alldata) ? htmlspecialchars($alldata[0]['par018'] ?? '') : '' ?>">
                <input class="form-control" type="number" step="0.000000001" data-min="<?= $min018 ?>" data-max="<?= $max018 ?>" id="par018_val" name="par018_val" value="<?= htmlspecialchars($p018['val']) ?>" 
                       oninput="combineData('par018'); togglePairLock('par018','par019')" 
                       onchange="combineData('par018'); togglePairLock('par018','par019'); validateDynamicUnit('par018')" placeholder="">
                <select class="form-control" id="par018_unit" name="par018_unit" onchange="combineData('par018'); togglePairLock('par018','par019'); validateDynamicUnit('par018')" style="max-width: 95px; padding: 0.375rem;">
                    <option value="MPa" data-min="0.01" data-max="0.10" <?= $u018 == 'MPa' ? 'selected' : '' ?>>MPa</option>
                    <option value="kPa" data-min="10" data-max="100" <?= $u018 == 'kPa' ? 'selected' : '' ?>>kPa</option>
                    <option value="kgf/cm^2" data-min="0.10" data-max="1" <?= $u018 == 'kgf/cm^2' ? 'selected' : '' ?>>kgf/cm²</option>
                </select>
            </div>
          </td>
        </tr>

        <tr>
          <td >Digital : 0.005-0.10 MPa (5-100 kPa) / 0.05 - 1 kgf/cm^2</td>
          <?php 
            $p019 = getValUnit(isset($alldata) ? $alldata : null, 'par019'); 
            $u019 = in_array($p019['unit'], ['kPa', 'kgf/cm^2']) ? $p019['unit'] : 'MPa';
            $min019 = ($u019 === 'kPa') ? '5' : (($u019 === 'kgf/cm^2') ? '0.05' : '0.005');
            $max019 = ($u019 === 'kPa') ? '100' : (($u019 === 'kgf/cm^2') ? '1' : '0.10');
          ?>
          <td>
            <div class="input-group">
                <input type="hidden" id="par019" name="par019" value="<?= isset($alldata) ? htmlspecialchars($alldata[0]['par019'] ?? '') : '' ?>">
                <input class="form-control" type="number" step="0.000000001" data-min="<?= $min019 ?>" data-max="<?= $max019 ?>" id="par019_val" name="par019_val" value="<?= htmlspecialchars($p019['val']) ?>" 
                       oninput="combineData('par019'); togglePairLock('par018','par019')" 
                       onchange="combineData('par019'); togglePairLock('par018','par019'); validateDynamicUnit('par019')" placeholder="">
                <select class="form-control" id="par019_unit" name="par019_unit" onchange="combineData('par019'); togglePairLock('par018','par019'); validateDynamicUnit('par019')" style="max-width: 95px; padding: 0.375rem;">
                    <option value="MPa" data-min="0.005" data-max="0.10" <?= $u019 == 'MPa' ? 'selected' : '' ?>>MPa</option>
                    <option value="kPa" data-min="5" data-max="100" <?= $u019 == 'kPa' ? 'selected' : '' ?>>kPa</option>
                    <option value="kgf/cm^2" data-min="0.05" data-max="1" <?= $u019 == 'kgf/cm^2' ? 'selected' : '' ?>>kgf/cm²</option>
                </select>
            </div>
          </td>
        </tr>

        <tr>
          <td rowspan="2">Dispense Time</td>
          <td >Analog : 0.01-0.15 sec / 10-150 ms</td>
          <?php 
            $p020 = getValUnit(isset($alldata) ? $alldata : null, 'par020'); 
            $u020 = ($p020['unit'] === 'ms') ? 'ms' : 'sec';
            $min020 = ($u020 === 'ms') ? '10' : '0.01';
            $max020 = ($u020 === 'ms') ? '150' : '0.15';
          ?>
          <td>
            <div class="input-group">
                <input type="hidden" id="par020" name="par020" value="<?= isset($alldata) ? htmlspecialchars($alldata[0]['par020'] ?? '') : '' ?>">
                <input class="form-control" type="number" step="0.000000001" data-min="<?= $min020 ?>" data-max="<?= $max020 ?>" id="par020_val" name="par020_val" value="<?= htmlspecialchars($p020['val']) ?>" 
                       oninput="combineData('par020'); togglePairLock('par020','par021')" 
                       onchange="combineData('par020'); togglePairLock('par020','par021'); validateDynamicUnit('par020')" placeholder="">
                <select class="form-control" id="par020_unit" name="par020_unit" onchange="combineData('par020'); togglePairLock('par020','par021'); validateDynamicUnit('par020')" style="max-width: 80px; padding: 0.375rem;">
                    <option value="sec" data-min="0.01" data-max="0.15" <?= $u020 == 'sec' ? 'selected' : '' ?>>sec</option>
                    <option value="ms" data-min="10" data-max="150" <?= $u020 == 'ms' ? 'selected' : '' ?>>ms</option>
                </select>
            </div>
          </td>
        </tr>
        
        <tr>
          <td >Digital : 0.005-0.15 sec / 5-150 ms</td>
          <?php 
            $p021 = getValUnit(isset($alldata) ? $alldata : null, 'par021'); 
            $u021 = ($p021['unit'] === 'ms') ? 'ms' : 'sec';
            $min021 = ($u021 === 'ms') ? '5' : '0.005';
            $max021 = ($u021 === 'ms') ? '150' : '0.15';
          ?>
          <td>
            <div class="input-group">
                <input type="hidden" id="par021" name="par021" value="<?= isset($alldata) ? htmlspecialchars($alldata[0]['par021'] ?? '') : '' ?>">
                <input class="form-control" type="number" step="0.000000001" data-min="<?= $min021 ?>" data-max="<?= $max021 ?>" id="par021_val" name="par021_val" value="<?= htmlspecialchars($p021['val']) ?>" 
                       oninput="combineData('par021'); togglePairLock('par020','par021')" 
                       onchange="combineData('par021'); togglePairLock('par020','par021'); validateDynamicUnit('par021')" placeholder="">
                <select class="form-control" id="par021_unit" name="par021_unit" onchange="combineData('par021'); togglePairLock('par020','par021'); validateDynamicUnit('par021')" style="max-width: 80px; padding: 0.375rem;">
                    <option value="sec" data-min="0.005" data-max="0.15" <?= $u021 == 'sec' ? 'selected' : '' ?>>sec</option>
                    <option value="ms" data-min="5" data-max="150" <?= $u021 == 'ms' ? 'selected' : '' ?>>ms</option>
                </select>
            </div>
          </td>
        </tr>

        <tr>
          <td rowspan="2">Suction Pressure Force</td>
          <td >Analog : kurang dari 1/6 dari posisi Off</td>
          <td>
            <select class="form-control" id="par022" name="par022" onchange="togglePairLock('par022','par023')">
              <option value="">-- Pilih --</option>
              <option value="✔" <?php if(isset($alldata) && $alldata[0]['par022'] == '✔'){echo 'selected';} ?>>✔ (OK)</option>
              <option value="✖" <?php if(isset($alldata) && $alldata[0]['par022'] == '✖'){echo 'selected';} ?>>✖ (NG)</option>
            </select>
          </td>
        </tr>
        
        <tr>
          <td >Digital : -0.3 ~ -0.1 kPa / -0.03 ~ -0.01 kgf/cm^2 / -3 ~ -1 mmHg</td>
          <?php 
            $p023 = getValUnit(isset($alldata) ? $alldata : null, 'par023'); 
            $u023 = in_array($p023['unit'], ['kgf/cm^2', 'mmHg']) ? $p023['unit'] : 'kPa';
            $min023 = ($u023 === 'kgf/cm^2') ? '-0.03' : (($u023 === 'mmHg') ? '-3' : '-0.3');
            $max023 = ($u023 === 'kgf/cm^2') ? '-0.01' : (($u023 === 'mmHg') ? '-1' : '-0.1');
          ?>
          <td>
            <div class="input-group">
                <input type="hidden" id="par023" name="par023" value="<?= isset($alldata) ? htmlspecialchars($alldata[0]['par023'] ?? '') : '' ?>">
                <input class="form-control" type="number" step="0.000000001" data-min="<?= $min023 ?>" data-max="<?= $max023 ?>" id="par023_val" name="par023_val" value="<?= htmlspecialchars($p023['val']) ?>" 
                       oninput="combineData('par023'); togglePairLock('par022','par023')" 
                       onchange="combineData('par023'); togglePairLock('par022','par023'); validateDynamicUnit('par023')" placeholder="">
                <select class="form-control" id="par023_unit" name="par023_unit" onchange="combineData('par023'); togglePairLock('par022','par023'); validateDynamicUnit('par023')" style="max-width: 95px; padding: 0.375rem;">
                    <option value="kPa" data-min="-0.3" data-max="-0.1" <?= $u023 == 'kPa' ? 'selected' : '' ?>>kPa</option>
                    <option value="kgf/cm^2" data-min="-0.03" data-max="-0.01" <?= $u023 == 'kgf/cm^2' ? 'selected' : '' ?>>kgf/cm²</option>
                    <option value="mmHg" data-min="-3" data-max="-1" <?= $u023 == 'mmHg' ? 'selected' : '' ?>>mmHg</option>
                </select>
            </div>
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
            <input class="form-control" type="number" step="0.0000001" onchange="changeRange('par029',0.2,0.6)" placeholder="" id="par029" name="par029" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par029'].'"';} ?>>
          </td>
        </tr> 

        <tr>
          <td colspan="4">Judgment Start Up *</td>
          <td>
            <input class="form-control text-center font-weight-bold" type="text" id="par031" name="par031" readonly placeholder="✔ / ✖" style="font-size: 1.25rem; font-weight: bold;" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par031'] ?? '').'"';} ?>>
          </td>
        </tr>

        <tr>
          <td colspan="4">Note #</td>
          <td>
            <input class="form-control" type="text" placeholder="Tambahkan catatan jika ada" id="par030" name="par030" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par030'].'"';} ?>>
          </td>
        </tr>

      </tbody>   
    </table>
    <div class="note mt-3">
        <h6><b>Catatan:</b></h6>
        <p class="mb-1 small">@ : Tulis hasil pemeriksaan sesuai dengan angka yang ditunjukan pada masing-masing alat ukur.</p>
        <p class="mb-1 small">* : Beri tanda V jika OK/Good dan X jika NG.</p>
        <p class="mb-1 small"># : Isi bagian note apabila ada penggantian part atau kondisi abnormal.</p>
    </div>
</div>

<script>
  (function() {
    if (typeof togglePairLock === 'function') {
      togglePairLock('par001', 'par002');
      togglePairLock('par003', 'par004');
      togglePairLock('par005', 'par006');
      togglePairLock('par007', 'par008');
      togglePairLock('par009', 'par010');
      togglePairLock('par018', 'par019');
      togglePairLock('par020', 'par021');
      togglePairLock('par022', 'par023');
    }

    function evaluateStartupJudgmentSl56() {
      var judgInput = document.getElementById('par031');
      if (!judgInput) return;

      var isStandby = (document.getElementById('is-standby') && document.getElementById('is-standby').checked);
      if (isStandby) {
        judgInput.value = '-';
        judgInput.style.backgroundColor = '#e9ecef';
        judgInput.style.color = '#495057';
        judgInput.style.borderColor = '#ced4da';
        return;
      }

      var container = document.getElementById('slvi') || document;
      var hasNg = false;
      var allFilled = true;

      // 1. Check all selects
      var selects = container.querySelectorAll('select:not([id="par031"]):not([id$="_unit"])');
      selects.forEach(function(sel) {
        if (sel.disabled) return;
        var val = (sel.value || '').trim();
        if (val === '') {
          allFilled = false;
        } else if (val === '✖' || val.indexOf('NG') !== -1 || val.indexOf('✖') !== -1 || val === 'X') {
          hasNg = true;
        }
      });

      // 2. Pairs of inputs
      var pairs = [
        ['par001', 'par002'],
        ['par003', 'par004'],
        ['par005', 'par006'],
        ['par007', 'par008'],
        ['par009', 'par010'],
        ['par018_val', 'par019_val'],
        ['par020_val', 'par021_val']
      ];

      pairs.forEach(function(pair) {
        var elA = document.getElementById(pair[0]);
        var elB = document.getElementById(pair[1]);
        var filledA = elA && !elA.disabled && elA.value.trim() !== '' && elA.value.trim() !== '-';
        var filledB = elB && !elB.disabled && elB.value.trim() !== '' && elB.value.trim() !== '-';
        if (!filledA && !filledB) {
          allFilled = false;
        }
        if (elA && !elA.disabled && filledA) {
          if (elA.dataset.hasError === "1" || elA.style.backgroundColor === "red") hasNg = true;
        }
        if (elB && !elB.disabled && filledB) {
          if (elB.dataset.hasError === "1" || elB.style.backgroundColor === "red") hasNg = true;
        }
      });

      // Pair par022 (select) or par023_val (input)
      var sel022 = document.getElementById('par022');
      var inp023 = document.getElementById('par023_val');
      var filled022 = sel022 && !sel022.disabled && sel022.value.trim() !== '';
      var filled023 = inp023 && !inp023.disabled && inp023.value.trim() !== '' && inp023.value.trim() !== '-';
      if (!filled022 && !filled023) {
        allFilled = false;
      }
      if (inp023 && !inp023.disabled && filled023) {
        if (inp023.dataset.hasError === "1" || inp023.style.backgroundColor === "red") hasNg = true;
      }

      // Single numeric inputs
      var singleInputs = ['par015_val', 'par016_val', 'par029'];
      singleInputs.forEach(function(id) {
        var el = document.getElementById(id);
        if (!el || el.disabled) return;
        var val = el.value.trim();
        if (val === '' || val === '-') {
          allFilled = false;
        } else if (el.dataset.hasError === "1" || el.style.backgroundColor === "red") {
          hasNg = true;
        }
      });

      // Error markers
      var errInputs = container.querySelectorAll('input[data-has-error="1"], select[data-has-error="1"]');
      if (errInputs.length > 0) {
        hasNg = true;
      }

      if (hasNg) {
        judgInput.value = '✖';
        judgInput.style.backgroundColor = '#f8d7da';
        judgInput.style.color = '#842029';
        judgInput.style.borderColor = '#f5c2c7';
      } else if (allFilled) {
        judgInput.value = '✔';
        judgInput.style.backgroundColor = '#d1e7dd';
        judgInput.style.color = '#0f5132';
        judgInput.style.borderColor = '#badbcc';
      } else {
        var curVal = (judgInput.value || '').trim();
        if (curVal === 'OK' || curVal === '✔') {
          judgInput.value = '✔';
          judgInput.style.backgroundColor = '#d1e7dd';
          judgInput.style.color = '#0f5132';
          judgInput.style.borderColor = '#badbcc';
        } else if (curVal === 'NG' || curVal === '✖') {
          judgInput.value = '✖';
          judgInput.style.backgroundColor = '#f8d7da';
          judgInput.style.color = '#842029';
          judgInput.style.borderColor = '#f5c2c7';
        } else {
          judgInput.value = '';
          judgInput.style.backgroundColor = '';
          judgInput.style.color = '';
          judgInput.style.borderColor = '';
        }
      }
    }

    window.evaluateStartupJudgmentSl56 = evaluateStartupJudgmentSl56;

    var container = document.getElementById('slvi');
    if (container) {
      container.addEventListener('input', function() {
        setTimeout(evaluateStartupJudgmentSl56, 40);
      });
      container.addEventListener('change', function() {
        setTimeout(evaluateStartupJudgmentSl56, 40);
      });
    }

    setTimeout(evaluateStartupJudgmentSl56, 100);

    var formEl = container ? container.closest('form') : null;
    if (formEl) {
      formEl.addEventListener('submit', function() {
        evaluateStartupJudgmentSl56();
      }, true);
    }
  })();
</script>