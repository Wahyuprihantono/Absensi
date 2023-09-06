<div class="page-inner">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">
            <a href="{{route('dashboard')}}">
              <span><i class="fas fa-arrow-left mr-3"></i>Data Lokasi</span>
            </a>
            <div class="pull-right">
              @if ($form_active)
              <button class="btn btn-danger btn-sm" wire:click="toggleForm(false)"><i class="fas fa-times"></i>
                Batal</button>
              @else
              <button class="btn btn-primary btn-sm" wire:click="{{$modal ? 'showModal' : 'toggleForm(true)'}}"><i
                  class="fas fa-plus"></i>
                Tambah Data Baru</button>
              @endif
            </div>
          </h4>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      @if ($form_active)
      <div class="card">
        <div class="card-body">
          <x-text-field type="text" name="nama_lokasi" label="Nama Lokasi" />
          <x-text-field type="text" name="latitude" label="latitude" readonly />
          <x-text-field type="text" name="longitude" label="longitude" readonly />
          <div wire:ignore class="form-group">
            <div id='map' class="w-100" style='height: 500px;'></div>
          </div>
          <div class="form-group">
            <button class="btn btn-primary pull-right"
              wire:click="{{$update_mode ? 'update' : 'store'}}">Simpan</button>
          </div>

        </div>
      </div>
      @else
      <livewire:table.lokasi-table />
      @endif
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
  @push('styles')
  <link href='https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.css' rel='stylesheet' />
  <script src='https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.js'></script>
  <link rel="stylesheet"
    href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.css" type="text/css">
  @endpush
  @push('scripts')

  <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.min.js">
  </script>

  <script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js"></script>

  <script>
    document.addEventListener('livewire:load', function(e) {
            var longlat = [{{$longitude}},{{$latitude}}]
            var accessToken = 'pk.eyJ1IjoieXVkaWNhbmRyYTEiLCJhIjoiY2tuemd6dXhoMDR1ZDJ2cGMzbGk0dHpoaSJ9.Y5TZzkmHQd4Q2hWpDllpsQ';
            window.livewire.on('loadForm', (data) => {
                mapboxgl.accessToken = accessToken
                longlat = [data.longitude,data.latitude]
                map = renderMap(longlat)
                let geocoder = new MapboxGeocoder({
                    accessToken: mapboxgl.accessToken,
                    mapboxgl: mapboxgl
                })
                map.addControl(geocoder);
                
                geocoder.on('result', function (e) {
                    let coordinates = e.result.geometry.coordinates
                    @this.set('nama_lokasi', e.result.place_name);
                    let map = renderMap(coordinates)
                    setMaker(coordinates,map)
                    @this.set('latitude', coordinates[1]);
                    @this.set('longitude', coordinates[0]);

                    
                })
                getAddress(longlat.join(','))
                setMaker(longlat,map)
            });

            window.livewire.on('closeModal', (data) => {
                $('#confirm-modal').modal('hide')
            });

            function renderMap(coordinates) {
               let map = new mapboxgl.Map({
                    container: 'map', // container ID
                    style: 'mapbox://styles/mapbox/streets-v11', // style URL
                    center: coordinates, // starting position [lng, lat]
                    zoom: 15 // starting zoom
                });
                return map
            }

            function setMaker(coordinates, map) {
                let marker = new mapboxgl.Marker({
                    color: "#4834d4",
                    draggable: true,
                })
                .setLngLat(coordinates)
                .addTo(map);

                marker.on('dragend', () => {
                    let latlong = marker.getLngLat()
                    @this.set('latitude', latlong.lat);
                    @this.set('longitude', latlong.lng);

                    getAddress([latlong.lng,latlong.lat].join(','))
                });
            }

            function getAddress(coordinate) {
                let url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${coordinate}.json?types=poi&access_token=${accessToken}`
                fetch(url).then(res => res.json()).then(result => {
                    @this.set('nama_lokasi', result.features[0].place_name);
                })
            }
        })
  </script>
  @endpush
</div>