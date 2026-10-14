<div class="table-responsive mt-3" id="sl56-s-ag">
<?= csrf_field(); ?>
<?php $lenData = count($alldata) ?>
<input type="hidden" id="cnt-table" value="1">
<table class="table table-bordered table-hover" id="table1">
    <thead align="center">
      <tr>
        <th colspan="8" scope="col"><?php echo nl2br("AG PASTE HARDENING")?></th>
      </tr>
    </thead>
    <thead align="center">
      <tr>
        <th rowspan="2" scope="col" >Day</th>
        <th colspan="4" scope="col" >Start Up Check Item</th>
        <th rowspan="2" scope="col" >Status</th>
        <th rowspan="2" scope="col" >Operator</th>
        <th rowspan="2" scope="col" >Time</th>
      </tr>
      <tr>
        <th rowspan="1" >A </th>
        <th rowspan="1" >B* </th>
        <th rowspan="1" >C* </th>
        <th rowspan="1" >D* </th>
      </tr>
    </thead>
    <tbody>
        <?php for($i = 0; $i < count($alldata); $i=$i+2) { ?>
            <tr>
                <td><?= date('d/m/Y', strtotime((string)$alldata[$i]['created_at'])); ?></td>
                <td><?= (string)$alldata[$i]['par001']; ?></td>
                <td><?= (string)$alldata[$i]['par002']; ?></td>
                <td><?= (string)$alldata[$i]['par003']; ?></td>
                <td><?= (string)$alldata[$i]['par004']; ?></td>
                <td><?= (string)$alldata[$i]['status']; ?></td>
                <?php
                // Ini baris baru hasil replace:
                $roleKey = (string)$alldata[$i]['role']; if($roleKey && $roleKey !== '-' && isset($alldata[$i][$roleKey])){
                    // Ini baris lama yang tidak ikut direplace, tapi SEKARANG AMAN karena dilindungi if di atas:
                    echo '<td>'. (string)$alldata[$i][(string)$alldata[$i]['role']] . '</td>';
                } 
                else{
                    echo '<td>  </td>';
                }
                ?>
                <td><?= date('H:i', strtotime((string)$alldata[$i]['updated_at'])); ?></td>
            </tr>
            
        <?php } ?>
    </tbody>
  </table>
</div>