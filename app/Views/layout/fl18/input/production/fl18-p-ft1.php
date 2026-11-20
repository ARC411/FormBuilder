<div class="table-responsive" id="fl18-p-ft1">
<?= csrf_field(); ?>
<input type="hidden" name="cnt-proses" value="3">
<input type="hidden" id="number-edit" name="number-edit" value="<?php if(isset($alldata)){echo $number;} ?>">
      <table class="table table-striped-columns">
        <thead align="center">
          <tr>
            <th colspan="13" scope="col"><?php echo nl2br("PRODUCTION CONTROL SHEET OF FINAL TEST GH15SERIES MODEL\n(FRAME LASER 1.8t)")?></th>
          </tr>
        </thead>
        <thead align="center">
          <tr>
            <th class="input-form" colspan="3" scope="col">Quantity</th>
            <th class="input-form" colspan="10" scope="col">Failure Item</th>
          </tr>
        
          <tr>            
            <th class="input-form" rowspan="3">Input</th>
            <th class="input-form" rowspan="1" colspan="2">Output</th>
            <th class="input-form">lfP/RdP/ldk1/lmp/mKi (00)</th>
            <th class="input-form">lf.LD (01)</th>
            <th class="input-form">LD-ir (02)</th>
            <th class="input-form">Vop (03)</th>
            <th class="input-form">lop (04)</th>
            <th class="input-form">lth (05)</th>
            <th class="input-form">Eta (06)</th>
            <th class="input-form">L.Kink2 (07)</th>
            <th class="input-form">L.Kink3 (08)</th>
            <th class="input-form">L.Kink4 (09)</th>
          </tr>
        
          <tr>           
            <th class="input-form" rowspan="2">GO</th>
            <th class="input-form" rowspan="2">NG</th>
            <th>Pol (0A)</th>
            <th>PhPl (0B)</th>
            <th>ThV (0C)</th>
            <th>Thh (0D)</th>
            <th>Phv (0E)</th>
            <th>Phh (1F)</th>
            <th>Rpv2 (10)</th>
            <th>Rph2 (11)</th>
            <th>Lp (12)</th>
            <th>LD Short (13)</th>
          </tr>
          <tr>  
            <th scope="col">LD Open (14)</th>
            <th scope="col">lop.Err (15)</th>
            <th scope="col">Pol.Err (17)</th>
            <th scope="col">FFV.Err (18)</th>
            <th scope="col">FFh.Err (19)</th>
            <th scope="col">SP.Err (1A)</th>
            <th scope="col">Ldir.Err (1B)</th>
            <th scope="col">m.Err/PDIr.Err (1C)</th>
            <th scope="col">Rpv3 (1D)</th>
            <th scope="col">Rph3 (1E)</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td rowspan="3">
              <input type="number" step="1" class="form-control" id="par013" name="par013" onkeyup="quantityChange4()" onchange="quantityChange4()" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par013'].'" readonly';} ?>>
            </td>
            <td rowspan="3">
              <input type="number" step="1" class="form-control" id="par014" name="par014" onkeyup="quantityChange4()" onchange="quantityChange4()" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par014'].'"';} ?>>
            </td>
            <td rowspan="3">
              <input type="number" step="1" class="form-control" id="par015" name="par015" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par015'].'"';} ?> readonly>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par017a" name="par017a" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par017'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par018a" name="par018a" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par018'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par019a" name="par019a" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par019'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par020a" name="par020a" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par020'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par021a" name="par021a" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par021'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par022a" name="par022a" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par022'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par023a" name="par023a" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par023'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par024a" name="par024a" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par024'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par025a" name="par025a" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par025'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par026a" name="par026a" <?php if(isset($alldata)){echo 'value="'.$alldata[0]['par026'].'"';} ?>>
            </td>
          </tr>
          <tr>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par017b" name="par017b" <?php if(isset($alldata)){echo 'value="'.$alldata[1]['par017'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par018b" name="par018b" <?php if(isset($alldata)){echo 'value="'.$alldata[1]['par018'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par019b" name="par019b" <?php if(isset($alldata)){echo 'value="'.$alldata[1]['par019'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par020b" name="par020b" <?php if(isset($alldata)){echo 'value="'.$alldata[1]['par020'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par021b" name="par021b" <?php if(isset($alldata)){echo 'value="'.$alldata[1]['par021'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par022b" name="par022b" <?php if(isset($alldata)){echo 'value="'.$alldata[1]['par022'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par023b" name="par023b" <?php if(isset($alldata)){echo 'value="'.$alldata[1]['par023'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par024b" name="par024b" <?php if(isset($alldata)){echo 'value="'.$alldata[1]['par024'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par025b" name="par025b" <?php if(isset($alldata)){echo 'value="'.$alldata[1]['par025'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par026b" name="par026b" <?php if(isset($alldata)){echo 'value="'.$alldata[1]['par026'].'"';} ?>>
            </td>
          </tr>
          <tr>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par017c" name="par017c" <?php if(isset($alldata)){echo 'value="'.$alldata[2]['par017'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par018c" name="par018c" <?php if(isset($alldata)){echo 'value="'.$alldata[2]['par018'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par019c" name="par019c" <?php if(isset($alldata)){echo 'value="'.$alldata[2]['par019'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par020c" name="par020c" <?php if(isset($alldata)){echo 'value="'.$alldata[2]['par020'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par021c" name="par021c" <?php if(isset($alldata)){echo 'value="'.$alldata[2]['par021'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par022c" name="par022c" <?php if(isset($alldata)){echo 'value="'.$alldata[2]['par022'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par023c" name="par023c" <?php if(isset($alldata)){echo 'value="'.$alldata[2]['par023'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par024c" name="par024c" <?php if(isset($alldata)){echo 'value="'.$alldata[2]['par024'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par025c" name="par025c" <?php if(isset($alldata)){echo 'value="'.$alldata[2]['par025'].'"';} ?>>
            </td>
            <td>
              <input type="number" step="0.00000001" class="form-control" id="par026c" name="par026c" <?php if(isset($alldata)){echo 'value="'.$alldata[2]['par026'].'"';} ?>>
            </td>
          </tr>
        </tbody>
    </table>
</div>
