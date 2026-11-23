<div class="table-responsive" id="slpldsm">
<?= csrf_field(); ?>
<input type="hidden" id="number-edit" name="number-edit" value="<?php if(isset($alldata)){echo $number;} ?>">
    <table class="table table-striped-columns">
    <thead align="center">
        <tr>
            <th colspan="11" scope="col"><?php echo nl2br("LDSM MACHINE START UP CHECK SHEET\n(Single Laser 5.6)")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" style="width: 5%;">No</th>
            <th colspan="2" scope="col" style="width: 30%;">Start up Check Item</th>
            <th colspan="7" scope="col">Standard</th>
            <th scope="col" style="width: 15%;">Data</th>
        </tr>
    </thead>

    <tbody>
        <!-- 1. Air Pressure -->
        <tr>
          <td rowspan="3">1</td>
          <td rowspan="2">@Air Pressure</td>
          <td>Drive Air</td>
          <td colspan="7" rowspan="2">0.4 ~ 0.6 Mpa</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par001',0.4,0.6)" id="par001" name="par001" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par001'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td>Blow Air</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par002',0.4,0.6)" id="par002" name="par002" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par002'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td colspan="2">N2 Pressure</td>
          <td colspan="7">0.20 ~ 0.30 Mpa</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par003',0.2,0.3)" id="par003" name="par003" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par003'] ?? '').'"';} ?>>
          </td>
        </tr>

        <!-- 2. Vacuum Pressure -->
        <tr>
          <td rowspan="12">2</td>
          <td rowspan="3">@Vacuum Source Pressure</td>
          <td>Source Vacuum 1</td>
          <td colspan="7">-70kPa atau kurang</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par004',-9999999999,-70)" id="par004" name="par004" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par004'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td>Source Vacuum 2</td>
          <td colspan="7" rowspan="2">-80kPa atau kurang</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par005',-9999999999,-80)" id="par005" name="par005" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par005'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td>Source Vacuum 3</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par006',-9999999999,-80)" id="par006" name="par006" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par006'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td rowspan="9">@Vacuum Pressure</td>
          <td>Stem Loader PP1</td>
          <td colspan="7" rowspan="3">-70kPa atau kurang</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par007',-9999999999,-70)" id="par007" name="par007" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par007'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td>Stem Loader PP2</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par008',-9999999999,-70)" id="par008" name="par008" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par008'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td>Stem Loader PP3</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par009',-9999999999,-70)" id="par009" name="par009" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par009'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td>Submount Chip Ring</td>
          <td colspan="7" rowspan="2">-50kPa atau kurang</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par010',-9999999999,-50)" id="par010" name="par010" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par010'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td>Laser Chip Push-Up Ring</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par011',-9999999999,-50)" id="par011" name="par011" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par011'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td>Stem Unloader PP1</td>
          <td colspan="7" rowspan="3">-70kPa atau kurang</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par012',-9999999999,-70)" id="par012" name="par012" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par012'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td>Stem Unloader PP2</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par013',-9999999999,-70)" id="par013" name="par013" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par013'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td>Stem Unloader PP3</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par014',-9999999999,-70)" id="par014" name="par014" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par014'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td>SM Loader PP</td>
          <td colspan="7">-80kPa atau kurang</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par015',-9999999999,-80)" id="par015" name="par015" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par015'] ?? '').'"';} ?>>
          </td>
        </tr>

        <!-- 3. Collet Load -->
        <tr>
          <td rowspan="4">3</td>
          <td colspan="2">LD Pick Up Collet</td>
          <td colspan="7" rowspan="2">0.2 ~ 0.4 N</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par016',0.2,0.4)" id="par016" name="par016" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par016'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td colspan="2">LD DB Collet</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par017',0.2,0.4)" id="par017" name="par017" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par017'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td colspan="2">SM Pick Up Collet</td>
          <td colspan="7" rowspan="2">0.6 ~ 0.8 N</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par018',0.6,0.8)" id="par018" name="par018" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par018'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td colspan="2">SM DB Collet</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par019',0.6,0.8)" id="par019" name="par019" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par019'] ?? '').'"';} ?>>
          </td>
        </tr>

        <!-- 4. Needle Push Up Level -->
        <tr>
          <td rowspan="2">4</td>
          <td colspan="2">LD</td>
          <td colspan="7" rowspan="2">0.2 ~ 0.6 mm</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par020',0.2,0.6)" id="par020" name="par020" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par020'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td colspan="2">SM</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par021',0.2,0.6)" id="par021" name="par021" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par021'] ?? '').'"';} ?>>
          </td>
        </tr>

        <!-- 5. Cleaning -->
        <tr>
          <td>5</td>
          <td>* Cleaning</td>
          <td>LD collet, SM collet, Stem collet dan masing-masing stage</td>
          <td colspan="7">Bersihkan dengan cutton bud dan basahi dengan alkohol</td>
          <td>
            <input class="form-control" type="text" id="par022" name="par022" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par022'] ?? '').'"';} ?>>
          </td>
        </tr>

        <!-- 6. Check the collet guide -->
        <tr>
          <td>6</td>
          <td colspan="2">* Check the collet guide</td>
          <td colspan="7">Check apakah collet guide smooth dan stabil</td>
          <td>
            <input class="form-control" type="text" id="par023" name="par023" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par023'] ?? '').'"';} ?>>
          </td>
        </tr>

        <!-- 7. Check arah Ion Blower Fan -->
        <tr>
          <td rowspan="2">7</td>
          <td colspan="2" rowspan="2">* Check arah Ion Blower Fan</td>
          <td colspan="7">Posisi blower</td>
          <td>
            <input class="form-control" type="text" id="par024" name="par024" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par024'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td colspan="7">Putaran blower</td>
          <td>
            <input class="form-control" type="text" id="par025" name="par025" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par025'] ?? '').'"';} ?>>
          </td>
        </tr>

        <!-- 8. Check die bonding position -->
        <tr>
          <td>8</td>
          <td colspan="2">* Check die bonding position</td>
          <td colspan="7">Periksa posisi die bonding dengan melihat monitor</td>
          <td>
            <input class="form-control" type="text" id="par026" name="par026" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par026'] ?? '').'"';} ?>>
          </td>
        </tr>

        <!-- 9. Check the electromagnetic lock -->
        <tr>
          <td>9</td>
          <td colspan="2">* Check the electromagnetic lock</td>
          <td colspan="7">Periksa the lock "ON" pada saat pengoperasian auto</td>
          <td>
            <input class="form-control" type="text" id="par027" name="par027" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par027'] ?? '').'"';} ?>>
          </td>
        </tr>

        <!-- 10. Cleaning Machine -->
        <tr>
          <td>10</td>
          <td colspan="2">* Cleaning Machine</td>
          <td colspan="7">Bersihkan mesin dan lingkungan sekitarnya dari debu dan kotoran</td>
          <td>
            <input class="form-control" type="text" id="par028" name="par028" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par028'] ?? '').'"';} ?>>
          </td>
        </tr>

        <!-- 11. Check the die bonding setting -->
        <tr>
          <td>11</td>
          <td colspan="2">* Check the die bonding setting</td>
          <td colspan="7">Periksa apakah pengaturan heater dan parameter sudah benar</td>
          <td>
            <input class="form-control" type="text" id="par029" name="par029" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par029'] ?? '').'"';} ?>>
          </td>
        </tr>

        <!-- 12. N2 (Nitrogen) -->
        <tr>
          <td rowspan="2">12</td>
          <td rowspan="2">@N2 (Nitrogen)</td>
          <td>Bonding Stage F</td>
          <td colspan="7" rowspan="2">3L/min</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par030',2.9,3.1)" id="par030" name="par030" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par030'] ?? '').'"';} ?>>
          </td>
        </tr>
        <tr>
          <td>Bonding Stage B</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par031',2.9,3.1)" id="par031" name="par031" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par031'] ?? '').'"';} ?>>
          </td>
        </tr>

        <!-- 13. Check penutup mesin -->
        <tr>
          <td>13</td>
          <td colspan="2">* Check penutup mesin</td>
          <td colspan="7">Pastikan bahwa penutup mesin sudah benar</td>
          <td>
            <input class="form-control" type="text" id="par032" name="par032" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par032'] ?? '').'"';} ?>>
          </td>
        </tr>

        <!-- 14. Check collet condition -->
        <tr>
          <td>14</td>
          <td colspan="2">* Check collet condition</td>
          <td colspan="7">Periksa secara visual LD collet dan SM collet (Tidak ada foreign object, tidak chipping, collet tidak tersumbat)</td>
          <td>
            <input class="form-control" type="text" id="par033" name="par033" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par033'] ?? '').'"';} ?>>
          </td>
        </tr>

        <!-- Note # -->
        <tr>
          <td colspan="10">Note</td>
          <td>
            <input class="form-control" type="text" id="par034" name="par034" placeholder="" <?php if(isset($alldata)){echo 'value="'.htmlspecialchars($alldata[0]['par034'] ?? '').'"';} ?>>
          </td>
        </tr>
    </tbody>
    </table>
</div>
