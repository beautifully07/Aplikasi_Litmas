@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-xl font-bold mb-6">
        Form Litmas {{ $jenis }} - {{ str_replace('_',' ', $kategori) }}
    </h2>

    <form action="{{ route('litmas.preview') }}" method="POST">
        @csrf

{{-- =========================
    STEP 1: NOTA DINAS
========================= --}}
<div id="step-1">

    <h3 class="text-lg font-semibold mb-4">Nota Dinas</h3>

    <div class="grid grid-cols-2 gap-4">

        <input type="text" name="no_nota_dinas" class="input" placeholder="Nomor Nota Dinas">
        <input type="date" name="tanggal_nota_dinas" class="input">

        <input type="text" name="perihal" class="input" placeholder="Perihal">
        <input type="text" name="kepada" class="input" placeholder="Kepada">

        <input type="text" name="no_surat" class="input" placeholder="Nomor Surat">
        <input type="date" name="tanggal_surat" class="input">

        <input type="text" name="no_register" class="input" placeholder="No Register">
        <input type="text" name="perkara" class="input" placeholder="Perkara">

    </div>
    <div class="mt-4">
        <label>Dasar Hukum</label>
        <select name="pasal_id" class="input">
            <option value="">-- Pilih Pasal --</option>

            @foreach($pasals as $pasal)
                <option value="{{ $pasal->id }}">
                    Pasal {{ $pasal->nomor_pasal }} -
                    {{ $pasal->klasifikasiHukum->nama_klasifikasi ?? '-' }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- PILIH KLIEN --}}
    <div class="mt-4">
        <label>Nama Klien</label>
        <select name="client_id" id="client_id" class="input">
            <option value="">-- Pilih Klien --</option>

            @foreach($clients as $client)
            <option value="{{ $client->id }}"
                data-nama="{{ $client->nama }}"
                data-user="{{ $client->user->name ?? '' }}"
                data-tempat="{{ $client->tempat_lahir }}"
                data-tanggal="{{ $client->tanggal_lahir }}"
                data-alamat="{{ $client->alamat }}"
                data-jk="{{ $client->jenis_kelamin }}"
                data-status="{{ $client->status_perkawinan }}"
                data-agama="{{ $client->agama }}"
                data-pendidikan="{{ $client->pendidikan }}"
                data-pekerjaan="{{ $client->pekerjaan }}"
                data-ciri="{{ $client->ciri_khusus }}">
                {{ $client->nama }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- AUTO STEP 1 --}}
    <div class="grid grid-cols-2 gap-4 mt-4">

        <div>
            <label>TTL</label>
            <input type="text" id="ttl_1"
                class="input bg-gray-100" readonly>
        </div>

        <div>
            <label>Alamat</label>
            <input type="text" id="alamat_1"
                class="input bg-gray-100" readonly>
        </div>

    </div>

    <div class="mt-6 text-right">
        <button type="button" onclick="handleNext()" class="btn-blue">
            Next →
        </button>
    </div>

</div>


{{-- =========================
    STEP 2
========================= --}}
<div id="step-2" style="display:none;">

    <h3 class="text-lg font-semibold mb-4">Data Pembimbing Kemasyarakatan</h3>

    <div class="grid grid-cols-2 gap-4">

        <input type="text"
                id="nama_pk"
                class="input bg-gray-100"
                readonly>

        <input type="text" name="nip" class="input" placeholder="NIP">

        <input type="text" name="jabatan" class="input" placeholder="Jabatan">

    </div>

    <hr class="my-6">

    <h3 class="text-lg font-semibold mb-5 mt-4">Identitas Klien</h3>

    <div class="grid grid-cols-2 gap-4">

        <input type="text" id="nama_klien"
               class="input bg-gray-100" readonly>

        <input type="text" name="no_registrasi"
               class="input" placeholder="No Registrasi">
        
        <input type="date" name="tanggal_wawancara"
               class="input" placeholder="Tanggal Wawancara">

        <input type="text" name="sumber_informasi"
               class="input" placeholder="Sumber Informasi">

        <input type="text" name="no_putusan_pengadilan"
               class="input" placeholder="No Putusan Pengadilan">

        <input type="text" name="tgl_putusan_pengadilan"
               class="input" placeholder="Tanggal Putusan Pengadilan">

        <input type="text" name="lama_pidana"
               class="input" placeholder="Lama Pidana">

        <input type="text" id="ttl_2"
               class="input bg-gray-100" readonly>

        <input type="text" id="jenis_kelamin"
               class="input bg-gray-100" readonly>

        <input type="text" id="status"
               class="input bg-gray-100" readonly>

        <input type="text" id="agama"
               class="input bg-gray-100" readonly>

        <input type="text" id="pendidikan"
               class="input bg-gray-100" readonly>

        <input type="text" id="pekerjaan"
               class="input bg-gray-100" readonly>

        <textarea id="alamat_2"
                  class="input bg-gray-100 col-span-2"
                  readonly></textarea>

        <textarea id="ciri"
                  class="input bg-gray-100 col-span-2"
                  readonly></textarea>
    </div>

    <hr class="my-6">

    <h3 class="text-lg font-semibold mb-5 mt-4">Orang Tua Kandung / Penjamin</h3>

    {{-- ================= AYAH ================= --}}
    <h4 class="font-semibold mt-4 mb-2">Ayah</h4>

    <div class="grid grid-cols-2 gap-4">

        <div>
            <label>Nama Ayah</label>
            <select id="ayah_select" class="input">
                <option value="">-- Pilih Ayah --</option>
            </select>
        </div>

        <div>
            <label>TTL</label>
            <input type="text" id="ayah_ttl" class="input bg-gray-100" readonly>
        </div>

        <div><label>Agama</label><input type="text" id="ayah_agama" class="input bg-gray-100" readonly></div>
        <div><label>Bangsa</label><input type="text" id="ayah_bangsa" class="input bg-gray-100" readonly></div>

        <div><label>Suku</label><input type="text" id="ayah_suku" class="input bg-gray-100" readonly></div>
        <div><label>Kewarganegaraan</label><input type="text" id="ayah_warga" class="input bg-gray-100" readonly></div>

        <div><label>Pendidikan</label><input type="text" id="ayah_pendidikan" class="input bg-gray-100" readonly></div>
        <div><label>Pekerjaan</label><input type="text" id="ayah_pekerjaan" class="input bg-gray-100" readonly></div>

        <div class="col-span-2">
            <label>Alamat</label>
            <textarea id="ayah_alamat" class="input bg-gray-100" readonly></textarea>
        </div>

        <div>
            <label>Hubungan</label>
            <input type="text" id="ayah_hubungan" class="input bg-gray-100" readonly>
        </div>

    </div>


    {{-- ================= IBU ================= --}}
    <h4 class="font-semibold mt-6 mb-2">Ibu</h4>

    <div class="grid grid-cols-2 gap-4">

        <div>
            <label>Nama Ibu</label>
            <select id="ibu_select" class="input"></select>
        </div>

        <div><label>TTL</label><input type="text" id="ibu_ttl" class="input bg-gray-100" readonly></div>
        <div><label>Agama</label><input type="text" id="ibu_agama" class="input bg-gray-100" readonly></div>
        <div><label>Bangsa</label><input type="text" id="ibu_bangsa" class="input bg-gray-100" readonly></div>
        <div><label>Suku</label><input type="text" id="ibu_suku" class="input bg-gray-100" readonly></div>
        <div><label>Kewarganegaraan</label><input type="text" id="ibu_warga" class="input bg-gray-100" readonly></div>
        <div><label>Pendidikan</label><input type="text" id="ibu_pendidikan" class="input bg-gray-100" readonly></div>
        <div><label>Pekerjaan</label><input type="text" id="ibu_pekerjaan" class="input bg-gray-100" readonly></div>

        <div class="col-span-2">
            <label>Alamat</label>
            <textarea id="ibu_alamat" class="input bg-gray-100" readonly></textarea>
        </div>

        <div><label>Hubungan</label><input type="text" id="ibu_hubungan" class="input bg-gray-100" readonly></div>

    </div>


    {{-- ================= PENJAMIN ================= --}}
    <h4 class="font-semibold mt-6 mb-2">Penjamin</h4>

    <div class="grid grid-cols-2 gap-4">

        <div>
            <label>Nama Penjamin</label>
            <select id="penjamin_select" class="input"></select>
        </div>

        <div><label>TTL</label><input type="text" id="penjamin_ttl" class="input bg-gray-100" readonly></div>
        <div><label>Agama</label><input type="text" id="penjamin_agama" class="input bg-gray-100" readonly></div>
        <div><label>Bangsa</label><input type="text" id="penjamin_bangsa" class="input bg-gray-100" readonly></div>
        <div><label>Suku</label><input type="text" id="penjamin_suku" class="input bg-gray-100" readonly></div>
        <div><label>Kewarganegaraan</label><input type="text" id="penjamin_warga" class="input bg-gray-100" readonly></div>
        <div><label>Pendidikan</label><input type="text" id="penjamin_pendidikan" class="input bg-gray-100" readonly></div>
        <div><label>Pekerjaan</label><input type="text" id="penjamin_pekerjaan" class="input bg-gray-100" readonly></div>

        <div class="col-span-2">
            <label>Alamat</label>
            <textarea id="penjamin_alamat" class="input bg-gray-100" readonly></textarea>
        </div>

        <div><label>Hubungan</label><input type="text" id="penjamin_hubungan" class="input bg-gray-100" readonly></div>

    </div>

    <div class="flex justify-between mt-6">
        <button type="button" onclick="prevStep(1)" class="btn-gray">
            ← Back
        </button>

        <button type="submit" class="btn-green">
            Preview
        </button>
    </div>

</div>

    </form>
</div>

{{-- STYLE --}}
<style>
.input { width:100%; border:1px solid #ccc; padding:8px; border-radius:6px; }
.btn-blue { background:#2563eb; color:white; padding:8px 16px; border-radius:6px; }
.btn-gray { background:#6b7280; color:white; padding:8px 16px; border-radius:6px; }
.btn-green { background:#16a34a; color:white; padding:8px 16px; border-radius:6px; }
</style>

{{-- =========================
    JAVASCRIPT
========================= --}}
<script>

/* STEP */
function nextStep(step){
    document.getElementById('step-1').style.display='none';
    document.getElementById('step-2').style.display='none';
    document.getElementById('step-'+step).style.display='block';
}

function prevStep(step){
    nextStep(step);
}

function handleNext(){
    document.getElementById('client_id').dispatchEvent(new Event('change'));
    nextStep(2);
}

/* FORMAT TANGGAL */
function formatTanggal(tanggal){
    if(!tanggal) return '';

    const bulan = ["Januari","Februari","Maret","April","Mei","Juni",
                   "Juli","Agustus","September","Oktober","November","Desember"];

    let d = new Date(tanggal);
    return d.getDate()+' '+bulan[d.getMonth()]+' '+d.getFullYear();
}

/* =========================
   AUTO FILL CLIENT
========================= */
document.getElementById('client_id').addEventListener('change', function(){

    let opt = this.options[this.selectedIndex];

    let ttl = opt.dataset.tempat + ', ' + formatTanggal(opt.dataset.tanggal);

    // STEP 1
    document.getElementById('ttl_1').value = ttl;
    document.getElementById('alamat_1').value = opt.dataset.alamat ?? '';

    // STEP 2
    document.getElementById('nama_klien').value = opt.dataset.nama ?? '';
    document.getElementById('ttl_2').value = ttl;

    document.getElementById('jenis_kelamin').value =
        opt.dataset.jk == 'L' ? 'Laki-laki' :
        opt.dataset.jk == 'P' ? 'Perempuan' : '';

    document.getElementById('status').value = opt.dataset.status ?? '';
    document.getElementById('agama').value = opt.dataset.agama ?? '';
    document.getElementById('pendidikan').value = opt.dataset.pendidikan ?? '';
    document.getElementById('pekerjaan').value = opt.dataset.pekerjaan ?? '';
    document.getElementById('alamat_2').value = opt.dataset.alamat ?? '';
    document.getElementById('ciri').value = opt.dataset.ciri ?? '';

    // NAMA PK DARI CLIENT
    document.getElementById('nama_pk').value =
        opt.dataset.user ?? 'Tidak ada PK';


    /* =========================
       AMBIL DATA ORTU / PENJAMIN
    ========================== */
    let clientId = this.value;

    fetch(`/ajax/penjamin/${clientId}`)
    .then(res => res.json())
    .then(data => {

        console.log('DATA KELUARGA:', data);

        let ayah = data.filter(d => d.jenis?.toLowerCase() === 'ayah');
        let ibu = data.filter(d => d.jenis?.toLowerCase() === 'ibu');
        let penjamin = data.filter(d => d.jenis?.toLowerCase() === 'penjamin');

        setDropdown('ayah_select', ayah);
        setDropdown('ibu_select', ibu);
        setDropdown('penjamin_select', penjamin);

    })
    .catch(err => console.error('Error keluarga:', err));

});


/* =========================
   SET DROPDOWN
========================= */
function setDropdown(id, data){
    let el = document.getElementById(id);

    if(!el) return; // biar aman kalau elemen belum ada

    el.innerHTML = '<option value="">-- Pilih --</option>';

    data.forEach(item => {
        let opt = document.createElement('option');
        opt.value = item.id;
        opt.text = item.nama;
        opt.dataset.data = JSON.stringify(item);
        el.appendChild(opt);
    });
}


/* =========================
   ISI DETAIL DATA
========================= */
function isiData(prefix, data){

    let ttl = data.tempat_lahir + ', ' + formatTanggal(data.tanggal_lahir);

    setVal(prefix+'_ttl', ttl);
    setVal(prefix+'_agama', data.agama);
    setVal(prefix+'_bangsa', data.bangsa);
    setVal(prefix+'_suku', data.suku);
    setVal(prefix+'_warga', data.kewarganegaraan);
    setVal(prefix+'_pendidikan', data.pendidikan);
    setVal(prefix+'_pekerjaan', data.pekerjaan);
    setVal(prefix+'_alamat', data.alamat);
    setVal(prefix+'_hubungan', data.hubungan);
}


/* =========================
   HELPER BIAR AMAN
========================= */
function setVal(id, value){
    let el = document.getElementById(id);
    if(el) el.value = value ?? '';
}


/* =========================
   EVENT DROPDOWN ORTU
========================= */
['ayah','ibu','penjamin'].forEach(prefix => {

    let el = document.getElementById(prefix+'_select');

    if(!el) return;

    el.addEventListener('change', function(){

        let data = this.options[this.selectedIndex].dataset.data;

        if (data) {
            isiData(prefix, JSON.parse(data));
        } else {
            isiData(prefix, {}); // reset kalau kosong
        }

    });

});
</script>

@endsection 