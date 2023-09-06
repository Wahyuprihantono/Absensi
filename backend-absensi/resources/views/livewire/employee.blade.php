<div class="page-inner">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">
            <a href="{{route('dashboard')}}">
              <span><i class="fas fa-arrow-left mr-3"></i>Data Karyawan</span>
            </a>
            <div class="pull-right">
              @if (!$form && !$modal)
              <button class="btn btn-danger btn-sm" wire:click="toggleForm(false)"><i class="fas fa-times"></i>
                Batal</button>
              @else
              <button class="btn btn-primary btn-sm" wire:click="{{$modal ? 'showModal' : 'toggleForm(true)'}}"><i
                  class="fas fa-plus"></i>
                Tambah Data Karyawan</button>
              @endif
            </div>
          </h4>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <livewire:table.karyawan-table />
    </div>

    {{-- Modal form --}}
    <div id="form-modal" wire:ignore.self class="modal fade" tabindex="-1" permission="dialog"
      aria-labelledby="my-modal-title" aria-hidden="true">
      <div class="modal-dialog" permission="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="my-modal-title">{{$update_mode ? 'Edit':'Tambah'}} Data
              Karyawan
            </h5>
          </div>
          <div class="modal-body">
            <x-text-field type="text" name="npk" label="NPK" />
            <x-text-field type="text" name="nama_karyawan" label="Nama karyawan" />
            <x-text-field type="text" name="telepon_karyawan" label="Nomor HP" />
            <x-text-field type="text" name="email_karyawan" label="Email" />
            <x-select name="jabatan_karyawan" label="Jabatan">
              <option value="">Pilih Salah satu</option>
              <option value="Kepala Sekolah">Staff</option>
              <option value="TU">Office Boy</option>
              <option value="Guru">Office Girl</option>
              <option value="Penjaga Sekolah">IT Suport</option>
            </x-select>
            <x-select name="is_whf" label="WFH/WFO">
              <option value="">Pilih Salah satu</option>
              <option value="1">WFH</option>
              <option value="0">WFO</option>
            </x-select>
            @if ($is_whf != '' && !$is_whf > 0)
            <x-select name="lokasi_id" label="Lokasi">
              <option value="">Pilih Lokasi</option>
              @foreach ($locations as $location)
              <option value="{{$location->id}}">{{$location->nama_lokasi}}}</option>
              @endforeach
            </x-select>
            @endif
          </div>
          <div class="modal-footer">
            <button type="button" wire:click={{$update_mode ? 'update' : 'store'}} class="btn btn-primary btn-sm"><i
                class="fa fa-check pr-2"></i>Simpan</button>
            <button class="btn btn-danger btn-sm" wire:click='_reset'><i class="fa fa-times pr-2"></i>Batal</a>
          </div>
        </div>
      </div>
    </div>


    {{-- Modal confirm --}}
    <div id="confirm-modal" wire:ignore.self class="modal fade" tabindex="-1" permission="dialog"
      aria-labelledby="my-modal-title" aria-hidden="true">
      <div class="modal-dialog" permission="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="my-modal-title">Konfirmasi Hapus</h5>
          </div>
          <div class="modal-body">
            <p>Apakah anda yakin hapus data ini.?</p>
          </div>
          <div class="modal-footer">
            <button type="submit" wire:click='delete' class="btn btn-danger btn-sm"><i class="fa fa-check pr-2"></i>Ya,
              Hapus</button>
            <button class="btn btn-primary btn-sm" wire:click='_reset'><i class="fa fa-times pr-2"></i>Batal</a>
          </div>
        </div>
      </div>
    </div>
  </div>
  @push('scripts')
  <script>
    document.addEventListener('livewire:load', function(e) {
            window.livewire.on('showModal', (data) => {
                $('#form-modal').modal('show')
            });

            window.livewire.on('closeModal', (data) => {
                $('#confirm-modal').modal('hide')
                $('#form-modal').modal('hide')
            });
        })
  </script>
  @endpush
</div>