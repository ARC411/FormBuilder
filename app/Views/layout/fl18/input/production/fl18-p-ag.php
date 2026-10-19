<div class="table-responsive" id="fl18-p-ag">
<?= csrf_field(); ?>
<input type="hidden" name="cnt-proses" value="1">
<input type="hidden" id="number-edit" name="number-edit" value=<?php if(isset($alldata)){echo $number;} ?>>
  <table class="table table-striped-columns" >
    <thead align="center">
        <tr>
            <th colspan="5" scope="col"><?php echo nl2br("PRODUCTION PROCESS CONTROL SHEET AG PASTE HARDENING")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th class="input-form" colspan="2" scope="col" >Quantity</th>
            <th class="input-form" colspan="3" scope="col" >Temperature</th>
        </tr>
        <tr>
            <th class="input-form" >Input</th>
            <th class="input-form" >Output</th>
            <th class="input-form" >Setting</th>
            <th class="input-form" >At Input</th>
            <th class="input-form" >At take Out</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td rowspan="1" >
                <input type="number"  oninput="" step="1" class="form-control" id="par013" name="par013" onkeyup=quantityChange6() onchange=quantityChange6() <?php if(isset($alldata)){echo 'value='.$alldata[0]['par013'];} ?>>
            </td>
            <td rowspan="1" >
                <input type="number"  oninput="" step="1" class="form-control" id="par014" name="par014" onkeyup=quantityChange6() onchange=quantityChange6() <?php if(isset($alldata)){echo 'value='.$alldata[0]['par014'];} ?>>
            </td>
            <td rowspan="1" >
                <input type="number" step="0.00000001" class="form-control" id="par015" name="par015" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par015']." readonly";} ?>>
            </td>
            <td rowspan="1" >
                <input type="number" step="0.00000001" class="form-control" id="par016" name="par016" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par016']." readonly";} ?>>
            </td>
            <td rowspan="1" >
                <input type="number" step="0.00000001" class="form-control" id="par017" name="par017" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par017']." readonly";} ?>>
            </td>
        </tr>
    </tbody>
    </table>
</div>