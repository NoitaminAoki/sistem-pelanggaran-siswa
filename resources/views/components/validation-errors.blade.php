@if($errors->has('plain'))
    <div class="px-2 py-3 mb-2 bg-red-200 border border-red-600 text-red-500">
        <span>{{$errors->first('plain')}}</span>
    </div>
@elseif ($errors->any())
    <div {{ $attributes }}>
        <div class="font-medium text-red-600">{{ __('Whoops! Something went wrong.') }}</div>

        <ul class="mt-3 list-disc list-inside text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
