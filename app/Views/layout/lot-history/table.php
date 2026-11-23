<table class="table table-striped-columns text-nonwrap" id="table-lot-history">
    <thead align="center">
        <tr>
            <th colspan="21" scope="col" class="header"><?php echo nl2br("Lot Pending History")?></th>
        </tr>
    </thead>
    <thead align="center">
        <tr>
            <th class="sorting">No.</th>
            <th>Start Date</th>     
            <th>End Date</th>         
            <th>Lot No</th>
            <th>Process</th>
            <th>Status</th>
        </tr>
    </thead>
    
    <tbody>
        <?php for($i = 0; $i < count($alldata); $i=$i+1) { ?>
            <tr>
                <td rowspan="1" ><?= $i+1; ?></td>
                <td rowspan="1" ><?= $alldata[$i]['created_at']; ?></td>
                <td rowspan="1" ><?= $alldata[$i]['par009']; ?></td>
                <td rowspan="1" ><?= $alldata[$i]['lotno']; ?></td>
                <td rowspan="1" >
                    <?=$alldata[$i]['name']; ?>
                </td>
                <td rowspan="1" >
                    <?php 
                    if($alldata[$i]['par045']=="Process Start"){
                        echo '<a href="' . base_url('production/edit/' . $alldata[$i]['number']) . '">' . $alldata[$i]['par045'] . '</a>';
                    }
                    else{
                        echo $alldata[$i]['par045'];
                    }
                    ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table> 