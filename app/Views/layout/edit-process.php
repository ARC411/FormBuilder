<form name="checksheet" action="<?= base_url('process/update') ?>" method="POST">
    <?= csrf_field(); ?>
    <input type="hidden" name="type-input" value="manual">
    <input type="hidden" name="id" value=<?= $alldata[0]['id']?>>

    <div class="row">
        <div class="col-md-6">

            <!-- Device input -->
            <div class="mb-3 row">
                <label for="device" class="col-sm-2 col-form-label">Device</label>
                <div class="col-sm-6">
                    <select id="device" name="device" class="form-select" aria-label="Default select example">
                        <?php
                            foreach ($listDevice as &$device) {
                                echo '<option value="'.$device['code'].'" selected>'.$device['name'].'</option>';
                            }
                        ?>
                        <option value="-" selected>Pilih Device</option>
                    </select>
                </div>
            </div>

            <!-- Type input -->
            <div class="mb-3 row">
                <label for="Type" class="col-sm-2 col-form-label">Tipe</label>
                <div class="col-sm-6">
                    <select id="type" name="type" class="form-select" aria-label="Default select example">
                        <?php
                            if($alldata[0]['type']=='production'){
                                echo '
                                    <option value="production" selected>Production</option>
                                    <option value="startup">Startup</option>
                                    <option value="foregoing">Foregoing</option>
                                ';
                            }
                            else if($alldata[0]['type']=='foregoing'){
                                echo '
                                    <option value="production">Production</option>
                                    <option value="startup">Startup</option>
                                    <option value="foregoing" selected>Foregoing</option>
                                ';
                            }
                            else{
                                echo '
                                    <option value="production" selected>Production</option>
                                    <option value="startup" selected>Startup</option>
                                    <option value="foregoing">Foregoing</option>
                                ';
                            }
                        ?>
                    </select>
                </div>
            </div>

            <!-- Process of Device -->
            <div class="mb-3 row">
                <label for="process-name" class="col-sm-2 col-form-label">Nama Process</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" id="process-name" name="process-name" placeholder="Contoh : Wire Bonding" value='<?= $alldata[0]['process_name']?>'>
                </div>
            </div>

            <!-- Code of Process -->
            <div class="mb-3 row">
                <label for="process-code" class="col-sm-2 col-form-label">Kode Process</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" id="process-code" name="process-code" placeholder="Contoh : sl90-p-Wire Bonding" value='<?= $alldata[0]['process_code']?>'>
                </div>
            </div>

            <!-- Doc No -->
            <div class="mb-3 row">
                <label for="docno" class="col-sm-2 col-form-label">Doc. No</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" id="docno" name="docno" placeholder="Contoh : SM-BI-001" value='<?= $alldata[0]['docno']?>'>
                </div>
            </div>

        </div>
    </div>
    <button type="submit" class="btn btn-primary" id="submit" onclick="return confirm('Apakah kamu yakin?');">Submit</button>
</form>
