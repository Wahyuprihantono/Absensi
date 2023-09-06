<div class="page-inner">
  <div class="row">
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">
            <a href="{{route('dashboard')}}">
              <span><i class="fas fa-arrow-left mr-3"></i>Data Log</span>
            </a>
          </h4>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <livewire:table.data-log-table />
    </div>

    @push('scripts')
    <script>
      document.addEventListener('livewire:load', function(e) {
           
        })
    </script>
    @endpush
  </div>
</div>