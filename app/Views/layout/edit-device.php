
<form name="checksheet" action="<?= base_url('device/update') ?>" method="POST">
    <?= csrf_field(); ?>
    <input type="hidden" name="type-input" value="manual">
    <input type="hidden" name="id" value=<?= $alldata[0]['id']?>>

    <div class="row">
        <div class="col-md-6">
            <!-- Code of Device -->
            <div class="mb-3 row">
                <label for="device-code" class="col-sm-2 col-form-label">Kode Device</label>
                <div class="col-sm-6">
                <input type="text" class="form-control" id="device-code" name="device-code" value='<?=$alldata[0]['code']?>'>
                </div>
            </div>

            <!-- Name of Device -->
            <div class="mb-3 row">
                <label for="device-name" class="col-sm-2 col-form-label">Nama Device</label>
                <div class="col-sm-6">
                <input type="text" class="form-control" id="device-name" name="device-name" value='<?=$alldata[0]['name']?>'>
                </div>
            </div>

        </div>
    </div>
    <button type="submit" class="btn btn-primary" id="submit" onclick="return confirm('Apakah kamu yakin?');">Submit</button>
</form>
  