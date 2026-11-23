<!-- FL Tie Bar Cutting -->
<div class="table-responsive" id="flwb2">
<?= csrf_field(); ?>
<input type="hidden" name="cnt-proses" value="1">
<input type="hidden" id="number-edit" name="number-edit" value=<?php if(isset($alldata)){echo $number;} ?>>
    <table class="table table-striped-columns table-responsive">
        <thead align="center">
            <tr>
                <th colspan="10" scope="col"><?php echo nl2br("INSPECTION WIRE BONDING CONTROL SHEET")?></th>
            </tr>
            <tr>
                <th scope="col" rowspan="2">WB No.</th>
                <th scope="col" colspan="4">Quantity</th>
                <th scope="col" colspan="32">Reject Item</th>
                <th rowspan="2">Note</th>
            </tr>
            <tr>
                <th scope="col">Input</th>
                <th scope="col">Output</th>
                <th scope="col">NG</th>
                <th scope="col">Yield </th>
                <th scope="col">GD1t Diff</th>
                <th scope="col">GD2d Diff</th>
                <th scope="col">GD2d Miss</th>
                <th scope="col">GD Loop NG</th>
                <th scope="col">GD No Wire</th>
                <th scope="col">GD1t Miss</th>
                <th scope="col">No GL2d GD</th>
                <th scope="col">LD1t Diff</th>
                <th scope="col">LD2d Diff</th>
                <th scope="col">LD1t Miss</th>
                <th scope="col">LD2d Miss</th>
                <th scope="col">LD Loop NG</th>
                <th scope="col">W Det Miss</th>
                <th scope="col">@LD Lack</th>
                <th scope="col">@Ld Dirt</th>
                <th scope="col">LD Peel</th>
                <th scope="col">LD Scratch</th>
                <th scope="col">LD Crack</th>
                <th scope="col">Ag Few</th>
                <th scope="col">Ag Much</th>
                <th scope="col">Ag NG</th>
                <th scope="col">Sic Slant</th>
                <th scope="col">Sic Peel</th>
                <th scope="col">Doublewire</th>
                <th scope="col">Wire Touch</th>
                <th scope="col">Wire Cut</th>
                <th scope="col">A Failure</th>
                <th scope="col">B Point NG</th>
                <th scope="col">Gold Thick</th>
                <th scope="col">Pos.Gold</th>
                <th scope="col">Sic Float</th>
                <th scope="col">Others</th>
            </tr>
        </thead>
        <tbody>
            <td>
                <input type="text" qqqqclass="form-control" id="par001" name="par001" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par001'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par002" name="par002" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par002'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par003" name="par003" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par003'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par004" name="par004" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par004'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par005" name="par005">
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par006" name="par006">
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par007" name="par007" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par007'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par008" name="par008" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par008'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par009" name="par009" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par009'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par010" name="par010" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par010'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par011" name="par011" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par011'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par012" name="par012" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par012'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par013" name="par013" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par013'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par014" name="par014" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par014'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par015" name="par015" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par015'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par016" name="par016" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par016'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par017" name="par017" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par017'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par018" name="par018" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par018'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par019" name="par019" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par019'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par020" name="par020" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par020'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par021" name="par021" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par021'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par022" name="par022" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par022'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par023" name="par023" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par023'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par024" name="par024" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par024'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par025" name="par025" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par025'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par026" name="par026" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par026'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par027" name="par027" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par027'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par028" name="par028" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par028'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par029" name="par029" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par029'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par030" name="par030" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par030'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par031" name="par031">
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par032" name="par032" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par032'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par033" name="par033" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par033'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par034" name="par034" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par034'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par035" name="par035" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par035'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par036" name="par036" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par036'];} ?>>
            </td>
            <td>
                <input type="number"  oninput="" step="0.00000001" class="form-control" id="par037" name="par037" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par037'];} ?>>
            </td>
            <td>
                <input type="text" class="form-control" id="par038" name="par038" <?php if(isset($alldata)){echo 'value='.$alldata[0]['par038'];} ?>>
            </td>
            </tbody>
        </table>
    </div>
</div>