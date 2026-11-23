<div class="table-responsive" id="slvi">
<?= csrf_field(); ?>
<input type="hidden" id="number-edit" name="number-edit" value=<?php if(isset($alldata)){echo $number;} ?>>
    <table class="table table-striped-columns" 
    >
    <thead align="center">
        <tr>
            <th colspan="19" scope="col"><?php echo nl2br("PIN DIE BONDING 1 MACHINE")?></th>
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
          <td rowspan="2">@ Pressure</td>
          <td >Air Pressure</td>
          <td >0.30 ~ 0.50 MPa</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par001',0.3,0.5)" placeholder="" id="par001" name="par001" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par001'];} ?>>
          </td>
        </tr>
        
        <tr>
          <td >N2 Pressure</td>
          <td >0.14 ~ 0.16 MPa</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par002',0.14,0.16)" placeholder="" id="par002" name="par002" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par002'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="2">2</td>
          <td rowspan="2">@ Vacuum pressure</td>
          <td >Chip PP</td>
          <td >-80.0 kPa atau kurang</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par003',-9999999,-80)" placeholder="" id="par003" name="par003" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par003'];} ?>>
          </td>
        </tr>

        <tr>
          <td >Stem PP</td>
          <td >-80.0 kPa atau kurang</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par004',-9999999,-80)" placeholder="" id="par004" name="par004" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par004'];} ?>>
          </td>
        </tr>
        
        <tr>
          <td rowspan="2">3</td>
          <td rowspan="2">@ Collet Load</td>
          <td >Chip PP1 collet</td>
          <td >0.30 ~ 0.50 N</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par005',0.3.0.5)" placeholder="" id="par005" name="par005" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par005'];} ?>>
          </td>
        </tr>

        <tr>
          <td >Chip PP2 collet</td>
          <td >0.30 ~ 0.50 N</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par006',0.3.0.5)" placeholder="" id="par006" name="par006" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par006'];} ?>>
          </td>
        </tr>

        <tr>
          <td >4</td>
          <td colspan="2">@ Needle Push Up Level</td>
          <td >0.4 ~ 0.6 mm</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par007',0.4.0.6)" placeholder="" id="par007" name="par007" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par007'];} ?>>
          </td>
        </tr>

        <tr>
          <td >5</td>
          <td colspan="3">* Needle and collet position check <br>(Cek posisi needle and collet)</td>
          <td>
            <input class="form-control" type="text" placeholder="" id="par008" name="par008" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par008'];} ?>>
          </td>
        </tr>

        <tr>
          <td >6</td>
          <td colspan="3">* Needle and collet Check the shape and dirt <br> (Bersihkan dengan Cotton Bud)</td>
          <td>
            <input class="form-control" type="text" placeholder="" id="par009" name="par009" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par009'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="3">7</td>
          <td rowspan="3">@ Collet down position</td>
          <td >Chip sheet</td>
          <td >-80 ~ -120 μm</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par010',-120,-80)" placeholder="" id="par010" name="par010" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par010'];} ?>>
          </td>
        </tr>

        <tr>
          <td >Middle stage</td>
          <td >-80 ~ -120 μm</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par011',-120,-80)" placeholder="" id="par011" name="par011" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par011'];} ?>>
          </td>
        </tr>

        <tr>
          <td >D/B stage</td>
          <td >-80 ~ -120 μm</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par012',-120,-80)" placeholder="" id="par012" name="par012" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par012'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="3">8</td>
          <td rowspan="3">@ Dispenser indication</td>
          <td >Application pressure</td>
          <td >110 ~ 200 Kpa</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par013',110,200)" placeholder="" id="par013" name="par013" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par013'];} ?>>
          </td>
        </tr>

        <tr>
          <td >Dispense Time</td>
          <td >120 ~ 180 msec</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par014',120,180)" placeholder="" id="par014" name="par014" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par014'];} ?>>
          </td>
        </tr>

        <tr>
          <td >Vaccum Pressure</td>
          <td >-1.1 ~ -0.8 Kpa</td>
          <td>
            <input class="form-control" type="number" step="0.000000001" onchange="changeRange('par015',-1.1,-0.8)" placeholder="" id="par015" name="par015" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par015'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="3">9</td>
          <td rowspan="3" colspan="2">* Clean up</td>
          <td >Chip PP1 collet</td>
          <td>
            <input class="form-control" type="text" placeholder="" id="par016" name="par016" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par016'];} ?>>
          </td>
        </tr> 
        
        <tr>
          <td >Chip PP2 collet</td>
          <td>
            <input class="form-control" type="text" placeholder="" id="par017" name="par017" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par017'];} ?>>
          </td>
        </tr>
 
        <tr>
          <td >Middle stage</td>
          <td>
            <input class="form-control" type="text" placeholder="" id="par018" name="par018" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par018'];} ?>>
          </td>
        </tr>

        <tr>
          <td rowspan="2">10</td>
          <td rowspan="2" colspan="2">* Check kondisi Ag Paste</td>
          <td >Posisi</td>
          <td>
            <input class="form-control" type="text" placeholder="" id="par019" name="par019" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par019'];} ?>>
          </td>
        </tr> 
        
        <tr>
          <td >Quantity</td>
          <td>
            <input class="form-control" type="text" placeholder="" id="par020" name="par020" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par020'];} ?>>
          </td>
        </tr>

        <tr>
          <td >11</td>
          <td colspan="3">* Check the safe door <br> (Buka Pintu dan seharusnya tidak bisa untuk jalankan Automatically)</td>
          <td>
            <input class="form-control" type="text" placeholder="" id="par021" name="par021" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par021'];} ?>>
          </td>
        </tr> 

        <tr>
          <td >12</td>
          <td colspan="3">* Cleaning Machine <br> (Bersihkan mesin dan lingkungan sekitarnya dari debu dan kotoran)</td>
          <td>
            <input class="form-control" type="text" placeholder="" id="par022" name="par022" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par022'];} ?>>
          </td>
        </tr>

        <tr>
          <td colspan="4">Note</td>
          <td>
            <input class="form-control" type="text" placeholder="" id="par023" name="par023" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par023'];} ?>>
          </td>
        </tr>

    </tbody>   
    </table>
</div>