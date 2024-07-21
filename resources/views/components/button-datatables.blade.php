@props(['edit', 'detail', 'pick', 'delete'])
<div class="w-100 d-flex justify-content-center gap-1">
    @isset($edit)
    <button type="button" class="btn bg-gradient-warning btn-xs" x-on:click="{{$edit['action']}}" data-id="{{$edit['dataId']}}">
        <i class="fas fa-edit"></i>
    </button>
    @endisset
    
    @isset($detail)
    <button type="button" class="btn bg-gradient-lightblue btn-xs" data-id="{{$detail['dataId']}}">
        <i class="fas fa-info-circle"></i>
    </button>
    @endisset

    @isset($pick)
    <button type="button" class="btn bg-gradient-navy btn-xs" wire:target="{{$pick['loadingTarget']}}" x-on:click="{{$pick['action']}}">
        <i class="fas fa-hand-point-right"></i>
    </button>
    @endisset
    
    @isset($delete)
    <button type="button" class="btn bg-gradient-maroon btn-xs" x-on:click="{{$delete['action']}}" data-id="{{$delete['dataId']}}">
        <i class="fas fa-trash-alt"></i>
    </button>
    @endisset
    
</div>