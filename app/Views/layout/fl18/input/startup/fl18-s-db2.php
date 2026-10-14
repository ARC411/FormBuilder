<div class="table-responsive" id="fldb1a">
<?= csrf_field(); ?>
<input type="hidden" id="number-edit" name="number-edit" value=<?php if(isset($alldata)){echo $number;} ?>>
    <table class="table table-striped-columns">
    <thead align="center">
        <tr>
        <th colspan="19" scope="col"><?php echo nl2br("LD DIE BONDING 2 MACHINE")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th scope="col" >No</th>
            <th scope="col" colspan="2" >Start Up Check Item</th>
            <th scope="col" >Standard</th>
            <th scope="col" >Hasil Start Up Check</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td >1</td>
            <td colspan="2">@ Air Pressure 1</td>
            <td >0.05 ~ 0.15 MPa</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par001',0.05,0.15)" id="par001" name="par001" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par001'];} ?>>
            </td>
        </tr>

        <tr>
            <td >2</td>
            <td colspan="2">@ Air Pressure 2</td>
            <td >0.35 ~ 0.50 MPa</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par002',0.35,0.5)"  id="par002" name="par002" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par002'];} ?>>
            </td>
        </tr>

        <tr>
            <td >3</td>
            <td colspan="2">Middle Push Up</td>
            <td >-80 kPa atau kurang</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par003',-99999999,-80)"  id="par003" name="par003" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par003'];} ?>>
          </td>
        </tr>

        <tr>
            <td >4</td>
            <td colspan="2">@ Needle Push Up</td>
            <td >0.30 ~ 0.60 mm</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par004',0.3,0.6)"  id="par004" name="par004" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par004'];} ?>>
            </td>
        </tr>

        <tr>
            <td rowspan="2">5</td>
            <td rowspan="2" colspan="2">* Collet Cleaning <br> (Bersihkan dengan Cotton Bud yang dibasahi dengan Alkohol)</td>
            <td >D/B Collet</td>
            <td >
                <select class="form-control text-center" id="par005" name="par005" required>
                    <option value="" disabled <?php echo (!isset($alldata)) ? 'selected' : ''; ?>>-- Pilih --</option>
                    <option value="OK" <?php echo (isset($alldata) && $alldata[0]['par005'] == 'OK') ? 'selected' : ''; ?>>✔ OK</option>
                    <option value="NG" <?php echo (isset($alldata) && $alldata[0]['par005'] == 'NG') ? 'selected' : ''; ?>>✖ NG</option>
                </select>
            </td>
        </tr>

        <tr>
            <td >Middle Collet</td>
            <td >
                <select class="form-control text-center" id="par006" name="par006" required>
                    <option value="" disabled <?php echo (!isset($alldata)) ? 'selected' : ''; ?>>-- Pilih --</option>
                    <option value="OK" <?php echo (isset($alldata) && $alldata[0]['par006'] == 'OK') ? 'selected' : ''; ?>>✔ OK</option>
                    <option value="NG" <?php echo (isset($alldata) && $alldata[0]['par006'] == 'NG') ? 'selected' : ''; ?>>✖ NG</option>
                </select>
            </td>
        </tr>

        <tr>
            <td >6</td>
            <td colspan="3">* Checking contact probe (Check jika patah atau bengkok ganti yang baru)</td>
            <td >
                <select class="form-control text-center" id="par007" name="par007" required>
                    <option value="" disabled <?php echo (!isset($alldata)) ? 'selected' : ''; ?>>-- Pilih --</option>
                    <option value="OK" <?php echo (isset($alldata) && $alldata[0]['par007'] == 'OK') ? 'selected' : ''; ?>>✔ OK</option>
                    <option value="NG" <?php echo (isset($alldata) && $alldata[0]['par007'] == 'NG') ? 'selected' : ''; ?>>✖ NG</option>
                </select>
            </td>
        </tr>

        <tr>
            <td >7</td>
            <td colspan="2">@ Colled Load</td>
            <td >0.60 ~ 1.00N (60 ~ 100 gr)</td>
            <td >
                <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par008',0.6,1)"  id="par008" name="par008" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par008'];} ?>>
            </td>
        </tr>

        <tr>
            <td >8</td>
            <td colspan="3">* Pengecekan posisi die bonding di monitor</td>
            <td >
                <select class="form-control text-center" id="par009" name="par009" required>
                    <option value="" disabled <?php echo (!isset($alldata)) ? 'selected' : ''; ?>>-- Pilih --</option>
                    <option value="OK" <?php echo (isset($alldata) && $alldata[0]['par009'] == 'OK') ? 'selected' : ''; ?>>✔ OK</option>
                    <option value="NG" <?php echo (isset($alldata) && $alldata[0]['par009'] == 'NG') ? 'selected' : ''; ?>>✖ NG</option>
                </select>
            </td>
        </tr>

        <tr>
            <td rowspan="3">9</td>
            <td rowspan="3">@ Dispenser indication</td>
            <td >Air Pressure</td>
            <td rowspan="3">Nilai Air pressure, Dispenser time, vacuum <br> pressure disesuaikan agar mendapatkan <br> nilai ag paste yang standard</td>
            <td >
                <input class="form-control" type="text" id="par010" name="par010" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par010'];} ?>>
            </td>
        </tr>

        <tr>
            <td >Dispense Time</td>
            <td >
                <input class="form-control" type="text" id="par011" name="par011" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par011'];} ?>>
            </td>
        </tr>
       
        <tr>
            <td >Vacuum pressure</td>
            <td >
              <input class="form-control" type="text" id="par012" name="par012" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par012'];} ?>>
            </td>
        </tr>

        <tr>
            <td rowspan="2">10</td>
            <td rowspan="2" colspan="2">* Check kondisi Ag Paste di monitor</td>
            <td >Posisi</td>
            <td >
                <select class="form-control text-center" id="par013" name="par013" required>
                    <option value="" disabled <?php echo (!isset($alldata)) ? 'selected' : ''; ?>>-- Pilih --</option>
                    <option value="OK" <?php echo (isset($alldata) && $alldata[0]['par013'] == 'OK') ? 'selected' : ''; ?>>✔ OK</option>
                    <option value="NG" <?php echo (isset($alldata) && $alldata[0]['par013'] == 'NG') ? 'selected' : ''; ?>>✖ NG</option>
                </select>
            </td>
        </tr>

        <tr>
            <td >Quantity</td>
            <td >
                <select class="form-control text-center" id="par014" name="par014" required>
                    <option value="" disabled <?php echo (!isset($alldata)) ? 'selected' : ''; ?>>-- Pilih --</option>
                    <option value="OK" <?php echo (isset($alldata) && $alldata[0]['par014'] == 'OK') ? 'selected' : ''; ?>>✔ OK</option>
                    <option value="NG" <?php echo (isset($alldata) && $alldata[0]['par014'] == 'NG') ? 'selected' : ''; ?>>✖ NG</option>
                </select>
            </td>
        </tr>

        <tr>
            <td rowspan="2">11</td>
            <td rowspan="2" colspan="2">* Check arah Ion Blower Fan</td>
            <td >Posisi blower</td>
            <td >
                <select class="form-control text-center" id="par015" name="par015" required>
                    <option value="" disabled <?php echo (!isset($alldata)) ? 'selected' : ''; ?>>-- Pilih --</option>
                    <option value="OK" <?php echo (isset($alldata) && $alldata[0]['par015'] == 'OK') ? 'selected' : ''; ?>>✔ OK</option>
                    <option value="NG" <?php echo (isset($alldata) && $alldata[0]['par015'] == 'NG') ? 'selected' : ''; ?>>✖ NG</option>
                </select>
            </td>
        </tr>

        <tr>
            <td >Putaran blower</td>
            <td >
                <select class="form-control text-center" id="par016" name="par016" required>
                    <option value="" disabled <?php echo (!isset($alldata)) ? 'selected' : ''; ?>>-- Pilih --</option>
                    <option value="OK" <?php echo (isset($alldata) && $alldata[0]['par016'] == 'OK') ? 'selected' : ''; ?>>✔ OK</option>
                    <option value="NG" <?php echo (isset($alldata) && $alldata[0]['par016'] == 'NG') ? 'selected' : ''; ?>>✖ NG</option>
                </select>
            </td>
        </tr>

        <tr>
            <td>12</td>
            <td colspan="2">* Cleaning Machine</td>
            <td >Bersihkan mesin dan sekitarnya dari debu dan kotoran</td>
            <td >
                <select class="form-control text-center" id="par017" name="par017" required>
                    <option value="" disabled <?php echo (!isset($alldata)) ? 'selected' : ''; ?>>-- Pilih --</option>
                    <option value="OK" <?php echo (isset($alldata) && $alldata[0]['par017'] == 'OK') ? 'selected' : ''; ?>>✔ OK</option>
                    <option value="NG" <?php echo (isset($alldata) && $alldata[0]['par017'] == 'NG') ? 'selected' : ''; ?>>✖ NG</option>
                </select>
            </td>
        </tr>

        <tr>
            <td >13</td>
            <td colspan="2">* Pemeriksaan grounding <br> (Baut pengunci di conductive mat, banana plug, koneksi kabel grounding)</td>
            <td>Terpasang bagus/tidak longgar, lepas, putus</td>
            <td >
                <select class="form-control text-center" id="par018" name="par018" required>
                    <option value="" disabled <?php echo (!isset($alldata)) ? 'selected' : ''; ?>>-- Pilih --</option>
                    <option value="OK" <?php echo (isset($alldata) && $alldata[0]['par018'] == 'OK') ? 'selected' : ''; ?>>✔ OK</option>
                    <option value="NG" <?php echo (isset($alldata) && $alldata[0]['par018'] == 'NG') ? 'selected' : ''; ?>>✖ NG</option>
                </select>
            </td>
        </tr>

        <tr>
            <td>14</td>
            <td colspan="2">* Cek Kondisi Collet</td>
            <td >Jika terjadi kerusakan atau diluar standard, <br> lakukan pergantian dengan yang baru. <br> Jika OK lanjutkan proses </td>
            <td >
                <select class="form-control text-center" id="par019" name="par019" required>
                    <option value="" disabled <?php echo (!isset($alldata)) ? 'selected' : ''; ?>>-- Pilih --</option>
                    <option value="OK" <?php echo (isset($alldata) && $alldata[0]['par019'] == 'OK') ? 'selected' : ''; ?>>✔ OK</option>
                    <option value="NG" <?php echo (isset($alldata) && $alldata[0]['par019'] == 'NG') ? 'selected' : ''; ?>>✖ NG</option>
                </select>
            </td>
        </tr>

        <tr>
            <td >15</td>
            <td colspan="2">* Cek Kondisi Mesin terhadap Pick Up Miss Chip</td>
            <td >Berdasarkan Sensor</td>
            <td >
                <select class="form-control text-center" id="par020" name="par020" required>
                    <option value="" disabled <?php echo (!isset($alldata)) ? 'selected' : ''; ?>>-- Pilih --</option>
                    <option value="OK" <?php echo (isset($alldata) && $alldata[0]['par020'] == 'OK') ? 'selected' : ''; ?>>✔ OK</option>
                    <option value="NG" <?php echo (isset($alldata) && $alldata[0]['par020'] == 'NG') ? 'selected' : ''; ?>>✖ NG</option>
                </select>
            </td>
        </tr>

        <tr>
            <td rowspan="2">16</td>
            <td rowspan="2" colspan="2">Collet Bonding Arm Check (gunakan jari untuk mengecek <br> pergerakan bonding collet)</td>
            <td >Arah depan-belakang (fix tidak bergerak)</td>
            <td >
                <select class="form-control text-center" id="par021" name="par021" required>
                    <option value="" disabled <?php echo (!isset($alldata)) ? 'selected' : ''; ?>>-- Pilih --</option>
                    <option value="OK" <?php echo (isset($alldata) && $alldata[0]['par021'] == 'OK') ? 'selected' : ''; ?>>✔ OK</option>
                    <option value="NG" <?php echo (isset($alldata) && $alldata[0]['par021'] == 'NG') ? 'selected' : ''; ?>>✖ NG</option>
                </select>
            </td>
        </tr>

        <tr>
            <td >Arah atas-bawah</td>
            <td >
                <select class="form-control text-center" id="par022" name="par022" required>
                    <option value="" disabled <?php echo (!isset($alldata)) ? 'selected' : ''; ?>>-- Pilih --</option>
                    <option value="OK" <?php echo (isset($alldata) && $alldata[0]['par022'] == 'OK') ? 'selected' : ''; ?>>✔ OK</option>
                    <option value="NG" <?php echo (isset($alldata) && $alldata[0]['par022'] == 'NG') ? 'selected' : ''; ?>>✖ NG</option>
                </select>
            </td>
        </tr>

        <tr>
            <td colspan="4">Note</td>
            <td >
              <input class="form-control" type="text" id="par023" name="par023" placeholder="" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par023'];} ?>>
            </td>
        </tr>
    </tbody>   
    </table>
    <div class="note">
        <h3>Catatan</h3>
        <p>
            @: isi kolom dengan nilai (value) di mesin
        </p>
        <p>
            *: Beri tanda V jika OK/Good dan X jika NG
        </p>
        <p>
            #: isi bagian note apabila ada penggantian part    
        </p>
    </div>
</div>