<div class="table-responsive" id="fl18-s-ag">
<?= csrf_field(); ?>
<input type="hidden" id="number-edit" name="number-edit" value="<?php if(!empty($alldata)){echo $number;} ?>">
    <table class="table table-striped-columns">
    <thead align="center">
        <tr>
        <th colspan="5" scope="col"><?php echo nl2br("AG PASTE HARDENING OVEN START UP CHECK SHEET (FRAME LASER 1.8t, 1.3t & GREEN FRAME 1.8t)")?></th>
        </tr>
        <tr>
            <th class="input-form" scope="col" colspan="4">Start Up Check Item</th>
            <th class="input-form" scope="col" rowspan="2">Note</th>
        </tr>

        <tr>
            <th class="input-form">A@</th>
            <th class="input-form">B@</th>
            <th class="input-form">C*</th>
            <th class="input-form">D*</th>
        </tr>
    </thead>

    <tbody>
        <tr>
            <td rowspan="1">
                <input class="form-control" type="number" step="0.001" id="par001" name="par001" onchange="changeRange('par001',150,160)" placeholder="Temp (°C)" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par001'].'"';} ?>>
            </td>
            <td rowspan="1">
                <input class="form-control" type="number" step="0.001" id="par002" name="par002" onchange="changeRange('par002',25,35)" placeholder="Damper (%)" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par002'].'"';} ?>>
            </td>
            <td rowspan="1">
                <input class="form-control" type="text" id="par003" name="par003" placeholder="Door (V/X)" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par003'].'"';} ?>>
            </td>
            <td rowspan="1">
                <input class="form-control" type="text" id="par004" name="par004" placeholder="Clean (V/X)" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par004'].'"';} ?>>
            </td>
            <td rowspan="1">
                <input class="form-control" type="text" id="par005" name="par005" placeholder="Note" <?php if(!empty($alldata)){echo 'value="'.$alldata[0]['par005'].'"';} ?>>
            </td>
        </tr>
    </tbody>   
    </table>
    <div class="note">
        <h3>Catatan & Standar Item Check:</h3>
        <p>
            <strong>A. Checking of temperature setting oven</strong><br>
            - 1. Ag Paste TS-1850 : Standard 150°C ~ 160°C<br>
            - 2. Ag Paste TS-9853G : Standard 195°C ~ 200°C
        </p>
        <p>
            <strong>B. Setting open-close damper</strong><br>
            - Standard : 25% ~ 35%
        </p>
        <p>
            <strong>C. Checking of oven door</strong><br>
            - Standard : Periksa apakah pintu oven rapat saat ditutup (isi V jika rapat/ditutup, isi X jika tidak)
        </p>
        <p>
            <strong>D. Cleaning of oven</strong><br>
            - Standard : Bersihkan oven dari debu atau kotoran menggunakan lap dan dari device yang terjatuh (isi V jika dibersihkan, isi X jika tidak)
        </p>
    </div>
</div>
<!-- http://startupman.dev.ssi:8080/assets/js/form/fl18-s-ag.js -->
<script src='<?=base_url().'assets/js/form/fl18-s-ag.js'?>'></script>
