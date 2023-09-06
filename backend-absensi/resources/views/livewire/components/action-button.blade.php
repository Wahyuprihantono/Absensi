<div class="d-flex" wire:ignore id="btn-list-{{ $id }}">
  <button class="btn btn-success btn-sm mr-2" wire:click="getDataById('{{ $id }}')" id="btn-edit-{{ $id }}"><i
      class="fas fa-edit"></i></button>
  <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#confirm-modal"
    wire:click="getId('{{ $id }}')" id="btn-delete-{{ $id }}"><i class="fas fa-trash"></i></button>
</div>