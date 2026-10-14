<section class="container-fluid">
<form name="checksheet" action="/process" method="POST">
    <?= csrf_field(); ?>
    <input type="hidden" name="type-input" value="manual">

    <div class="row">
    <div class="col-md-6">

        <!-- Device input -->
        <div class="mb-3 row">
        <label for="device" class="col-sm-2 col-form-label">Device</label>
        <div class="col-sm-3">
            <select id="device" name="device" class="form-select" aria-label="Default select example">
                <option value="" selected>Pilih Device</option>
                <?php if(!empty($listDevice)) : ?>
                    <?php foreach($listDevice as $dev) : ?>
                        <?php 
                            // Menyesuaikan dengan nama kolom code dan name dari database
                            $kode = is_object($dev) ? $dev->code : $dev['code'];
                            $nama = is_object($dev) ? $dev->name : $dev['name'];
                        ?>
                        <option value="<?= $kode; ?>"><?= $nama; ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        </div>

        <!-- Type input -->
        <div class="mb-3 row">
        <label for="Type" class="col-sm-2 col-form-label">Tipe</label>
        <div class="col-sm-6">
            <select id="type" name="type" class="form-select" aria-label="Default select example">
            <option selected>Pilih tipe</option>
            <option value="production">Production</option>
            <option value="startup">Startup</option>
            <option value="foregoing">Foregoing</option>
            </select>
        </div>
        </div>

        <!-- Process of Device -->
        <div class="mb-3 row">
        <label for="process-name" class="col-sm-2 col-form-label">Nama Process</label>
        <div class="col-sm-6">
            <input type="text" class="form-control" id="process-name" name="process-name" placeholder="Contoh : Wire Bonding">
        </div>
        </div>

        <!-- Code of Process -->
        <div class="mb-3 row">
        <label for="process-code" class="col-sm-2 col-form-label">Kode Process</label>
        <div class="col-sm-6">
            <input type="text" class="form-control" id="process-code" name="process-code" placeholder="Contoh : sl90-p-Wire Bonding">
        </div>
        </div>

        <!-- Doc No -->
        <div class="mb-3 row">
        <label for="docno" class="col-sm-2 col-form-label">Doc. No</label>
        <div class="col-sm-6">
            <input type="text" class="form-control" id="docno" name="docno" placeholder="Contoh : SM-BI-001">
        </div>
        </div>

    </div>
</div>

    <?php 
        if (session()->getFlashdata('message')){ 
        echo '<p id=msgsuccess class="invisible">' . session()->getFlashdata('message').'</p>'; 
        }else{ 
        echo '<p id=msgsuccess class="invisible">Kosong</p>'; ;}
    ?>

    <!-- Input Form inject using javascript -->
    <div id="modal" class="">
    
    </div>

    </div>
    <button type="submit" class="btn btn-primary" id="submit" onclick="return confirm('Apakah kamu yakin?');">Submit</button>
</form>
</section>
<script src="/assets/js/add-process.js"></script>