<div class="page-inner">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">
            <a href="{{route('dashboard')}}">
              <span><i class="fas fa-arrow-left mr-3"></i>Data Absen</span>
            </a>
          </h4>
        </div>
      </div>
    </div>

    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <form @if ($tanggal_mulai && $tanggal_selesai) target="_blank" @endif action="{{route('laporan.absensi')}}"
            method="post">
            @csrf
            <x-text-field type="hidden" name="npk" value="{{$npk}}" label="Tanggal Mulai" />
            <div class="row">
              <div class="col-md-4">
                <x-select name="npk" label="Karyawan" handleChange="handleChange">
                  <option value="">Pilih Karyawan</option>
                  @foreach ($employess as $employee)
                  <option value="{{$employee->npk}}">{{$employee->nama_karyawan}}</option>
                  @endforeach
                </x-select>
              </div>
              <div class="col-md-4">
                <x-text-date type="date" name="tanggal_mulai" value="{{$tanggal_mulai}}" label="Tanggal Mulai" />
              </div>
              <div class="col-md-4">
                <x-text-date type="date" name="tanggal_selesai" value="{{$tanggal_selesai}}" min="{{$tanggal_mulai}}"
                  max="{{date('Y-m-d')}}" label="Tanggal Selesai" />
              </div>
            </div>
            <button type="button" class="btn btn-primary ml-2" wire:click="filter">Tampilkan</button>
            @if ($show)
            <button type="submit" class="btn btn-danger ml-2">Cetak</button>
            @endif
          </form>
        </div>
      </div>
    </div>

    <div class="col-md-12">
      <livewire:table.data-absen-table />
    </div>

    @push('scripts')
    <script>
      document.addEventListener('livewire:load', function(e) {
           
        })
    </script>
    @endpush
  </div>
</div>